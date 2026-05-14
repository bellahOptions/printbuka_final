<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Str;

class ReferenceCode
{
    public static function invoiceNumber(string $documentType = 'invoice'): string
    {
        return self::sequential(
            $documentType === 'quote' ? 'QT' : 'INV',
            fn (string $code): bool => Invoice::query()->where('invoice_number', $code)->exists()
        );
    }

    public static function quotationNumber(): string
    {
        return self::invoiceNumber('quote');
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

    private static function sequential(string $prefix, callable $exists): string
    {
        $latest = Invoice::query()
            ->where('invoice_number', 'like', $prefix.'-%')
            ->pluck('invoice_number')
            ->map(function (string $number) use ($prefix): int {
                if (preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/', $number, $matches) !== 1) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max() ?? 0;

        for ($next = $latest + 1; $next < $latest + 10000; $next++) {
            $candidate = sprintf('%s-%06d', $prefix, $next);

            if (! $exists($candidate)) {
                return $candidate;
            }
        }

        return self::unique($prefix, $exists);
    }
}
