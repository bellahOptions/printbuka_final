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

        // 1. Data URIs – pass through
        if (str_starts_with($candidate, 'data:')) {
            return $candidate;
        }

        // 2. If Cloudinary is configured, try to resolve as Cloudinary resource first
        if (CloudinaryUrl::isCloudinaryResource($candidate)) {
            $cloudUrl = CloudinaryUrl::resolve($candidate);
            if ($cloudUrl !== null) {
                return $cloudUrl;
            }
        }

        // 3. Already a full URL (non-storage, non-cloudinary)
        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            // If it's a Cloudinary URL, pass through
            if (str_contains($candidate, 'cloudinary.com')) {
                return $candidate;
            }
            return self::normalizeUrlHostDependency($candidate);
        }

        // 4. Local absolute path
        if (str_starts_with($candidate, '/')) {
            return self::normalizeRootPath($candidate, $disk);
        }

        // 6. Relative storage path – check disk
        $normalizedStoragePath = self::normalizeStoragePath($candidate);

        if ($normalizedStoragePath !== '' && Storage::disk($disk)->exists($normalizedStoragePath)) {
            return self::storageUrl($disk, $normalizedStoragePath);
        }

        // 7. Public path
        if (file_exists(public_path($candidate))) {
            return asset(ltrim($candidate, '/'));
        }

        // 8. Last resort – try Cloudinary (if configured)
        if (CloudinaryUrl::isConfigured()) {
            $cloudUrl = CloudinaryUrl::resolve($candidate);
            if ($cloudUrl !== null) {
                return $cloudUrl;
            }
        }

        return null;
    }

    private static function normalizeUrlHostDependency(string $candidate): string
    {
        // If it's already a Cloudinary URL, don't strip
        if (str_contains($candidate, 'cloudinary.com')) {
            return $candidate;
        }

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
        $normalizedStoragePath = self::normalizeStoragePath($path);

        if ($normalizedStoragePath !== '' && Storage::disk($disk)->exists($normalizedStoragePath)) {
            return self::storageUrl($disk, $normalizedStoragePath);
        }

        return file_exists(public_path($path)) ? asset(ltrim($path, '/')) : null;
    }

    private static function normalizeStoragePath(string $value): string
    {
        $path = rawurldecode(ltrim(str_replace('\\', '/', trim($value)), '/'));

        if ($path === '') {
            return '';
        }

        if (preg_match('#(?:^|/)storage/app/public/(.+)$#', $path, $matches) === 1) {
            return ltrim((string) ($matches[1] ?? ''), '/');
        }

        if (preg_match('#(?:^|/)public/storage/(.+)$#', $path, $matches) === 1) {
            return ltrim((string) ($matches[1] ?? ''), '/');
        }

        if (str_starts_with($path, 'app/public/')) {
            $path = substr($path, strlen('app/public/'));
        }

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return ltrim($path, '/');
    }

    private static function storageUrl(string $disk, string $path): string
    {
        return self::normalizeUrlHostDependency(Storage::disk($disk)->url($path));
    }
}
