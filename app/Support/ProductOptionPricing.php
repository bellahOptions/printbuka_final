<?php

namespace App\Support;

use App\Models\Product;

class ProductOptionPricing
{
    /**
     * @return array<int, array{label: string, price: float}>
     */
    public static function parseLines(?string $lines): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $lines))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->map(function (string $line): array {
                [$label, $price] = array_pad(explode('|', $line, 2), 2, 0);

                return [
                    'label' => trim($label),
                    'price' => (float) str_replace([',', 'NGN', 'ngn'], '', (string) $price),
                ];
            })
            ->filter(fn (array $option): bool => $option['label'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{label?: mixed, price?: mixed}>|null  $options
     * @return array<int, array{label: string, price: float}>
     */
    public static function normalize(?array $options): array
    {
        return collect($options ?? [])
            ->map(function (array $option): array {
                return [
                    'label' => trim((string) ($option['label'] ?? '')),
                    'price' => (float) ($option['price'] ?? 0),
                ];
            })
            ->filter(fn (array $option): bool => $option['label'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{label?: string, price?: mixed}>|null  $options
     */
    public static function toLines(?array $options): string
    {
        return collect($options ?? [])
            ->map(fn (array $option): string => ($option['label'] ?? '').'|'.number_format((float) ($option['price'] ?? 0), 2, '.', ''))
            ->implode("\n");
    }

    public static function priceFor(Product $product, string $optionSet, ?string $label): float
    {
        if (! $label) {
            return 0;
        }

        $option = collect($product->{$optionSet} ?? [])->firstWhere('label', $label);

        return (float) ($option['price'] ?? 0);
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    public static function optionsForProductOrSetting(
        Product $product,
        string $productOptionSet,
        string $settingKey,
        ?string $fallbackLabel = null
    ): array {
        $productOptions = self::normalize($product->{$productOptionSet} ?? null);

        if ($productOptions !== []) {
            return $productOptions;
        }

        $settingOptions = self::parseLines((string) SiteSettings::get($settingKey, ''));

        if ($settingOptions !== []) {
            return $settingOptions;
        }

        if (filled($fallbackLabel)) {
            return [[
                'label' => (string) $fallbackLabel,
                'price' => 0,
            ]];
        }

        return [];
    }

    public static function priceFromOptions(array $options, ?string $label): float
    {
        if (! filled($label)) {
            return 0;
        }

        $option = collect($options)->firstWhere('label', $label);

        return (float) ($option['price'] ?? 0);
    }
}
