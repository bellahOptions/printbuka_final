<?php

namespace App\Support;

class CompactNumber
{
    /**
     * Format a number compactly (1.5M instead of 1,500,000) so it never
     * overflows a fixed-width stat card. Used universally for any large
     * summary figure — finance totals, dashboard KPIs, etc. Full precision
     * is always available via the $title attribute callers should add
     * alongside this (e.g. title="{{ number_format($amount, 2) }}").
     */
    public static function format(float $value, int $decimals = 1): string
    {
        $sign = $value < 0 ? '-' : '';
        $abs = abs($value);

        [$divisor, $suffix] = match (true) {
            $abs >= 1_000_000_000 => [1_000_000_000, 'B'],
            $abs >= 1_000_000 => [1_000_000, 'M'],
            $abs >= 1_000 => [1_000, 'K'],
            default => [1, ''],
        };

        if ($divisor === 1) {
            return $sign.number_format($abs, 2);
        }

        return $sign.rtrim(rtrim(number_format($abs / $divisor, $decimals), '0'), '.').$suffix;
    }

    public static function currency(float $value, string $symbol = '₦', int $decimals = 1): string
    {
        return $symbol.self::format($value, $decimals);
    }
}
