<?php

namespace App\Support;

use Illuminate\Support\Str;

class ExternalAssetLinks
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_HOSTS = [
        'drive.google.com',
        'docs.google.com',
        'mediafire.com',
        'onedrive.live.com',
        '1drv.ms',
        'dropbox.com',
        'mega.nz',
        'wetransfer.com',
        'we.tl',
    ];

    /**
     * @return array<int, string>
     */
    public static function parse(?string $raw): array
    {
        if (! filled($raw)) {
            return [];
        }

        return collect(preg_split('/[\r\n,]+/', (string) $raw) ?: [])
            ->map(fn (string $part): string => trim($part))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function invalidLinks(?string $raw): array
    {
        return collect(self::parse($raw))
            ->reject(fn (string $link): bool => self::isAllowedLink($link))
            ->values()
            ->all();
    }

    public static function appendToNotes(?string $notes, ?string $rawLinks): string
    {
        $baseNotes = trim((string) ($notes ?? ''));
        $links = self::parse($rawLinks);

        if ($links === []) {
            return $baseNotes;
        }

        $formattedLinks = collect($links)
            ->map(fn (string $link): string => '- '.$link)
            ->implode("\n");

        $section = "External asset links:\n".$formattedLinks;

        if ($baseNotes === '') {
            return $section;
        }

        return $baseNotes."\n\n".$section;
    }

    private static function isAllowedLink(string $link): bool
    {
        if (! filter_var($link, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($link, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($link, PHP_URL_HOST));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        return collect(self::ALLOWED_HOSTS)->contains(
            fn (string $allowedHost): bool => $host === $allowedHost || Str::endsWith($host, '.'.$allowedHost)
        );
    }
}

