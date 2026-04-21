<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\SafeCache;
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

        $filterCategoryIds = SafeCache::remember('products:index:filter-category-ids:v1', now()->addMinutes(5), function (): array {
            return ProductCategory::query()
                ->where('is_active', true)
                ->orderBy('parent_id')
                ->orderBy('name')
                ->pluck('id')
                ->all();
        });

        $filterCategories = $filterCategoryIds === []
            ? collect()
            : ProductCategory::query()
                ->whereIn('id', $filterCategoryIds)
                ->with('parent:id,name')
                ->orderBy('parent_id')
                ->orderBy('name')
                ->get();

        $publicCategoryIds = SafeCache::remember('products:index:public-category-ids:v1', now()->addMinutes(5), function (): array {
            return ProductCategory::publicTreeQuery()
                ->pluck('id')
                ->all();
        });

        $categories = $publicCategoryIds === []
            ? collect()
            : ProductCategory::query()
                ->whereIn('id', $publicCategoryIds)
                ->withActiveProductsCount()
                ->with([
                    'children' => fn ($childrenQuery) => $childrenQuery
                        ->where('is_active', true)
                        ->whereHas('products', fn ($productsQuery) => $productsQuery->where('is_active', true))
                        ->withActiveProductsCount()
                        ->orderBy('name'),
                ])
                ->orderBy('name')
                ->get();

        return view('products.index', [
            'activeProductCount' => SafeCache::remember('products:index:active-count:v1', now()->addMinutes(5), fn (): int => Product::query()->where('is_active', true)->count()),
            'categories' => $categories,
            'filterCategories' => $filterCategories,
            'filters' => $filters,
        ]);
    }

    public function byCategory(ProductCategory $category): View
    {
        abort_if(! $category->is_active, 404);

        $categoryIds = SafeCache::remember("products:category:{$category->id}:visible-child-ids:v1", now()->addMinutes(5), function () use ($category): array {
            return $category->children()
                ->where('is_active', true)
                ->pluck('id')
                ->push($category->id)
                ->all();
        });

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
