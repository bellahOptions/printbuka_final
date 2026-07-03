<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\ServiceCatalog;
use Illuminate\Database\Seeder;

class GiftAndServiceProductSeeder extends Seeder
{
    /**
     * Seeds starter catalog entries for gift/merch, Laser Engraving, and UV DTF
     * items so staff have real products to price and quote against in the
     * Pricelist. Prices are starting points — adjust via /admin/pricelist.
     */
    public function run(): void
    {
        $categoryIds = ProductCategory::query()->pluck('id', 'slug');

        foreach ($this->products() as $product) {
            $categorySlug = $product['category_slug'] ?? null;
            unset($product['category_slug']);

            Product::query()->updateOrCreate(
                ['name' => $product['name']],
                [
                    ...$this->defaults(),
                    ...$product,
                    'is_seeded' => true,
                    'product_category_id' => $categorySlug ? $categoryIds->get($categorySlug) : null,
                ]
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function defaults(): array
    {
        return [
            'moq' => 1,
            'is_active' => true,
            'is_featured' => false,
            'price_unavailable' => false,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function products(): array
    {
        return [
            // Branding & merchandise (from the Printbuka price-list reference sheet)
            [
                'category_slug' => 'custom-t-shirts',
                'name' => 'Customised T-Shirts',
                'service_type' => 'gift',
                'moq' => 20,
                'price' => 4500,
                'short_description' => 'Screen print or DTF branding, per piece.',
                'description' => 'Custom-branded t-shirts printed via screen print or DTF transfer, priced per piece with a minimum order of 20 units.',
                'paper_type' => 'Cotton',
                'paper_size' => 'Standard (S–XL)',
                'finishing' => 'Screen Print / DTF',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'custom-mugs',
                'name' => 'Branded Mugs',
                'service_type' => 'gift',
                'moq' => 12,
                'price' => 3500,
                'short_description' => 'Full-colour wrap print, per piece.',
                'description' => 'Ceramic mugs with a full-colour wraparound print of your logo or design, minimum order of 12 units.',
                'paper_type' => 'Ceramic',
                'paper_size' => '11oz Standard',
                'finishing' => 'Full Colour Wrap',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'caps-hats',
                'name' => 'Customised Caps',
                'service_type' => 'gift',
                'moq' => 20,
                'price' => 5000,
                'short_description' => 'Embroidered logo, per piece.',
                'description' => 'Branded caps with an embroidered logo, minimum order of 20 units.',
                'paper_type' => 'Cotton Twill',
                'paper_size' => 'Standard',
                'finishing' => 'Embroidered Logo',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'branded-notepads-and-jotters',
                'name' => 'Corporate Jotters / Notepads',
                'service_type' => 'gift',
                'moq' => 50,
                'price' => 2000,
                'short_description' => 'A5, branded cover, per piece.',
                'description' => 'A5 corporate jotters with a branded cover, minimum order of 50 units.',
                'paper_type' => 'Art Card 300gsm',
                'paper_size' => 'A5',
                'finishing' => 'Branded Cover',
                'paper_density' => '300gsm',
            ],

            // Laser Engraving items — each item is its own product; Engraved Plaque
            // also carries size-based price add-ons via size_price_options.
            [
                'category_slug' => 'gift-items-corporate-gifts',
                'name' => 'Laser Engraved Pen',
                'service_type' => ServiceCatalog::serviceTypeForSlug('laser-engraving'),
                'price' => 1500,
                'short_description' => 'Precision laser-engraved metal or plastic pen.',
                'description' => 'A metal or plastic pen with a permanent laser-engraved logo or name, ideal for corporate gifting.',
                'paper_type' => 'Metal / Plastic',
                'paper_size' => 'Standard',
                'finishing' => 'Laser Engraved',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'gift-items-corporate-gifts',
                'name' => 'Laser Engraved Mug',
                'service_type' => ServiceCatalog::serviceTypeForSlug('laser-engraving'),
                'price' => 3000,
                'short_description' => 'Ceramic mug with permanent laser engraving.',
                'description' => 'A ceramic mug with a permanent laser-engraved logo or design, ideal for corporate gifting.',
                'paper_type' => 'Ceramic',
                'paper_size' => '11oz Standard',
                'finishing' => 'Laser Engraved',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'gift-items-corporate-gifts',
                'name' => 'Laser Engraved Plaque',
                'service_type' => ServiceCatalog::serviceTypeForSlug('laser-engraving'),
                'price' => 8000,
                'short_description' => 'Acrylic or wood award plaque, size-dependent pricing.',
                'description' => 'A premium acrylic or wood plaque with permanent laser engraving, priced by size.',
                'paper_type' => 'Acrylic / Wood',
                'paper_size' => 'Medium',
                'finishing' => 'Laser Engraved',
                'paper_density' => 'N/A',
                'size_price_options' => [
                    ['label' => 'Small', 'price' => 0],
                    ['label' => 'Medium', 'price' => 2000],
                    ['label' => 'Large', 'price' => 4000],
                ],
            ],
            [
                'category_slug' => 'gift-items-corporate-gifts',
                'name' => 'Laser Engraved Bottle',
                'service_type' => ServiceCatalog::serviceTypeForSlug('laser-engraving'),
                'price' => 4000,
                'short_description' => 'Stainless steel bottle with permanent laser engraving.',
                'description' => 'A stainless steel water bottle with a permanent laser-engraved logo or name.',
                'paper_type' => 'Stainless Steel',
                'paper_size' => 'Standard',
                'finishing' => 'Laser Engraved',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'gift-items-corporate-gifts',
                'name' => 'Laser Engraved Notepad Cover',
                'service_type' => ServiceCatalog::serviceTypeForSlug('laser-engraving'),
                'price' => 2500,
                'short_description' => 'Leather or PU notepad cover with laser-engraved branding.',
                'description' => 'An A5 leather or PU notepad cover with a permanent laser-engraved logo.',
                'paper_type' => 'Leather / PU',
                'paper_size' => 'A5',
                'finishing' => 'Laser Engraved',
                'paper_density' => 'N/A',
            ],

            // UV DTF items — each item is its own product with its own base price.
            [
                'category_slug' => 'uv-dtf-products',
                'name' => 'UV DTF Pen',
                'service_type' => ServiceCatalog::serviceTypeForSlug('uv-dtf'),
                'price' => 1200,
                'short_description' => 'Durable UV DTF transfer branding on a pen.',
                'description' => 'A pen branded with a scratch-resistant UV DTF transfer of your logo or design.',
                'paper_type' => 'Metal / Plastic',
                'paper_size' => 'Standard',
                'finishing' => 'UV DTF Transfer',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'uv-dtf-products',
                'name' => 'UV DTF Mug',
                'service_type' => ServiceCatalog::serviceTypeForSlug('uv-dtf'),
                'price' => 2500,
                'short_description' => 'Durable UV DTF transfer branding on a ceramic mug.',
                'description' => 'A ceramic mug branded with a scratch-resistant UV DTF transfer of your logo or design.',
                'paper_type' => 'Ceramic',
                'paper_size' => '11oz Standard',
                'finishing' => 'UV DTF Transfer',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'uv-dtf-products',
                'name' => 'UV DTF Bottle',
                'service_type' => ServiceCatalog::serviceTypeForSlug('uv-dtf'),
                'price' => 3000,
                'short_description' => 'Durable UV DTF transfer branding on a bottle.',
                'description' => 'A stainless steel bottle branded with a scratch-resistant UV DTF transfer of your logo or design.',
                'paper_type' => 'Stainless Steel',
                'paper_size' => 'Standard',
                'finishing' => 'UV DTF Transfer',
                'paper_density' => 'N/A',
            ],
            [
                'category_slug' => 'uv-dtf-products',
                'name' => 'UV DTF Notepad',
                'service_type' => ServiceCatalog::serviceTypeForSlug('uv-dtf'),
                'price' => 2000,
                'short_description' => 'Durable UV DTF transfer branding on a notepad cover.',
                'description' => 'An A5 notepad branded with a scratch-resistant UV DTF transfer of your logo or design.',
                'paper_type' => 'Art Card',
                'paper_size' => 'A5',
                'finishing' => 'UV DTF Transfer',
                'paper_density' => 'N/A',
            ],
        ];
    }
}
