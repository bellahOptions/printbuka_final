<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->featured()
            ->orderByDesc('view_count')
            ->limit(8)
            ->get();

        $popularGiftItems = Product::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query
                    ->where('name', 'like', '%gift%')
                    ->orWhere('name', 'like', '%mug%')
                    ->orWhere('name', 'like', '%shirt%')
                    ->orWhere('name', 'like', '%tote%')
                    ->orWhere('description', 'like', '%gift%')
                    ->orWhere('short_description', 'like', '%gift%');
            })
            ->orderByDesc('view_count')
            ->limit(6)
            ->get();

        return view('welcome', compact('featuredProducts', 'popularGiftItems'));
    }
}
