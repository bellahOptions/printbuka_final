<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopProduct;
use App\Support\SafeCache;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
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

        $homeCategoryIds = SafeCache::remember('home:category-ids:v1', now()->addMinutes(5), function (): array {
            return ProductCategory::homeCategories(6)->pluck('id')->all();
        });

        $homeCategories = $homeCategoryIds === [] ? collect() : ProductCategory::query()
            ->whereIn('id', $homeCategoryIds)
            ->withActiveProductsCount()
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        $featuredShopIds = SafeCache::remember('shop:home-featured-ids:v1', now()->addMinutes(5), function (): array {
            $ids = ShopProduct::query()->active()->featured()->orderByDesc('view_count')->limit(4)->pluck('id')->all();

            return $ids ?: ShopProduct::query()->active()->orderByDesc('view_count')->limit(4)->pluck('id')->all();
        });

        $featuredShopProducts = $featuredShopIds === []
            ? collect()
            : ShopProduct::query()->whereIn('id', $featuredShopIds)->get()->sortBy(fn ($p) => array_search($p->id, $featuredShopIds))->values();

        return Inertia::render('Home', [
            'shopProducts' => $featuredShopProducts->map(fn (ShopProduct $p) => $this->shopProductProps($p))->values(),
        ]);
    }

    private function shopProductProps(ShopProduct $p): array
    {
        return [
            'id'       => $p->id,
            'proImg'   => $p->featuredImageUrl() ?? '/img/product-placeholder.svg',
            'title'    => $p->name,
            'slug'     => $p->slug,
            'price'    => number_format($p->currentPrice(), 2, '.', ''),
            'delPrice' => $p->isOnSale() ? number_format((float) $p->price, 2, '.', '') : null,
            'brand'    => 'Printbuka',
        ];
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
