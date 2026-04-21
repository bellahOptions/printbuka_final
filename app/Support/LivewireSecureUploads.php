<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LivewireSecureUploads
{
    public const SESSION_KEY = 'livewire_secure_image_uploads';

    public static function register(Request $request, string $path): void
    {
        if (! filled($path)) {
            return;
        }

        $uploads = collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->filter(fn ($item): bool => is_string($item) && filled($item))
            ->push($path)
            ->unique()
            ->take(-500)
            ->values()
            ->all();

        $request->session()->put(self::SESSION_KEY, $uploads);
    }

    /**
     * @param  array<int, string>  $paths
     */
    public static function registerMany(Request $request, array $paths): void
    {
        foreach ($paths as $path) {
            self::register($request, (string) $path);
        }
    }

    public static function consumePath(Request $request, ?string $path, array $allowedPrefixes = []): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $candidate = (string) $path;
        $uploads = (array) $request->session()->get(self::SESSION_KEY, []);

        if (! in_array($candidate, $uploads, true)) {
            return null;
        }

        if (! self::matchesAllowedPrefix($candidate, $allowedPrefixes)) {
            return null;
        }

        if (! Storage::disk('public')->exists($candidate)) {
            self::forget($request, $candidate);

            return null;
        }

        self::forget($request, $candidate);

        return $candidate;
    }

    /**
     * @param  array<int, string>  $paths
     * @param  array<int, string>  $allowedPrefixes
     * @return array<int, string>
     */
    public static function consumePaths(Request $request, array $paths, array $allowedPrefixes = []): array
    {
        $consumed = [];

        foreach (collect($paths)->filter()->unique()->values()->all() as $path) {
            $resolved = self::consumePath($request, (string) $path, $allowedPrefixes);

            if ($resolved !== null) {
                $consumed[] = $resolved;
            }
        }

        return $consumed;
    }

    /**
     * @param  string|array<int, string>|null  $paths
     */
    public static function forget(Request $request, string|array|null $paths): void
    {
        $toForget = collect(is_array($paths) ? $paths : [$paths])
            ->filter(fn ($path): bool => is_string($path) && filled($path))
            ->values();

        if ($toForget->isEmpty()) {
            return;
        }

        $uploads = collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->reject(fn ($item): bool => $toForget->containsStrict($item))
            ->values()
            ->all();

        $request->session()->put(self::SESSION_KEY, $uploads);
    }

    /**
     * @param  array<int, string>  $allowedPrefixes
     */
    private static function matchesAllowedPrefix(string $path, array $allowedPrefixes): bool
    {
        if ($allowedPrefixes === []) {
            return true;
        }

        return collect($allowedPrefixes)
            ->filter(fn ($prefix): bool => is_string($prefix) && $prefix !== '')
            ->contains(fn (string $prefix): bool => Str::startsWith($path, rtrim($prefix, '/').'/'));
    }
}
