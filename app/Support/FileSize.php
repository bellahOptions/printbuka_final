<?php

namespace App\Support;

class FileSize
{
    /**
     * Human-readable byte size (18.35 MB, 1.2 GB, …). Used for backup
     * archive sizes and free-disk-space readouts.
     */
    public static function format(?int $bytes, int $decimals = 2): string
    {
        if ($bytes === null) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? min((int) floor(log($bytes, 1024)), count($units) - 1) : 0;
        $value = $bytes / (1024 ** $power);

        return round($value, $power === 0 ? 0 : $decimals).' '.$units[$power];
    }
}
