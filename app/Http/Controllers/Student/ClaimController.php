<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClaimController extends Controller
{
    public function create(Request $request, int $item): View
    {
        $report = $this->claimableItem($item, $request->user()->id);

        return view('student.claims.create', ['item' => $report]);
    }

    public function store(Request $request, int $item): RedirectResponse
    {
        $validated = $request->validate([
            'proof_description' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        DB::transaction(function () use ($item, $request, $validated): void {
            $report = DB::table('items')
                ->where('id', $item)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $report
                    && strtolower($report->type) === 'found'
                    && strtolower($report->status) === 'open'
                    && $report->is_verified
                    && (int) $report->user_id !== (int) $request->user()->id,
                404,
            );

            $hasApprovedClaim = DB::table('claims')
                ->where('item_id', $item)
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->exists();

            $hasExistingClaim = DB::table('claims')
                ->where('item_id', $item)
                ->where('claimant_id', $request->user()->id)
                ->whereIn(DB::raw('LOWER(status)'), ['pending', 'approved'])
                ->exists();

            abort_unless(! $hasApprovedClaim && ! $hasExistingClaim, 404);

            DB::table('claims')->insert([
                'item_id' => $report->id,
                'claimant_id' => $request->user()->id,
                'proof_description' => $validated['proof_description'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('student.claims.index')
            ->with('success', 'Your claim request has been submitted for review.');
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected'],
        ]);

        $claimsQuery = DB::table('claims')
            ->join('items', 'items.id', '=', 'claims.item_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->where('claims.claimant_id', $request->user()->id)
            ->select(
                'claims.id',
                'claims.item_id',
                'claims.proof_description',
                'claims.status',
                'claims.remarks',
                'claims.reviewed_at',
                'claims.created_at',
                'items.item_name',
                'items.photo_path',
                'items.location',
                'categories.name as category_name',
            );

        if (! empty($validated['status'])) {
            $claimsQuery->whereRaw('LOWER(claims.status) = ?', [$validated['status']]);
        }

        $claims = $claimsQuery
            ->orderByDesc('claims.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('student.claims.index', [
            'claims' => $claims,
            'selectedStatus' => $validated['status'] ?? '',
        ]);
    }

    private function claimableItem(int $itemId, int $studentId): object
    {
        $item = DB::table('items')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->where('items.id', $itemId)
            ->whereRaw('LOWER(items.type) = ?', ['found'])
            ->whereRaw('LOWER(items.status) = ?', ['open'])
            ->where('items.is_verified', true)
            ->where('items.user_id', '!=', $studentId)
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('claims')
                    ->whereColumn('claims.item_id', 'items.id')
                    ->whereRaw('LOWER(claims.status) = ?', ['approved']);
            })
            ->select('items.*', 'categories.name as category_name')
            ->first();

        if (! $item) {
            throw new NotFoundHttpException;
        }

        $hasExistingClaim = DB::table('claims')
            ->where('item_id', $itemId)
            ->where('claimant_id', $studentId)
            ->whereIn(DB::raw('LOWER(status)'), ['pending', 'approved'])
            ->exists();

        if ($hasExistingClaim) {
            throw new NotFoundHttpException;
        }

        return $item;
    }
}
