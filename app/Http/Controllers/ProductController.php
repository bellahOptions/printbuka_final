<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\SafeCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('shop.index', $request->only(['search', 'sort']), 301);
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
