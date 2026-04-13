<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Business Cards',
                'moq' => 100,
                'price' => 8500.00,
                'short_description' => 'Premium business cards for everyday networking.',
                'description' => 'Full-colour business cards printed on sturdy card stock with a clean professional finish.',
                'paper_type' => 'Art Card',
                'paper_size' => '85mm x 55mm',
                'finishing' => 'Matte Lamination',
                'paper_density' => '300gsm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flyers',
                'moq' => 500,
                'price' => 35000.00,
                'short_description' => 'Bright promotional flyers for campaigns and events.',
                'description' => 'High-quality flyer prints suitable for product launches, events, promos, and brand announcements.',
                'paper_type' => 'Gloss Paper',
                'paper_size' => 'A5',
                'finishing' => 'Gloss Finish',
                'paper_density' => '150gsm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Letterheads',
                'moq' => 100,
                'price' => 18000.00,
                'short_description' => 'Branded letterheads for official communication.',
                'description' => 'Clean letterhead prints on smooth bond paper for proposals, invoices, and office documents.',
                'paper_type' => 'Bond Paper',
                'paper_size' => 'A4',
                'finishing' => 'No Finish',
                'paper_density' => '100gsm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brochures',
                'moq' => 200,
                'price' => 65000.00,
                'short_description' => 'Folded brochures for detailed brand storytelling.',
                'description' => 'Full-colour brochures printed on durable paper, ideal for menus, catalogues, and service guides.',
                'paper_type' => 'Matte Paper',
                'paper_size' => 'A4 Tri-fold',
                'finishing' => 'Creasing and Folding',
                'paper_density' => '170gsm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stickers',
                'moq' => 100,
                'price' => 12000.00,
                'short_description' => 'Custom stickers for packaging and branding.',
                'description' => 'Durable adhesive stickers printed with rich colours for labels, seals, and product packaging.',
                'paper_type' => 'Sticker Paper',
                'paper_size' => 'A4 Sheet',
                'finishing' => 'Die Cut',
                'paper_density' => 'Self Adhesive',
            ],
            [
                'name' => 'Branded Mugs',
                'moq' => 12,
                'price' => 48000.00,
                'short_description' => 'Custom mugs for clients, teams and event gifts.',
                'description' => 'Ceramic mugs branded with your logo, message or campaign artwork for corporate gifting and merch packs.',
                'paper_type' => 'Ceramic',
                'paper_size' => 'Standard Mug',
                'finishing' => 'Full Colour Branding',
                'paper_density' => 'Gift Item',
            ],
            [
                'name' => 'Custom T-Shirts',
                'moq' => 20,
                'price' => 120000.00,
                'short_description' => 'Branded shirts for teams, campaigns and events.',
                'description' => 'Comfortable branded T-shirts produced for corporate events, campaigns, staff uniforms and customer giveaways.',
                'paper_type' => 'Cotton Fabric',
                'paper_size' => 'Mixed Sizes',
                'finishing' => 'Screen Print',
                'paper_density' => 'Gift Item',
            ],
            [
                'name' => 'Corporate Gift Sets',
                'moq' => 10,
                'price' => 250000.00,
                'short_description' => 'Curated gift sets for clients and staff appreciation.',
                'description' => 'Branded corporate gift sets with practical items selected for launches, holidays, conferences and thank-you packages.',
                'paper_type' => 'Mixed Materials',
                'paper_size' => 'Custom Pack',
                'finishing' => 'Branded Packaging',
                'paper_density' => 'Gift Item',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['name' => $product['name']],
                [
                    ...$product,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
