<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    /**
     * @param  array<int, string>  $labels
     */
    private static function pricedLines(array $labels): string
    {
        return collect($labels)
            ->map(fn (string $label): string => trim($label))
            ->filter(fn (string $label): bool => $label !== '')
            ->map(fn (string $label): string => $label.'|0')
            ->implode(PHP_EOL);
    }

    public static function all(): array
    {
        $defaults = [
            'site_name' => config('app.name', 'Printbuka'),
            'contact_email' => 'sales@printbuka.com.ng',
            'contact_phone' => '08035245784, 09054784526',
            'company_account_name' => '',
            'company_account_number' => '',
            'company_account_bank_name' => '',
            'company_account_note' => '',
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
            'default_material_price_options' => self::pricedLines((array) config('printbuka_admin.materials', [])),
            'default_size_price_options' => self::pricedLines((array) config('printbuka_admin.sizes', [])),
            'default_finish_price_options' => self::pricedLines((array) config('printbuka_admin.finishes', [])),
            'default_density_price_options' => self::pricedLines([
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
            'default_delivery_price_options' => self::pricedLines((array) config('printbuka_admin.delivery_methods', [])),
            'service_price_direct_image_printing' => (string) (config('printbuka_services.services.direct-image-printing.default_price', 0)),
            'service_price_direct_image_printing_design' => '0',
            'service_price_direct_image_printing_delivery' => '0',
            'service_price_uv_dtf' => (string) (config('printbuka_services.services.uv-dtf.default_price', 0)),
            'service_price_dtf' => (string) (config('printbuka_services.services.dtf.default_price', 0)),
            'service_price_dtf_design' => '0',
            'service_price_dtf_delivery' => '0',
            'service_dtf_size_price_options' => self::pricedLines(['A2', 'A3', 'A4', 'A5', 'A6']),
            'service_price_laser_engraving' => (string) (config('printbuka_services.services.laser-engraving.default_price', 0)),
            'pending_job_reminder_hours' => '24',
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
