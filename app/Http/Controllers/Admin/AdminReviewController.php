<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function reports(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        $reportsQuery = DB::table('items')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->select(
                'items.id',
                'items.type',
                'items.item_name',
                'items.description',
                'items.location',
                'items.date_lost_or_found',
                'items.photo_path',
                'items.status',
                'items.is_verified',
                'items.created_at',
                'users.name as student_name',
                'users.email as student_email',
                'categories.name as category_name',
            );

        if (($validated['status'] ?? 'pending') === 'pending') {
            $reportsQuery->where('items.is_verified', false)
                ->whereRaw('LOWER(items.status) != ?', ['rejected']);
        } elseif (($validated['status'] ?? null) === 'verified') {
            $reportsQuery->where('items.is_verified', true);
        } elseif (($validated['status'] ?? null) === 'rejected') {
            $reportsQuery->whereRaw('LOWER(items.status) = ?', ['rejected']);
        }

        $reports = $reportsQuery
            ->orderByDesc('items.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'selectedStatus' => $validated['status'] ?? 'pending',
            'pendingCount' => DB::table('items')->where('is_verified', false)->whereRaw('LOWER(status) != ?', ['rejected'])->count(),
            'verifiedCount' => DB::table('items')->where('is_verified', true)->count(),
            'rejectedCount' => DB::table('items')->whereRaw('LOWER(status) = ?', ['rejected'])->count(),
        ]);
    }

    public function verifyReport(int $item): RedirectResponse
    {
        DB::transaction(function () use ($item): void {
            $report = DB::table('items')->where('id', $item)->lockForUpdate()->first();

            abort_unless($report, 404);

            if ($report->is_verified || strtolower($report->status) === 'rejected') {
                throw ValidationException::withMessages(['report' => 'This report has already been reviewed.']);
            }

            DB::table('items')->where('id', $item)->update([
                'is_verified' => true,
                'status' => 'open',
                'updated_at' => now(),
            ]);

            $this->notify(
                (int) $report->user_id,
                'Report approved',
                'Your '.$report->type.' item report for "'.$report->item_name.'" has been verified and is now visible to students.',
                'report_review',
            );

            $this->logActivity('Verified item report', 'Verified report #'.$item.': '.$report->item_name.'.');
        });

        return redirect()->route('admin.reports.index')->with('success', 'Report verified. Students can now find it in Browse Items.');
    }

    public function rejectReport(Request $request, int $item): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($item, $validated): void {
            $report = DB::table('items')->where('id', $item)->lockForUpdate()->first();

            abort_unless($report, 404);

            if ($report->is_verified || strtolower($report->status) === 'rejected') {
                throw ValidationException::withMessages(['report' => 'This report has already been reviewed.']);
            }

            DB::table('items')->where('id', $item)->update([
                'is_verified' => false,
                'status' => 'rejected',
                'updated_at' => now(),
            ]);

            $this->notify(
                (int) $report->user_id,
                'Report not approved',
                'Your report for "'.$report->item_name.'" was not approved. Admin note: '.$validated['reason'],
                'report_review',
            );

            $this->logActivity('Rejected item report', 'Rejected report #'.$item.': '.$report->item_name.'.');
        });

        return redirect()->route('admin.reports.index')->with('success', 'Report rejected and the student was notified.');
    }

    public function claims(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected,all'],
        ]);

        $claimsQuery = DB::table('claims')
            ->join('items', 'items.id', '=', 'claims.item_id')
            ->join('users as claimants', 'claimants.id', '=', 'claims.claimant_id')
            ->join('users as reporters', 'reporters.id', '=', 'items.user_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->select(
                'claims.id',
                'claims.item_id',
                'claims.proof_description',
                'claims.status',
                'claims.remarks',
                'claims.created_at',
                'items.item_name',
                'items.photo_path',
                'items.location',
                'categories.name as category_name',
                'claimants.name as claimant_name',
                'claimants.email as claimant_email',
                'reporters.name as reporter_name',
            );

        if (($validated['status'] ?? 'pending') !== 'all') {
            $claimsQuery->whereRaw('LOWER(claims.status) = ?', [$validated['status'] ?? 'pending']);
        }

        $claims = $claimsQuery
            ->orderByDesc('claims.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.claims.index', [
            'claims' => $claims,
            'selectedStatus' => $validated['status'] ?? 'pending',
            'pendingCount' => DB::table('claims')->whereRaw('LOWER(status) = ?', ['pending'])->count(),
            'approvedCount' => DB::table('claims')->whereRaw('LOWER(status) = ?', ['approved'])->count(),
            'rejectedCount' => DB::table('claims')->whereRaw('LOWER(status) = ?', ['rejected'])->count(),
        ]);
    }

    public function approveClaim(Request $request, int $claim): RedirectResponse
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($claim, $validated): void {
            $claimRecord = DB::table('claims')->where('id', $claim)->lockForUpdate()->first();

            abort_unless($claimRecord, 404);

            $item = DB::table('items')->where('id', $claimRecord->item_id)->lockForUpdate()->first();

            if (strtolower($claimRecord->status) !== 'pending') {
                throw ValidationException::withMessages(['claim' => 'This claim has already been reviewed.']);
            }

            if (! $item->is_verified || strtolower($item->status) !== 'open') {
                throw ValidationException::withMessages(['claim' => 'This item is no longer open for claim approval.']);
            }

            $existingApprovedClaim = DB::table('claims')
                ->where('item_id', $item->id)
                ->where('id', '!=', $claim)
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->exists();

            if ($existingApprovedClaim) {
                throw ValidationException::withMessages(['claim' => 'Another claim for this item has already been approved.']);
            }

            $reviewedAt = now();

            DB::table('claims')->where('id', $claim)->update([
                'status' => 'approved',
                'remarks' => $validated['remarks'] ?? null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => $reviewedAt,
                'updated_at' => $reviewedAt,
            ]);

            DB::table('items')->where('id', $item->id)->update([
                'status' => 'claimed',
                'updated_at' => $reviewedAt,
            ]);

            $otherPendingClaims = DB::table('claims')
                ->where('item_id', $item->id)
                ->where('id', '!=', $claim)
                ->whereRaw('LOWER(status) = ?', ['pending'])
                ->get(['id', 'claimant_id']);

            if ($otherPendingClaims->isNotEmpty()) {
                DB::table('claims')
                    ->whereIn('id', $otherPendingClaims->pluck('id'))
                    ->update([
                        'status' => 'rejected',
                        'remarks' => 'Another claim for this item was approved.',
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => $reviewedAt,
                        'updated_at' => $reviewedAt,
                    ]);

                foreach ($otherPendingClaims->pluck('claimant_id')->unique() as $claimantId) {
                    $this->notify(
                        (int) $claimantId,
                        'Claim request not approved',
                        'Another student’s claim for "'.$item->item_name.'" was approved.',
                        'claim_review',
                    );
                }
            }

            $this->notify(
                (int) $claimRecord->claimant_id,
                'Claim request approved',
                'Your claim for "'.$item->item_name.'" was approved. Please coordinate with the Lost & Found office to retrieve it.',
                'claim_review',
            );

            $this->logActivity('Approved claim request', 'Approved claim #'.$claim.' for item '.$item->item_name.'.');
        });

        return redirect()->route('admin.claims.index')->with('success', 'Claim approved. The item is marked claimed and the student was notified.');
    }

    public function rejectClaim(Request $request, int $claim): RedirectResponse
    {
        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($claim, $validated): void {
            $claimRecord = DB::table('claims')->where('id', $claim)->lockForUpdate()->first();

            abort_unless($claimRecord, 404);

            if (strtolower($claimRecord->status) !== 'pending') {
                throw ValidationException::withMessages(['claim' => 'This claim has already been reviewed.']);
            }

            $item = DB::table('items')->where('id', $claimRecord->item_id)->first();
            $reviewedAt = now();

            DB::table('claims')->where('id', $claim)->update([
                'status' => 'rejected',
                'remarks' => $validated['remarks'],
                'reviewed_by' => Auth::id(),
                'reviewed_at' => $reviewedAt,
                'updated_at' => $reviewedAt,
            ]);

            $this->notify(
                (int) $claimRecord->claimant_id,
                'Claim request not approved',
                'Your claim for "'.$item->item_name.'" was not approved. Admin note: '.$validated['remarks'],
                'claim_review',
            );

            $this->logActivity('Rejected claim request', 'Rejected claim #'.$claim.' for item '.$item->item_name.'.');
        });

        return redirect()->route('admin.claims.index')->with('success', 'Claim rejected and the student was notified.');
    }

    private function notify(int $userId, string $title, string $message, string $type): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function logActivity(string $action, string $details): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
