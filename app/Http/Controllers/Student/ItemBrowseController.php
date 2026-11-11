<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:lost,found'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $itemsQuery = DB::table('items')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->where('items.is_verified', true)
            ->whereIn(DB::raw('LOWER(items.status)'), ['open'])
            ->select(
                'items.id',
                'items.type',
                'items.item_name',
                'items.description',
                'items.location',
                'items.date_lost_or_found',
                'items.photo_path',
                'items.user_id',
                'categories.name as category_name',
                'users.name as reporter_name',
            );

        if (! empty($validated['search'])) {
            $search = '%'.$validated['search'].'%';
            $itemsQuery->where(function ($query) use ($search): void {
                $lowerSearch = mb_strtolower($search);
                $query->whereRaw('LOWER(items.item_name) LIKE ?', [$lowerSearch])
                    ->orWhereRaw('LOWER(items.description) LIKE ?', [$lowerSearch])
                    ->orWhereRaw('LOWER(items.location) LIKE ?', [$lowerSearch])
                    ->orWhereRaw('LOWER(categories.name) LIKE ?', [$lowerSearch]);
            });
        }

        if (! empty($validated['type'])) {
            $itemsQuery->whereRaw('LOWER(items.type) = ?', [$validated['type']]);
        }

        if (! empty($validated['category_id'])) {
            $itemsQuery->where('items.category_id', $validated['category_id']);
        }

        if ($request->user()) {
            $itemsQuery->where(function ($query): void {
                $query->whereRaw('LOWER(items.type) != ?', ['found'])
                    ->orWhereNotExists(function ($claims): void {
                        $claims->selectRaw('1')
                            ->from('claims')
                            ->whereColumn('claims.item_id', 'items.id')
                            ->whereRaw('LOWER(claims.status) = ?', ['approved']);
                    });
            });
        }

        $items = $itemsQuery
            ->orderByDesc('items.created_at')
            ->paginate(12)
            ->withQueryString();

        $categories = DB::table('categories')->orderBy('name')->get(['id', 'name']);

        return view('student.items.index', [
            'items' => $items,
            'categories' => $categories,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'type' => $validated['type'] ?? '',
                'category_id' => $validated['category_id'] ?? '',
            ],
        ]);
    }

    public function show(Request $request, int $item): View
    {
        $user = $request->user();

        $report = DB::table('items')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->where('items.id', $item)
            ->where('items.is_verified', true)
            ->select(
                'items.*',
                'users.name as reporter_name',
                'categories.name as category_name',
            )
            ->first();

        if (! $report) {
            throw new NotFoundHttpException;
        }

        $hasApprovedClaim = DB::table('claims')
            ->where('item_id', $report->id)
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->exists();

        $hasExistingClaim = $user->isStudent()
            && DB::table('claims')
                ->where('item_id', $report->id)
                ->where('claimant_id', $user->id)
                ->whereIn(DB::raw('LOWER(status)'), ['pending', 'approved'])
                ->exists();

        return view('student.items.show', [
            'item' => $report,
            'isVisitor' => $user->isVisitor(),
            'canClaim' => $user->isStudent()
                && strtolower($report->type) === 'found'
                && strtolower($report->status) === 'open'
                && (int) $report->user_id !== (int) $user->id
                && ! $hasApprovedClaim
                && ! $hasExistingClaim,
            'hasExistingClaim' => $hasExistingClaim,
        ]);
    }
}
