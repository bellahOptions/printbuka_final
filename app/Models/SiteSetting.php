<?php

namespace App\Models;

use App\Support\SiteSettings;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            SiteSettings::clearCache();
        });

        static::deleted(function (): void {
            SiteSettings::clearCache();
        });
    }
}
