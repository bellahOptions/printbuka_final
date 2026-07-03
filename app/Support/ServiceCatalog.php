<?php

namespace App\Support;

class ServiceCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return (array) config('printbuka_services.services', []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        $services = self::all();

        if (! array_key_exists($slug, $services)) {
            return null;
        }

        $service = (array) $services[$slug];
        $service['slug'] = $slug;
        $service['price'] = self::priceForSlug($slug);

        return $service;
    }

    public static function priceForSlug(string $slug): float
    {
        $service = (array) (self::all()[$slug] ?? []);
        $fallback = (float) ($service['default_price'] ?? 0);

        return PriceList::serviceBase($slug) ?? $fallback;
    }

    public static function feeForSlug(string $slug, string $feeKey): float
    {
        return PriceList::serviceFee($slug, $feeKey);
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    public static function optionsForSlug(string $slug, string $componentGroup): array
    {
        return PriceList::serviceOptions($slug, $componentGroup);
    }

    public static function serviceTypeForSlug(string $slug): string
    {
        return 'service:'.$slug;
    }

    public static function slugFromServiceType(?string $serviceType): ?string
    {
        $value = (string) $serviceType;

        if (! str_starts_with($value, 'service:')) {
            return null;
        }

        $slug = substr($value, 8);

        return array_key_exists($slug, self::all()) ? $slug : null;
    }

    public static function isServiceType(?string $serviceType): bool
    {
        return self::slugFromServiceType($serviceType) !== null;
    }
}
