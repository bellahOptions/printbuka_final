<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    public static function all(): array
    {
        $defaults = [
            'site_name' => config('app.name', 'Printbuka'),
            'contact_email' => 'sales@printbuka.com.ng',
            'contact_phone' => '08035245784, 09054784526',
            'notification_message' => null,
            'announcement' => null,
            'maintenance_mode' => '0',
            'maintenance_message' => 'We are making a few improvements. Please check back shortly.',
            'paper_types' => implode(PHP_EOL, config('printbuka_admin.materials', [])),
            'paper_sizes' => implode(PHP_EOL, config('printbuka_admin.sizes', [])),
            'finishings' => implode(PHP_EOL, config('printbuka_admin.finishes', [])),
            'paper_densities' => implode(PHP_EOL, [
                '100gsm',
                '115gsm',
                '150gsm',
                '170gsm',
                '200gsm',
                '250gsm',
                '300gsm',
                '350gsm',
                'Self Adhesive',
                'Gift Item',
                'Custom',
            ]),
        ];

        try {
            if (! Schema::hasTable('site_settings')) {
                return $defaults;
            }

            $settings = SiteSetting::query()
                ->pluck('value', 'key')
                ->filter(fn ($value) => filled($value))
                ->all();

            return array_merge($defaults, $settings);
        } catch (\Throwable) {
            return $defaults;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function maintenanceEnabled(): bool
    {
        return in_array(self::get('maintenance_mode', '0'), ['1', 1, true, 'true', 'on'], true);
    }
}
