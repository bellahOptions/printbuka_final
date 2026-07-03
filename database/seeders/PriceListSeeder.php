<?php

namespace Database\Seeders;

use App\Models\PriceListItem;
use App\Support\ProductOptionPricing;
use App\Support\ServiceCatalog;
use App\Support\SiteSettings;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    /**
     * Migrate existing scattered pricing (SiteSettings default option prices,
     * service prices, surcharges) into the centralized price_list_items table.
     * Product-level base price and option pricing stay on the products table
     * itself. Safe to re-run — every write is an updateOrCreate.
     */
    public function run(): void
    {
        $this->seedGlobalProductDefaults();
        $this->seedServices();
        $this->seedSurcharges();
    }

    private function seedGlobalProductDefaults(): void
    {
        $groups = [
            'material' => 'default_material_price_options',
            'size' => 'default_size_price_options',
            'finish' => 'default_finish_price_options',
            'density' => 'default_density_price_options',
            'delivery' => 'default_delivery_price_options',
        ];

        foreach ($groups as $componentGroup => $settingKey) {
            $options = ProductOptionPricing::parseLines((string) SiteSettings::get($settingKey, ''));

            foreach ($options as $index => $option) {
                PriceListItem::query()->updateOrCreate(
                    [
                        'category' => 'product',
                        'product_id' => null,
                        'component_group' => $componentGroup,
                        'label' => $option['label'],
                    ],
                    [
                        'price' => $option['price'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function seedServices(): void
    {
        foreach (ServiceCatalog::all() as $slug => $service) {
            $basePrice = (float) ($service['default_price'] ?? 0);

            PriceListItem::query()->updateOrCreate(
                [
                    'category' => 'service',
                    'service_slug' => $slug,
                    'component_group' => null,
                    'label' => (string) ($service['name'] ?? $slug),
                ],
                ['price' => $basePrice, 'is_active' => true]
            );
        }

        $fees = [
            'direct-image-printing' => [
                'design_fee' => 'service_price_direct_image_printing_design',
                'delivery_fee' => 'service_price_direct_image_printing_delivery',
            ],
            'dtf' => [
                'design_fee' => 'service_price_dtf_design',
                'delivery_fee' => 'service_price_dtf_delivery',
            ],
        ];

        foreach ($fees as $slug => $feeKeys) {
            foreach ($feeKeys as $componentKey => $settingKey) {
                PriceListItem::query()->updateOrCreate(
                    [
                        'category' => 'service',
                        'service_slug' => $slug,
                        'component_group' => 'fee',
                        'component_key' => $componentKey,
                    ],
                    [
                        'label' => str($componentKey)->replace('_', ' ')->title()->toString(),
                        'price' => (float) SiteSettings::get($settingKey, 0),
                        'is_active' => true,
                    ]
                );
            }
        }

        $dtfSizeOptions = ProductOptionPricing::parseLines((string) SiteSettings::get('service_dtf_size_price_options', ''));

        foreach ($dtfSizeOptions as $index => $option) {
            PriceListItem::query()->updateOrCreate(
                [
                    'category' => 'service',
                    'service_slug' => 'dtf',
                    'component_group' => 'size',
                    'label' => $option['label'],
                ],
                [
                    'price' => $option['price'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedSurcharges(): void
    {
        $surcharges = [
            'express_order_surcharge' => (float) SiteSettings::get('express_order_surcharge', 5000),
            'sample_order_surcharge' => (float) SiteSettings::get('sample_order_surcharge', 5000),
        ];

        foreach ($surcharges as $key => $price) {
            PriceListItem::query()->updateOrCreate(
                ['category' => 'surcharge', 'component_key' => $key],
                [
                    'label' => str($key)->replace('_', ' ')->title()->toString(),
                    'price' => $price,
                    'is_active' => true,
                ]
            );
        }
    }
}
