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
        $legacyCategories = [
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

        $nukreationzCategories = [
            [
                'name' => 'Banners & Large Format',
                'tag' => 'Large Format',
                'description' => 'Outdoor and indoor large-format banner products.',
                'children' => [],
            ],
            [
                'name' => 'Branded Envelopes',
                'tag' => 'Office Print',
                'description' => 'DL and C4 branded envelopes for business correspondence.',
                'children' => [],
            ],
            [
                'name' => 'Branded Notepads and Jotters',
                'tag' => 'Stationery',
                'description' => 'Branded jotters, diaries, and notepads for teams and events.',
                'children' => [],
            ],
            [
                'name' => 'Business Cards',
                'tag' => 'Brand Identity',
                'description' => 'Standard and premium business card printing options.',
                'children' => [],
            ],
            [
                'name' => 'Calendars',
                'tag' => 'Corporate',
                'description' => 'Wall and desk calendar products for annual campaigns.',
                'children' => [],
            ],
            [
                'name' => 'Caps & Hats',
                'tag' => 'Apparel',
                'description' => 'Custom branded caps and hat products.',
                'children' => [],
            ],
            [
                'name' => 'Custom Mugs',
                'tag' => 'Giftware',
                'description' => 'Branded ceramic and magic mug options.',
                'children' => [],
            ],
            [
                'name' => 'Custom T-Shirts',
                'tag' => 'Apparel',
                'description' => 'Custom T-shirt printing for teams, events, and campaigns.',
                'children' => [],
            ],
            [
                'name' => 'Flyers & Handbills',
                'tag' => 'Marketing',
                'description' => 'Promotional flyers, handbills, and event print handouts.',
                'children' => [],
            ],
            [
                'name' => 'Frames & Wall Arts',
                'tag' => 'Decor',
                'description' => 'Canvas and framed wall art prints for interiors and gifting.',
                'children' => [],
            ],
            [
                'name' => 'Greeting Cards',
                'tag' => 'Cards',
                'description' => 'Seasonal and appreciation card products.',
                'children' => [],
            ],
            [
                'name' => 'Letterhead',
                'tag' => 'Office Print',
                'description' => 'Continuation sheets and branded letterhead print products.',
                'children' => [],
            ],
            [
                'name' => 'Marketing Brochures',
                'tag' => 'Marketing',
                'description' => 'Folded brochures and print marketing collateral.',
                'children' => [],
            ],
            [
                'name' => 'Paper Bags',
                'tag' => 'Packaging',
                'description' => 'Branded retail and event paper bag products.',
                'children' => [],
            ],
            [
                'name' => 'Phone Cases',
                'tag' => 'Accessories',
                'description' => 'Custom printed phone case products across popular devices.',
                'children' => [],
            ],
            [
                'name' => 'Plastic Identity Cards',
                'tag' => 'Corporate',
                'description' => 'Portrait and landscape PVC identity card products.',
                'children' => [],
            ],
            [
                'name' => 'Presentation Folders',
                'tag' => 'Office Print',
                'description' => 'Branded folders for proposals, events, and corporate documents.',
                'children' => [],
            ],
            [
                'name' => 'Posters',
                'tag' => 'Marketing',
                'description' => 'A0-A3 poster products for promotions and announcements.',
                'children' => [],
            ],
            [
                'name' => 'Stickers',
                'tag' => 'Labels',
                'description' => 'Round, square, and custom sticker print products.',
                'children' => [],
            ],
            [
                'name' => 'Tote Bags',
                'tag' => 'Packaging',
                'description' => 'Branded tote bag products for events and retail.',
                'children' => [],
            ],
            [
                'name' => 'Wedding Stationery',
                'tag' => 'Events',
                'description' => 'Save-the-date cards and wedding invitation products.',
                'children' => [],
            ],
        ];

        foreach (array_merge($legacyCategories, $nukreationzCategories) as $entry) {
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
