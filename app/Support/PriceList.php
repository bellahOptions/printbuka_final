<?php

namespace App\Support;

use App\Models\PriceListItem;
use App\Models\Product;
use Illuminate\Support\Collection;

class PriceList
{
    /**
     * Site-wide default option list for a component group (size/material/finish/density/delivery),
     * used as a fallback when a product has no price overrides of its own. Products keep their
     * own size/material/finish/density/delivery price options on the products table itself.
     *
     * @return array<int, array{label: string, price: float}>
     */
    public static function productDefaultOptions(string $componentGroup): array
    {
        return self::optionRows('product', $componentGroup)->all();
    }

    public static function serviceBase(string $slug): ?float
    {
        $row = PriceListItem::query()
            ->where('category', 'service')
            ->where('service_slug', $slug)
            ->whereNull('component_group')
            ->where('is_active', true)
            ->first();

        return $row ? (float) $row->price : null;
    }

    public static function serviceFee(string $slug, string $feeKey): float
    {
        $row = PriceListItem::query()
            ->where('category', 'service')
            ->where('service_slug', $slug)
            ->where('component_group', 'fee')
            ->where('component_key', $feeKey)
            ->where('is_active', true)
            ->first();

        return $row ? (float) $row->price : 0.0;
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    public static function serviceOptions(string $slug, string $componentGroup): array
    {
        return self::optionRows('service', $componentGroup, serviceSlug: $slug)->all();
    }

    public static function surcharge(string $key): ?float
    {
        $row = PriceListItem::query()
            ->where('category', 'surcharge')
            ->where('component_key', $key)
            ->where('is_active', true)
            ->first();

        return $row ? (float) $row->price : null;
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    private static function optionRows(string $category, string $componentGroup, ?string $serviceSlug = null): Collection
    {
        return PriceListItem::query()
            ->where('category', $category)
            ->where('component_group', $componentGroup)
            ->when($category === 'product', fn ($query) => $query->whereNull('product_id'))
            ->when($category === 'service', fn ($query) => $query->where('service_slug', $serviceSlug))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['label', 'price'])
            ->map(fn (PriceListItem $item): array => [
                'label' => (string) $item->label,
                'price' => (float) $item->price,
            ]);
    }

    /**
     * @return array<int, array{type: string, key: string, label: string, price: float, edit_url: string}>
     */
    public static function search(string $query, int $limit = 20): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $products = Product::query()
            ->where('is_active', true)
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn (Product $product): array => [
                'type' => 'product',
                'key' => (string) $product->id,
                'label' => (string) $product->name,
                'price' => (float) $product->price,
                'edit_url' => route('admin.pricelist.products.edit', $product),
            ]);

        $services = collect(ServiceCatalog::all())
            ->filter(fn (array $service, string $slug): bool => str_contains(strtolower((string) ($service['name'] ?? $slug)), strtolower($query)))
            ->map(fn (array $service, string $slug): array => [
                'type' => 'service',
                'key' => $slug,
                'label' => (string) ($service['name'] ?? $slug),
                'price' => ServiceCatalog::priceForSlug($slug),
                'edit_url' => route('admin.pricelist.services.edit', $slug),
            ])
            ->values();

        return $products->concat($services)->take($limit)->all();
    }

    public static function clearCache(): void
    {
        // No cache layer yet — reads go straight to the database. Kept as a
        // stable hook so a cache can be introduced later without touching callers.
    }
}
