<?php

namespace App\Support;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

class SafeCache
{
    public static function remember(string $key, DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Throwable) {
            return $callback();
        }
    }

    public static function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Throwable) {
            // Fallback paths intentionally ignore cache backend failures.
        }
    }
}
