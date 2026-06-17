<?php

namespace App\Providers;

use App\Models\ProductCategory;
use App\Models\ShopProduct;
use App\Support\SafeCache;
use App\Support\SiteSettings;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        VerifyEmail::createUrlUsing(function (object $notifiable): string {
            $relativeSignedUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                absolute: false
            );

            return URL::to($relativeSignedUrl);
        });

        View::share('siteSettings', SiteSettings::all());

        View::composer(['layouts.guest.nav', 'layouts.guest.footer', 'welcome'], function ($view): void {
            $cachedCategories = SafeCache::remember('public:category-tree:v1', now()->addMinutes(5), function (): array {
                return ProductCategory::publicTreeQuery()
                    ->limit(6)
                    ->get()
                    ->map(function (ProductCategory $category): array {
                        return [
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'tag' => $category->tag,
                            'description' => $category->description,
                            'image_url' => $category->imageUrl(),
                            'children' => $category->children
                                ->map(fn (ProductCategory $child): array => [
                                    'name' => $child->name,
                                    'slug' => $child->slug,
                                    'active_products_count' => (int) ($child->active_products_count ?? 0),
                                ])
                                ->values()
                                ->all(),
                        ];
                    })
                    ->values()
                    ->all();
            });

            $publicCategories = collect($cachedCategories)
                ->map(function (array $category): array {
                    $category['children'] = collect($category['children'] ?? []);

                    return $category;
                })
                ->values();

            $view->with('menuCategories', $publicCategories->take(6)->values());

            // 3 shop products for the nav mega-menu "Shop" strip
            $shopNavIds = SafeCache::remember('shop:nav-ids:v1', now()->addMinutes(5), function (): array {
                $ids = ShopProduct::query()->active()->featured()->orderByDesc('view_count')->limit(3)->pluck('id')->all();

                return $ids ?: ShopProduct::query()->active()->orderByDesc('view_count')->limit(3)->pluck('id')->all();
            });

            $shopNavProducts = $shopNavIds === []
                ? collect()
                : ShopProduct::query()->whereIn('id', $shopNavIds)->get();

            $view->with('shopNavProducts', $shopNavProducts);
        });

        // Popup: share most-viewed shop products as a JSON-serialisable array to every customer page
        View::composer('layouts.theme', function ($view): void {
            $popupIds = SafeCache::remember('shop:popup-ids:v1', now()->addMinutes(5), function (): array {
                return ShopProduct::query()->active()->orderByDesc('view_count')->limit(6)->pluck('id')->all();
            });

            $popupProducts = $popupIds === []
                ? []
                : ShopProduct::query()
                    ->whereIn('id', $popupIds)
                    ->get()
                    ->map(fn (ShopProduct $p): array => [
                        'name'              => $p->name,
                        'slug'              => $p->slug,
                        'short_description' => $p->short_description,
                        'image'             => $p->featuredImageUrl() ?: asset('img/product-placeholder.svg'),
                        'price'             => number_format((float) $p->price, 0),
                        'current_price'     => number_format($p->currentPrice(), 0),
                        'is_on_sale'        => $p->isOnSale(),
                        'url'               => route('shop.show', $p->slug),
                        'cart_url'          => route('shop.show', $p->slug),
                    ])
                    ->all();

            $view->with('popupShopProducts', $popupProducts);
        });
    }
}
