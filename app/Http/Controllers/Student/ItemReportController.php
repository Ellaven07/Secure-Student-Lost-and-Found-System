<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
            ->select('items.*', 'categories.name as category_name');

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
