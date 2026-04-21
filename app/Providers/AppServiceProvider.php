<?php

namespace App\Providers;

use App\Models\ProductCategory;
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
            $view->with('homeCategories', $publicCategories->take(5)->values());
        });
    }
}
