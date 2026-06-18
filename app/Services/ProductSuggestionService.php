<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInteraction;
use App\Models\ShopProduct;
use Illuminate\Support\Collection;
use Throwable;

class ProductSuggestionService
{
    const SESSION_KEY = 'pb_ph';

    const MAX_HISTORY = 40;

    // -------------------------------------------------------------------------
    // Record a product view into session (+ DB for authenticated users)
    // -------------------------------------------------------------------------
    public function record(string $type, int $productId, ?int $categoryId = null): void
    {
        try {
            $history = session(self::SESSION_KEY, []);

            // De-duplicate: remove existing entry for same product so it moves to front
            $history = array_values(array_filter(
                $history,
                fn ($h) => ! ($h['t'] === $type && $h['i'] === $productId)
            ));

            array_unshift($history, ['t' => $type, 'i' => $productId, 'c' => $categoryId]);

            session([self::SESSION_KEY => array_slice($history, 0, self::MAX_HISTORY)]);

            if (auth()->check()) {
                ProductInteraction::create([
                    'user_id'      => auth()->id(),
                    'product_type' => $type,
                    'product_id'   => $productId,
                    'category_id'  => $categoryId,
                ]);
            }
        } catch (Throwable) {
            // Never break the page request over suggestion tracking
        }
    }

    // -------------------------------------------------------------------------
    // Compute personalised suggestions
    // -------------------------------------------------------------------------
    public function getSuggestions(
        int $catalogLimit = 4,
        int $shopLimit = 2,
        ?int $excludeCatalogId = null,
        ?int $excludeShopId = null,
    ): array {
        try {
            $history = $this->getHistory();

            $viewedCatalogIds = collect($history)->where('t', 'catalog')->pluck('i')->unique()->values()->all();
            $viewedShopIds    = collect($history)->where('t', 'shop')->pluck('i')->unique()->values()->all();
            $recentCatIds     = collect($history)->whereNotNull('c')->pluck('c')->unique()->take(6)->values()->all();
            $hasShopIntent    = count($viewedShopIds) > 0;
            $hasHistory       = count($history) > 0;

            $catalog = $this->fetchCatalog($viewedCatalogIds, $recentCatIds, $excludeCatalogId, $catalogLimit);
            $shop    = $this->fetchShop($viewedShopIds, $hasShopIntent, $excludeShopId, $shopLimit);

            return [
                'catalog'      => $catalog,
                'shop'         => $shop,
                'has_history'  => $hasHistory,
                'personalized' => $hasHistory && (! empty($recentCatIds) || $hasShopIntent),
            ];
        } catch (Throwable) {
            return ['catalog' => collect(), 'shop' => collect(), 'has_history' => false, 'personalized' => false];
        }
    }

    // -------------------------------------------------------------------------
    // Merge session history with DB history for authenticated users
    // -------------------------------------------------------------------------
    private function getHistory(): array
    {
        $sessionHistory = session(self::SESSION_KEY, []);

        if (auth()->check() && count($sessionHistory) < 5) {
            try {
                $dbHistory = ProductInteraction::query()
                    ->where('user_id', auth()->id())
                    ->where('created_at', '>=', now()->subDays(7))
                    ->latest('created_at')
                    ->limit(40)
                    ->get()
                    ->map(fn ($r) => ['t' => $r->product_type, 'i' => $r->product_id, 'c' => $r->category_id])
                    ->all();

                $combined = array_merge($sessionHistory, $dbHistory);
                $seen     = [];

                return array_values(array_filter($combined, function ($item) use (&$seen) {
                    $key = $item['t'].':'.$item['i'];
                    if (in_array($key, $seen, true)) {
                        return false;
                    }
                    $seen[] = $key;

                    return true;
                }));
            } catch (Throwable) {
                return $sessionHistory;
            }
        }

        return $sessionHistory;
    }

    // -------------------------------------------------------------------------
    // Fetch catalog (print) product suggestions
    // -------------------------------------------------------------------------
    private function fetchCatalog(array $viewedIds, array $categoryIds, ?int $excludeId, int $limit): Collection
    {
        $query = Product::query()
            ->where('is_active', true)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->when($viewedIds, fn ($q) => $q->whereNotIn('id', $viewedIds));

        if (empty($categoryIds)) {
            return $query
                ->orderByDesc('is_featured')
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get();
        }

        // Prioritise products whose category was recently browsed
        $catList = implode(',', array_map('intval', $categoryIds));

        return $query
            ->orderByRaw("CASE WHEN product_category_id IN ({$catList}) THEN 0 ELSE 1 END")
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Fetch shop product suggestions
    // -------------------------------------------------------------------------
    private function fetchShop(array $viewedIds, bool $hasShopIntent, ?int $excludeId, int $limit): Collection
    {
        $query = ShopProduct::query()
            ->active()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->when($viewedIds, fn ($q) => $q->whereNotIn('id', $viewedIds));

        if (! $hasShopIntent) {
            // No shop browsing history — only show featured items
            return $query->featured()->orderByDesc('view_count')->limit($limit)->get();
        }

        // User has shown shop intent — surface best available items
        return $query
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN 0 ELSE 1 END')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get();
    }
}
