<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category' => trim((string) $request->query('category', '')),
            'sort' => trim((string) $request->query('sort', 'name_asc')),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
        ];

        $filterCategories = ProductCategory::query()
            ->where('is_active', true)
            ->with('parent:id,name')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::publicTreeQuery()->get();

        return view('products.index', [
            'activeProductCount' => Product::query()->where('is_active', true)->count(),
            'categories' => $categories,
            'filterCategories' => $filterCategories,
            'filters' => $filters,
        ]);
    }

    public function byCategory(ProductCategory $category): View
    {
        abort_if(! $category->is_active, 404);

        $categoryIds = $category->children()
            ->where('is_active', true)
            ->pluck('id')
            ->push($category->id)
            ->all();

        $products = Product::query()
            ->whereIn('product_category_id', $categoryIds)
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        abort_if(! $product->is_active, 404);

        $product->increment('view_count');
        $product->refresh();

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
