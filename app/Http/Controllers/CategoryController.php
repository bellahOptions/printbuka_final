<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => [
                [
                    'name' => 'Business Essentials',
                    'tag' => 'Print',
                    'description' => 'Business cards, letterheads, ID cards, envelopes and office stationery.',
                    'products' => ['Business Cards', 'Letterheads', 'ID Cards', 'Envelopes'],
                    'image' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'name' => 'Marketing Prints',
                    'tag' => 'Campaigns',
                    'description' => 'Flyers, posters, brochures, menus, catalogues and postcards for launches and promotions.',
                    'products' => ['Flyers', 'Posters', 'Brochures', 'Menus'],
                    'image' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'name' => 'Packaging',
                    'tag' => 'Retail',
                    'description' => 'Stickers, labels, paper bags, courier bags and product sleeves for a finished brand experience.',
                    'products' => ['Stickers', 'Labels', 'Paper Bags', 'Courier Bags'],
                    'image' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'name' => 'Branded Gifts',
                    'tag' => 'Core Service',
                    'description' => 'Mugs, shirts, tote bags, notebooks, hampers and corporate gift sets for clients and teams.',
                    'products' => ['Branded Mugs', 'T-shirts', 'Tote Bags', 'Gift Sets'],
                    'image' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'name' => 'Event Materials',
                    'tag' => 'Events',
                    'description' => 'Banners, roll-ups, name tags, programmes and branded giveaways for memorable events.',
                    'products' => ['Banners', 'Roll-ups', 'Name Tags', 'Programmes'],
                    'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'name' => 'Large Format',
                    'tag' => 'Outdoor',
                    'description' => 'Posters, banners, signage and display prints for visibility in bigger spaces.',
                    'products' => ['Posters', 'Banners', 'Signage', 'Displays'],
                    'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
                ],
            ],
        ]);
    }
}
