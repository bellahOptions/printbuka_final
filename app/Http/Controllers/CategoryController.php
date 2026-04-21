<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Support\SafeCache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $publicCategoryIds = SafeCache::remember('categories:index:public-category-ids:v1', now()->addMinutes(5), function (): array {
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

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }
}
