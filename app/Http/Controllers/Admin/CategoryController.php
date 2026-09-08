<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(
        StoreCategoryRequest $request
    ): RedirectResponse {
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('categories', 'public')
            : null;

        Category::create([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
            'description' => $request->validated('description'),
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view(
            'admin.categories.edit',
            compact('category')
        );
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): RedirectResponse {
        $imagePath = $category->image;

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $imagePath = $request->file('image')
                ->store('categories', 'public');
        }

        $category->update([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
            'description' => $request->validated('description'),
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(
        Category $category
    ): RedirectResponse {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    'This category cannot be deleted because it contains products.'
                );
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}