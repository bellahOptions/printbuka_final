<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gift Items',
                'tag' => 'Gifts',
                'description' => 'Branded and personal gift-ready products for different moments.',
                'children' => [
                    'Corporate Gifts',
                    'Casual Gifts',
                    'Anniversary Gifts',
                ],
            ],
            [
                'name' => 'UV DTF Products',
                'tag' => 'UV DTF',
                'description' => 'UV DTF-ready branded products and transfers.',
                'children' => [],
            ],
            [
                'name' => 'Custom DT',
                'tag' => 'Custom',
                'description' => 'Custom direct transfer products tailored to client branding.',
                'children' => [],
            ],
            [
                'name' => 'Stationaries',
                'tag' => 'Office',
                'description' => 'Everyday stationery and branded office print materials.',
                'children' => [],
            ],
            [
                'name' => 'Promotionals',
                'tag' => 'Promo',
                'description' => 'Products designed for campaigns and promotional visibility.',
                'children' => [],
            ],
            [
                'name' => 'Corporate',
                'tag' => 'Business',
                'description' => 'Corporate-grade products for teams, offices, and events.',
                'children' => [],
            ],
        ];

        foreach ($categories as $entry) {
            $parentSlug = Str::slug($entry['name']);

            $parent = ProductCategory::query()->updateOrCreate(
                ['slug' => $parentSlug],
                [
                    'parent_id' => null,
                    'name' => $entry['name'],
                    'tag' => $entry['tag'],
                    'description' => $entry['description'],
                    'is_active' => true,
                ]
            );

            foreach ($entry['children'] as $childName) {
                $childSlug = Str::slug($entry['name'].' '.$childName);

                ProductCategory::query()->updateOrCreate(
                    ['slug' => $childSlug],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'tag' => 'Sub-category',
                        'description' => $childName.' under '.$entry['name'].'.',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
