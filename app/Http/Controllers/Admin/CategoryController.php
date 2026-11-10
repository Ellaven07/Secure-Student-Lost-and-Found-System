<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        // Count associated items without requiring a separate Item model.
        $categories = DB::table('categories')
            ->leftJoin('items', 'categories.id', '=', 'items.category_id')
            ->select(
                'categories.id',
                'categories.name',
                'categories.description',
                'categories.created_at',
                'categories.updated_at',
                DB::raw('COUNT(items.id) as items_count')
            )
            ->groupBy(
                'categories.id',
                'categories.name',
                'categories.description',
                'categories.created_at',
                'categories.updated_at'
            )
            ->orderBy('categories.name')
            ->get();

        $totalCategories = $categories->count();

        $categoriesWithItems = $categories
            ->where('items_count', '>', 0)
            ->count();

        $categoriesWithoutItems = $totalCategories - $categoriesWithItems;

        return view('admin.categories', compact(
            'categories',
            'totalCategories',
            'categoriesWithItems',
            'categoriesWithoutItems'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $category = Category::create($validated);

        $this->logActivity(
            'Created category',
            'Created category: '.$category->name.'.'
        );

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldName = $category->name;

        $category->update($validated);

        $this->logActivity(
            'Updated category',
            'Updated category '.$oldName.' to '.$category->name.'.'
        );

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $categoryName = $category->name;

        // The database foreign key sets category_id to null
        // on existing item reports when this category is deleted.
        $category->delete();

        $this->logActivity(
            'Deleted category',
            'Deleted category: '.$categoryName.'. Existing item reports were preserved.'
        );

        return redirect()
            ->route('admin.categories')
            ->with('success', 'Category deleted successfully. Existing item reports were preserved.');
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