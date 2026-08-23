<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopProduct;
use App\Support\SafeCache;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $settings = SiteSettings::all();

        $heroSlides = [
            $settings['home_hero_image_1'] ?? 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1600&q=80',
            $settings['home_hero_image_2'] ?? 'https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=1600&q=80',
            $settings['home_hero_image_3'] ?? 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=1600&q=80',
            $settings['home_hero_image_4'] ?? 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1600&q=80',
            $settings['home_hero_image_5'] ?? 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1600&q=80',
        ];

        $categoryFallbackImages = [
            $settings['home_category_fallback_image_1'] ?? 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            $settings['home_category_fallback_image_2'] ?? 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
            $settings['home_category_fallback_image_3'] ?? 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
            $settings['home_category_fallback_image_4'] ?? 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
            $settings['home_category_fallback_image_5'] ?? 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
            $settings['home_category_fallback_image_6'] ?? 'https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=900&q=80',
        ];

        $homePromoImages = [
            $settings['home_promo_image_1'] ?? 'https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=900&q=80',
            $settings['home_promo_image_2'] ?? 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=900&q=80',
        ];

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

        return view('new-home', compact(
            'featuredProducts',
            'popularGiftItems',
            'homeCategories',
            'featuredShopProducts',
            'heroSlides',
            'categoryFallbackImages',
            'homePromoImages'
        ));
    }

    public function newHome(): \Illuminate\View\View
    {
        $featuredProductIds = SafeCache::remember('home:featured-product-ids:v1', now()->addMinutes(5), function (): array {
            return Product::query()
                ->featured()
                ->orderByDesc('view_count')
                ->limit(6)
                ->pluck('id')
                ->all();
        });

        $featuredProducts = $this->orderedProductsFromCachedIds($featuredProductIds);

        return view('new-home', compact('featuredProducts'));
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
