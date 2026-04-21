<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\SafeCache;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProductIds = SafeCache::remember('home:featured-product-ids:v1', now()->addMinutes(5), function (): array {
            return Product::query()
                ->featured()
                ->orderByDesc('view_count')
                ->limit(8)
                ->pluck('id')
                ->all();
        });

        $popularGiftItemIds = SafeCache::remember('home:popular-gift-item-ids:v1', now()->addMinutes(5), function (): array {
            return Product::query()
                ->where('is_active', true)
                ->where(function ($query): void {
                    $query
                        ->where('name', 'like', '%gift%')
                        ->orWhere('name', 'like', '%mug%')
                        ->orWhere('name', 'like', '%shirt%')
                        ->orWhere('name', 'like', '%tote%')
                        ->orWhere('description', 'like', '%gift%')
                        ->orWhere('short_description', 'like', '%gift%');
                })
                ->orderByDesc('view_count')
                ->limit(6)
                ->pluck('id')
                ->all();
        });

        $featuredProducts = $this->orderedProductsFromCachedIds($featuredProductIds);
        $popularGiftItems = $this->orderedProductsFromCachedIds($popularGiftItemIds);

        return view('welcome', compact('featuredProducts', 'popularGiftItems'));
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function orderedProductsFromCachedIds(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        $idOrder = array_flip($ids);

        return Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn (Product $product): int => $idOrder[$product->id] ?? PHP_INT_MAX)
            ->values();
    }
}
