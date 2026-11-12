<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ItemReportController extends Controller
{
    public function createLost(): View
    {
        return $this->reportForm('lost');
    }

    public function createFound(): View
    {
        return $this->reportForm('found');
    }

    public function storeLost(Request $request, SupabaseStorageService $photoStorage): RedirectResponse
    {
        return $this->store($request, 'lost', $photoStorage);
    }

    public function storeFound(Request $request, SupabaseStorageService $photoStorage): RedirectResponse
    {
        return $this->store($request, 'found', $photoStorage);
    }

    public function history(Request $request): View
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
        ]);

        $reportsQuery = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->where('items.user_id', $request->user()->id)
            ->select('items.*', 'categories.name as category_name')
            ->addSelect([
                'claims_count' => DB::table('claims')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('claims.item_id', 'items.id'),
            ]);

        if (! empty($validated['category_id'])) {
            $reportsQuery->where('items.category_id', $validated['category_id']);
        }

        $reports = $reportsQuery
            ->latest('items.created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = DB::table('categories')->orderBy('name')->get(['id', 'name']);

        return view('student.reports.history', [
            'reports' => $reports,
            'categories' => $categories,
            'selectedCategoryId' => $validated['category_id'] ?? '',
        ]);
    }

    public function edit(Request $request, int $item): View
    {
        $report = $this->editableReport($item, (int) $request->user()->id);
        $categories = DB::table('categories')->orderBy('name')->get(['id', 'name']);

        return view('student.reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, int $item, SupabaseStorageService $photoStorage): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'date_lost_or_found' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo') && $photoStorage->missingItemConfiguration() !== []) {
            $missingSettings = implode(', ', $photoStorage->missingItemConfiguration());

            return back()->withInput()->withErrors([
                'photo' => "Item photo storage is missing server setting(s): {$missingSettings}. Configure Supabase Storage and restart the server.",
            ]);
        }

        $newPhotoPath = $request->file('photo')
            ? $photoStorage->uploadItemPhoto($request->file('photo'), $request->user()->id)
            : null;

        if ($request->hasFile('photo') && ! $newPhotoPath) {
            return back()->withInput()->withErrors([
                'photo' => 'The photo could not be saved. Please try again.',
            ]);
        }

        try {
            $oldPhotoPath = DB::transaction(function () use ($item, $request, $validated, $newPhotoPath): ?string {
                $report = DB::table('items')
                    ->where('id', $item)
                    ->where('user_id', $request->user()->id)
                    ->lockForUpdate()
                    ->first();

                abort_unless($report, 404);
                $this->ensureReportCanChange($report);

                $updates = [
                    'category_id' => $validated['category_id'] ?? null,
                    'item_name' => $validated['item_name'],
                    'description' => $validated['description'],
                    'location' => $validated['location'],
                    'date_lost_or_found' => $validated['date_lost_or_found'],
                    'status' => 'open',
                    'is_verified' => false,
                    'updated_at' => now(),
                ];

                if ($newPhotoPath) {
                    $updates['photo_path'] = $newPhotoPath;
                } elseif ($request->boolean('remove_photo')) {
                    $updates['photo_path'] = null;
                }

                DB::table('items')->where('id', $item)->update($updates);

                return $report->photo_path !== ($updates['photo_path'] ?? $report->photo_path)
                    ? $report->photo_path
                    : null;
            });
        } catch (Throwable $exception) {
            if ($newPhotoPath) {
                $photoStorage->delete($newPhotoPath);
            }

            throw $exception;
        }

        if ($oldPhotoPath) {
            $photoStorage->delete($oldPhotoPath);
        }

        return redirect()
            ->route('student.reports.history')
            ->with('success', 'Your report was updated and sent back for admin review.');
    }

    public function destroy(Request $request, int $item, SupabaseStorageService $photoStorage): RedirectResponse
    {
        $photoPath = DB::transaction(function () use ($item, $request): ?string {
            $report = DB::table('items')
                ->where('id', $item)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->first();

            abort_unless($report, 404);
            $this->ensureReportCanChange($report);

            DB::table('items')->where('id', $item)->delete();

            return $report->photo_path;
        });

        if ($photoPath) {
            $photoStorage->delete($photoPath);
        }

        return redirect()
            ->route('student.reports.history')
            ->with('success', 'Your report was deleted.');
    }

    private function editableReport(int $item, int $studentId): object
    {
        $report = DB::table('items')
            ->where('id', $item)
            ->where('user_id', $studentId)
            ->first();

        abort_unless($report, 404);
        $this->ensureReportCanChange($report);

        return $report;
    }

    private function ensureReportCanChange(object $report): void
    {
        if (in_array(strtolower($report->status), ['claimed', 'closed', 'resolved'], true)) {
            throw ValidationException::withMessages([
                'report' => 'This report can no longer be changed because it is closed.',
            ]);
        }

        $hasClaims = DB::table('claims')->where('item_id', $report->id)->exists();

        if ($hasClaims) {
            throw ValidationException::withMessages([
                'report' => 'This report can no longer be changed because it has claim requests.',
            ]);
        }
    }

    private function reportForm(string $type): View
    {
        $categories = DB::table('categories')->orderBy('name')->get(['id', 'name']);

        return view('student.reports.create', compact('categories', 'type'));
    }

    private function store(Request $request, string $type, SupabaseStorageService $photoStorage): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'date_lost_or_found' => ['required', 'date', 'before_or_equal:today'],
            'photo' => [$type === 'found' ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $missingSettings = $photoStorage->missingItemConfiguration();

        if ($request->hasFile('photo') && $missingSettings !== []) {
            $missingSettings = implode(', ', $missingSettings);

            return back()->withInput()->withErrors([
                'photo' => "Item photo storage is missing server setting(s): {$missingSettings}. Configure Supabase Storage and restart the server.",
            ]);
        }

        $photoPath = $request->file('photo')
            ? $photoStorage->uploadItemPhoto($request->file('photo'), $request->user()->id)
            : null;

        if ($request->hasFile('photo') && ! $photoPath) {
            return back()->withInput()->withErrors([
                'photo' => 'The photo could not be saved. Please try again.',
            ]);
        }

        DB::table('items')->insert([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'] ?? null,
            'type' => $type,
            'item_name' => $validated['item_name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'date_lost_or_found' => $validated['date_lost_or_found'],
            'photo_path' => $photoPath,
            'status' => 'open',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('student.reports.history')
            ->with('success', ucfirst($type).' item report submitted successfully.');
    }
}
