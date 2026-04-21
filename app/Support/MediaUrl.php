<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function resolve(?string $value, string $disk = 'public'): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $candidate = str_replace('\\', '/', trim((string) $value));

        if ($candidate === '') {
            return null;
        }

        if (str_starts_with($candidate, 'data:')) {
            return $candidate;
        }

        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            return self::normalizeUrlHostDependency($candidate);
        }

        if (str_starts_with($candidate, '/')) {
            return self::normalizeRootPath($candidate, $disk);
        }

        $normalizedStoragePath = self::normalizeStoragePath($candidate);

        if (Storage::disk($disk)->exists($normalizedStoragePath)) {
            return '/storage/'.ltrim($normalizedStoragePath, '/');
        }

        if (file_exists(public_path($candidate))) {
            return '/'.ltrim($candidate, '/');
        }

        return null;
    }

    private static function normalizeUrlHostDependency(string $candidate): string
    {
        $path = parse_url($candidate, PHP_URL_PATH);
        $query = parse_url($candidate, PHP_URL_QUERY);

        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $candidate;
        }

        if (! is_string($query) || $query === '') {
            return $path;
        }

        return $path.'?'.$query;
    }

    private static function normalizeRootPath(string $path, string $disk): ?string
    {
        if (str_starts_with($path, '/storage/')) {
            $normalizedStoragePath = self::normalizeStoragePath($path);

            if (Storage::disk($disk)->exists($normalizedStoragePath)) {
                return '/storage/'.ltrim($normalizedStoragePath, '/');
            }

            return null;
        }

        return file_exists(public_path($path)) ? $path : null;
    }

    private static function normalizeStoragePath(string $value): string
    {
        $path = ltrim(str_replace('\\', '/', trim($value)), '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return ltrim($path, '/');
    }
}
