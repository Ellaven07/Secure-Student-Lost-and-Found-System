<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
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

    public function activityLogs(Request $request): View
    {
        $filters = $request->validate([
            'user' => ['nullable', 'string', 'max:120'],
            'action' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:admin,student,deleted'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $logsQuery = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->select(
                'activity_logs.id',
                'activity_logs.user_id',
                'activity_logs.action',
                'activity_logs.details',
                'activity_logs.ip_address',
                'activity_logs.created_at',
                'users.name as user_name',
                'users.role as user_role',
            );

        if (! empty($filters['user'])) {
            $logsQuery->where(function ($query) use ($filters): void {
                $query->where('users.name', 'like', '%'.$filters['user'].'%')
                    ->orWhere('users.email', 'like', '%'.$filters['user'].'%');
            });
        }

        if (! empty($filters['action'])) {
            $logsQuery->where('activity_logs.action', 'like', '%'.$filters['action'].'%');
        }

        if (($filters['role'] ?? null) === 'deleted') {
            $logsQuery->whereNull('activity_logs.user_id');
        } elseif (! empty($filters['role'])) {
            $logsQuery->where('users.role', $filters['role']);
        }

        if (! empty($filters['start_date'])) {
            $logsQuery->whereDate('activity_logs.created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $logsQuery->whereDate('activity_logs.created_at', '<=', $filters['end_date']);
        }

        return view('admin.activity-logs.index', [
            'logs' => $logsQuery->orderByDesc('activity_logs.created_at')->paginate(20)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function generateReports(Request $request): View
    {
        $filters = $this->validatedReportFilters($request);
        $items = $this->filteredItemReports($filters);

        $summary = [
            'lost' => (clone $items)->whereRaw('LOWER(items.type) = ?', ['lost'])->count(),
            'found' => (clone $items)->whereRaw('LOWER(items.type) = ?', ['found'])->count(),
            'approved' => (clone $items)->where('items.is_verified', true)->count(),
            'rejected' => (clone $items)->whereRaw('LOWER(items.status) = ?', ['rejected'])->count(),
            'pending' => (clone $items)->where('items.is_verified', false)->whereRaw('LOWER(items.status) != ?', ['rejected'])->count(),
            'claimed' => (clone $items)->whereRaw('LOWER(items.status) = ?', ['claimed'])->count(),
            'returned' => (clone $items)->whereRaw('LOWER(items.status) = ?', ['returned'])->count(),
            'claims' => DB::table('claims')->when($filters['start_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))->count(),
            'approved_claims' => DB::table('claims')->whereRaw('LOWER(status) = ?', ['approved'])
                ->when($filters['start_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))->count(),
            'rejected_claims' => DB::table('claims')->whereRaw('LOWER(status) = ?', ['rejected'])
                ->when($filters['start_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))->count(),
        ];

        $reportQuery = $this->filteredItemReports($filters)
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->select('items.id', 'items.item_name', 'items.type', 'items.status', 'items.is_verified', 'items.location', 'items.created_at', 'categories.name as category_name', 'users.name as reporter_name', 'users.department');

        if (in_array($filters['report_type'] ?? '', ['lost', 'found'], true)) {
            $reportQuery->whereRaw('LOWER(items.type) = ?', [$filters['report_type']]);
        }

        return view('admin.generate-reports.index', [
            'filters' => $filters,
            'summary' => $summary,
            'reports' => $reportQuery->orderByDesc('items.created_at')->limit(500)->get(),
            'categoryStats' => (clone $items)->leftJoin('categories', 'categories.id', '=', 'items.category_id')
                ->selectRaw("COALESCE(categories.name, 'Uncategorized') AS label, COUNT(items.id) AS total")
                ->groupBy('categories.name')->orderByDesc('total')->limit(10)->get(),
            'categories' => DB::table('categories')->orderBy('name')->get(['id', 'name']),
            'departments' => DB::table('users')->whereNotNull('department')->where('department', '!=', '')
                ->distinct()->orderBy('department')->pluck('department'),
            'departmentStats' => (clone $items)->join('users', 'users.id', '=', 'items.user_id')
                ->selectRaw("COALESCE(users.department, 'Unspecified') AS label, COUNT(items.id) AS total")
                ->groupBy('users.department')->orderByDesc('total')->limit(10)->get(),
            'generatedAt' => now(),
            'adminName' => $request->user()->name,
        ]);
    }

    public function exportGeneratedReports(Request $request): StreamedResponse
    {
        $filters = $this->validatedReportFilters($request);
        $reports = $this->filteredItemReports($filters)
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->select('items.item_name', 'items.type', 'items.status', 'items.is_verified', 'items.location', 'items.created_at', 'categories.name as category_name', 'users.name as reporter_name', 'users.department')
            ->when(in_array($filters['report_type'] ?? '', ['lost', 'found'], true), fn (Builder $query) => $query->whereRaw('LOWER(items.type) = ?', [$filters['report_type']]))
            ->orderByDesc('items.created_at')->get();

        return response()->streamDownload(function () use ($reports): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Item', 'Type', 'Category', 'Reporter', 'Department', 'Location', 'Approval', 'Item Status', 'Submitted At']);

            foreach ($reports as $report) {
                fputcsv($output, [
                    $this->csvCell($report->item_name), ucfirst(strtolower($report->type)),
                    $this->csvCell($report->category_name ?? 'Uncategorized'), $this->csvCell($report->reporter_name),
                    $this->csvCell($report->department ?? 'Unspecified'), $this->csvCell($report->location),
                    $report->is_verified ? 'Approved' : 'Pending', $report->status, $report->created_at,
                ]);
            }

            fclose($output);
        }, 'admin-reports.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array<string, string> */
    private function validatedReportFilters(Request $request): array
    {
        return $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'report_type' => ['nullable', 'in:all,lost,found,category,department'],
            'department' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'min:1'],
            'approval_status' => ['nullable', 'in:pending,approved,rejected'],
            'item_status' => ['nullable', 'in:open,claimed,returned,rejected'],
        ]);
    }

    private function filteredItemReports(array $filters): Builder
    {
        return DB::table('items')
            ->when($filters['start_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('items.created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn (Builder $query, string $date) => $query->whereDate('items.created_at', '<=', $date))
            ->when($filters['department'] ?? null, function (Builder $query, string $department): void {
                $query->whereIn('items.user_id', DB::table('users')->select('id')->where('department', $department));
            })
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('items.category_id', $category))
            ->when(($filters['approval_status'] ?? null) === 'approved', fn (Builder $query) => $query->where('items.is_verified', true))
            ->when(($filters['approval_status'] ?? null) === 'pending', fn (Builder $query) => $query->where('items.is_verified', false)->whereRaw('LOWER(items.status) != ?', ['rejected']))
            ->when(($filters['approval_status'] ?? null) === 'rejected', fn (Builder $query) => $query->whereRaw('LOWER(items.status) = ?', ['rejected']))
            ->when($filters['item_status'] ?? null, fn (Builder $query, string $status) => $query->whereRaw('LOWER(items.status) = ?', [$status]));
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
