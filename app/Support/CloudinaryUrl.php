<?php

namespace App\Support;

use Cloudinary\Cloudinary;
use Cloudinary\Asset\Media;
use Illuminate\Support\Facades\Cache;

class CloudinaryUrl
{
    /**
     * Cached Cloudinary client instance.
     */
    private static ?Cloudinary $client = null;

    /**
     * Get the singleton Cloudinary client.
     */
    public static function client(): Cloudinary
    {
        if (self::$client === null) {
            self::$client = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => (bool) config('cloudinary.secure', true),
                ],
            ]);
        }

        return self::$client;
    }

    /**
     * Resolve Cloudinary public path → full CDN URL.
     *
     * Accepts formats:
     *   - "printbuka/job-assets/abc123.jpg" (relative public_id)
     *   - "image/upload/v123456/printbuka/job-assets/abc123.jpg" (full API path)
     *   - Full Cloudinary URL (returns as-is if on same cloud)
     *
     * Returns null if the path is not a valid Cloudinary resource.
     */
    public static function resolve(?string $value, array $transformations = []): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $path = trim((string) $value);

        // Already a full URL – return as-is if it's from our cloud
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $cloudName = config('cloudinary.cloud_name');
            if ($cloudName && str_contains($path, "res.cloudinary.com/{$cloudName}")) {
                return $path;
            }

            // External URL that isn't Cloudinary – pass through
            if (! str_contains($path, 'cloudinary.com')) {
                return $path;
            }

            // Another cloud name – rebuild as Cloudinary media
        }

        // Strip full API prefix if present
        $publicId = self::normalizeToPublicId($path);

        if ($publicId === null) {
            return $path;
        }

        // Try to return cached URL
        $cacheKey = 'cloudinary_url_'.md5($publicId.serialize($transformations));

        return Cache::remember($cacheKey, 3600, function () use ($publicId, $transformations): ?string {
            try {
                $media = Media::fromParams($publicId, [
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'secure' => true,
                    ],
                ]);

                if ($media === null) {
                    return null;
                }

                $url = (string) $media->secureUrl();

                // Apply transformations if any
                if ($transformations !== []) {
                    $transformedUrl = self::client()->image($publicId)
                        ->secure()
                        ->quality('auto')
                        ->fetchFormat('auto');

                    foreach ($transformations as $option => $value) {
                        if (method_exists($transformedUrl, $option)) {
                            $transformedUrl->{$option}($value);
                        }
                    }

                    $url = (string) $transformedUrl;
                }

                return $url;
            } catch (\Throwable $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Strip any prefix to extract the public_id (the path in the cloud).
     *
     * Example: "image/upload/v123456/printbuka/job-assets/file.jpg"
     *          → "printbuka/job-assets/file.jpg"
     *
     * Example: "printbuka/job-assets/file.jpg"
     *          → "printbuka/job-assets/file.jpg"
     */
    private static function normalizeToPublicId(string $path): ?string
    {
        $path = str_replace('\\', '/', $path);

        // Match: image/upload/v\d+/<public_id>
        if (preg_match('#^image/upload/v\d+/(.+)$#', $path, $m)) {
            return $m[1];
        }

        // Match: image/upload/<public_id> (without version)
        if (preg_match('#^image/upload/(.+)$#', $path, $m)) {
            return $m[1];
        }

        // If it already looks like a relative path (contains / and has extension), treat as public_id
        if (preg_match('#^[a-zA-Z0-9_\-/]+\.[a-zA-Z0-9]{2,4}$#', $path)) {
            return $path;
        }

        // Might not be a Cloudinary path at all
        return null;
    }

    /**
     * Build a public_id from a storage path and folder.
     * E.g., "job-assets/images/file.jpg" → "printbuka/job-assets/images/file.jpg"
     */
    public static function publicId(string $storagePath, string $folder = ''): string
    {
        $normalized = ltrim(str_replace('\\', '/', $storagePath), '/');
        $defaultFolder = trim((string) config('cloudinary.default_folder', 'printbuka'), '/');

        $parts = collect([$defaultFolder]);

        if (filled($folder)) {
            $parts->push(trim($folder, '/'));
        }

        $parts->push($normalized);

        return $parts->implode('/');
    }

    /**
     * Check if a string looks like a Cloudinary resource identifier.
     */
    public static function isCloudinaryResource(string $value): bool
    {
        return str_contains($value, 'cloudinary.com')
            || preg_match('#^[a-zA-Z0-9_\-/]+\.[a-zA-Z0-9]{2,4}$#', $value)
            || str_starts_with($value, 'image/upload/');
    }

    /**
     * Check if Cloudinary is configured.
     */
    public static function isConfigured(): bool
    {
        return filled(config('cloudinary.cloud_name'))
            && filled(config('cloudinary.api_key'))
            && filled(config('cloudinary.api_secret'));
    }
}
