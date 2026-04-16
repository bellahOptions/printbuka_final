<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Str;

class ReferenceCode
{
    public static function invoiceNumber(): string
    {
        return self::unique('INV', fn (string $code): bool => Invoice::query()->where('invoice_number', $code)->exists());
    }

    public static function jobOrderNumber(string $serviceType = 'print'): string
    {
        $prefix = $serviceType === 'quote' ? 'QTE' : 'JOB';

        return self::unique($prefix, fn (string $code): bool => Order::query()->where('job_order_number', $code)->exists());
    }

    private static function unique(string $prefix, callable $exists): string
    {
        for ($attempt = 0; $attempt < 12; $attempt++) {
            $candidate = sprintf(
                '%s-%s-%s',
                $prefix,
                now()->format('Ymd'),
                strtoupper(Str::random(6))
            );

            if (! $exists($candidate)) {
                return $candidate;
            }
        }

        return sprintf(
            '%s-%s-%s',
            $prefix,
            now()->format('YmdHis'),
            strtoupper(Str::random(8))
        );
    }
}

