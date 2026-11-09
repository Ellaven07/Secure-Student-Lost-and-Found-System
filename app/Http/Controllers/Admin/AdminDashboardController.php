<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $approvedReports = DB::table('items')->where('is_verified', true)->count();
        $awaitingReports = DB::table('items')->where('is_verified', false)->count();

        $lostFoundChart = $this->formatBars(
            DB::table('items')
                ->selectRaw('LOWER(type) AS label, COUNT(*) AS total')
                ->groupByRaw('LOWER(type)')
                ->orderBy('label')
                ->get()
        );

        $approvalChart = $this->formatBars(collect([
            (object) ['label' => 'Approved', 'total' => $approvedReports],
            (object) ['label' => 'Awaiting approval', 'total' => $awaitingReports],
        ]));

        $claimStatusChart = $this->formatBars(
            DB::table('claims')
                ->selectRaw('LOWER(status) AS label, COUNT(*) AS total')
                ->groupByRaw('LOWER(status)')
                ->orderBy('label')
                ->get()
        );

        $categoryChart = $this->formatBars(
            DB::table('items')
                ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
                ->selectRaw("COALESCE(categories.name, 'Uncategorized') AS label, COUNT(items.id) AS total")
                ->groupBy('categories.name')
                ->orderByDesc('total')
                ->get()
        );

        $monthStart = now()->startOfMonth()->subMonths(5);
        $monthExpression = match (DB::connection()->getDriverName()) {
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'sqlite' => "strftime('%Y-%m', created_at)",
            default => null,
        };

        if ($monthExpression !== null) {
            $monthCounts = DB::table('items')
                ->where('created_at', '>=', $monthStart)
                ->selectRaw("{$monthExpression} AS month_key, COUNT(*) AS total")
                ->groupByRaw($monthExpression)
                ->pluck('total', 'month_key');
        } else {
            $monthCounts = DB::table('items')
                ->where('created_at', '>=', $monthStart)
                ->select('created_at')
                ->get()
                ->groupBy(fn (object $item): string => Carbon::parse($item->created_at)->format('Y-m'))
                ->map(fn (Collection $items): int => $items->count());
        }

        $monthlyReportChart = $this->formatBars(collect(range(0, 5))->map(function (int $offset) use ($monthStart, $monthCounts): object {
            $month = $monthStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');

            return (object) [
                'label' => $month->format('M'),
                'total' => (int) ($monthCounts[$key] ?? 0),
            ];
        }));

        $registrations = DB::table('users')
            ->where('role', 'student')
            ->select('name', 'email', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (object $student): array => $this->makeActivity(
                'Student registration',
                "{$student->name} ({$student->email}) registered",
                $student->created_at
            ));

        $reports = DB::table('items')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->select('items.type', 'items.item_name', 'users.name as student_name', 'items.created_at')
            ->orderByDesc('items.created_at')
            ->limit(5)
            ->get()
            ->map(fn (object $report): array => $this->makeActivity(
                ucfirst(strtolower($report->type)).' report submitted',
                "{$report->item_name} submitted by {$report->student_name}",
                $report->created_at
            ));

        $claims = DB::table('claims')
            ->join('users', 'users.id', '=', 'claims.claimant_id')
            ->join('items', 'items.id', '=', 'claims.item_id')
            ->select('users.name as student_name', 'items.item_name', 'claims.status', 'claims.created_at')
            ->orderByDesc('claims.created_at')
            ->limit(5)
            ->get()
            ->map(fn (object $claim): array => $this->makeActivity(
                'Claim request '.$claim->status,
                "{$claim->student_name} submitted a claim for {$claim->item_name}",
                $claim->created_at
            ));

        $latestReviews = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->where(function ($query): void {
                $query->whereRaw('LOWER(activity_logs.action) LIKE ?', ['%approv%'])
                    ->orWhereRaw('LOWER(activity_logs.action) LIKE ?', ['%reject%']);
            })
            ->select('activity_logs.action', 'activity_logs.details', 'users.name as admin_name', 'activity_logs.created_at')
            ->orderByDesc('activity_logs.created_at')
            ->limit(6)
            ->get()
            ->map(function (object $review): array {
                $action = strtolower($review->action);
                $recordType = str_contains($action, 'claim') ? 'Claim' : 'Report';
                $decision = str_contains($action, 'reject') ? 'rejected' : 'approved';

                return $this->makeActivity(
                    "{$recordType} {$decision}",
                    $review->details ?: ($review->admin_name ? "Reviewed by {$review->admin_name}" : $review->action),
                    $review->created_at
                );
            });

        $recentActivities = $registrations
            ->concat($reports)
            ->concat($claims)
            ->concat($latestReviews)
            ->sortByDesc(fn (array $activity): int => $activity['created_at']->timestamp)
            ->take(12)
            ->values();

        $pendingReports = DB::table('items')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->where('items.is_verified', false)
            ->select('items.id', 'items.type', 'items.item_name', 'users.name as student_name', 'items.created_at')
            ->orderByDesc('items.created_at')
            ->limit(5)
            ->get();

        $pendingClaims = DB::table('claims')
            ->join('users', 'users.id', '=', 'claims.claimant_id')
            ->join('items', 'items.id', '=', 'claims.item_id')
            ->where('claims.status', 'pending')
            ->select('claims.id', 'users.name as student_name', 'items.item_name', 'claims.created_at')
            ->orderByDesc('claims.created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'lostFoundChart' => $lostFoundChart,
            'approvalChart' => $approvalChart,
            'claimStatusChart' => $claimStatusChart,
            'monthlyReportChart' => $monthlyReportChart,
            'categoryChart' => $categoryChart,
            'recentActivities' => $recentActivities,
            'latestReviews' => $latestReviews,
            'pendingReportCount' => $awaitingReports,
            'pendingClaimCount' => DB::table('claims')->where('status', 'pending')->count(),
            'pendingReports' => $pendingReports,
            'pendingClaims' => $pendingClaims,
        ]);
    }

    public function export(): StreamedResponse
    {
        $reports = DB::table('items')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->select('items.type', 'items.item_name', 'categories.name as category', 'items.status', 'items.is_verified', 'items.created_at')
            ->orderByDesc('items.created_at')
            ->get();

        return response()->streamDownload(function () use ($reports): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Type', 'Item', 'Category', 'Status', 'Approval', 'Submitted At']);

            foreach ($reports as $report) {
                fputcsv($output, [
                    ucfirst(strtolower($report->type)),
                    $this->csvCell($report->item_name),
                    $this->csvCell($report->category ?? 'Uncategorized'),
                    $report->status,
                    $report->is_verified ? 'Approved' : 'Awaiting approval',
                    $report->created_at,
                ]);
            }

            fclose($output);
        }, 'lost-found-reports.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array<int, array{label: string, total: int, width: int}>
     */
    private function formatBars(Collection $rows): array
    {
        $maximum = max(1, (int) $rows->max(fn (object $row): int => (int) $row->total));

        return $rows->map(fn (object $row): array => [
            'label' => (string) $row->label,
            'total' => (int) $row->total,
            'width' => (int) round(((int) $row->total / $maximum) * 100),
        ])->all();
    }

    /**
     * @return array{type: string, description: string, created_at: Carbon}
     */
    private function makeActivity(string $type, string $description, mixed $createdAt): array
    {
        return [
            'type' => $type,
            'description' => $description,
            'created_at' => Carbon::parse($createdAt),
        ];
    }

    private function csvCell(?string $value): string
    {
        if ($value !== null && preg_match('/^[=+@\-\t\r]/', $value) === 1) {
            return "'{$value}";
        }

        return $value ?? '';
    }
}
