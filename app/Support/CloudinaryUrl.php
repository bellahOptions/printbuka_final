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
            return null;
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

        // Relative path with or without extension — Cloudinary public IDs often omit extensions
        // when fetch_format:auto is used. Must contain a slash to distinguish from bare filenames.
        if (str_contains($path, '/') && preg_match('#^[a-zA-Z0-9_\-/]+(\.[a-zA-Z0-9]{2,4})?$#', $path)) {
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
     *
     * Matches only genuine Cloudinary identifiers:
     *   - Full cloudinary.com URLs
     *   - Cloudinary API paths  (image/upload/...)
     *   - Paths that begin with our configured default folder (e.g. "printbuka/...")
     *
     * Deliberately does NOT match bare relative storage paths like
     * "product-images/featured/abc.jpg" so local-only files are never
     * mistakenly routed through the CDN.
     */
    public static function isCloudinaryResource(string $value): bool
    {
        if (str_contains($value, 'cloudinary.com')) {
            return true;
        }

        if (str_starts_with($value, 'image/upload/')) {
            return true;
        }

        $defaultFolder = trim((string) config('cloudinary.default_folder', 'printbuka'), '/');

        return $defaultFolder !== '' && str_starts_with($value, $defaultFolder.'/');
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
