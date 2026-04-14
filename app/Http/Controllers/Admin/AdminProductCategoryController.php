<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProductCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.product-categories.index', [
            'categories' => ProductCategory::query()->withCount('products')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.product-categories.form', [
            'category' => new ProductCategory(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ProductCategory::query()->create($this->validated($request));

        return redirect()->route('admin.product-categories.index')->with('status', 'Product category created.');
    }

    public function edit(ProductCategory $productCategory): View
    {
        return view('admin.product-categories.form', [
            'category' => $productCategory,
        ]);
    }

    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->update($this->validated($request, $productCategory));

        return redirect()->route('admin.product-categories.index')->with('status', 'Product category updated.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->delete();

        return back()->with('status', 'Product category deleted.');
    }

    private function validated(Request $request, ?ProductCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('product_categories', 'slug')->ignore($category?->id)],
            'tag' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
