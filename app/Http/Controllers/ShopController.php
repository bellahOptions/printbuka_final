<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => (string) $request->input('search', ''),
            'featured' => $request->boolean('featured'),
            'on_sale' => $request->boolean('on_sale'),
            'sort' => (string) $request->input('sort', 'featured'),
        ];

        $totalCount = ShopProduct::active()->count();
        $saleCount  = ShopProduct::active()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count();

        return view('shop.index', compact('filters', 'totalCount', 'saleCount'));
    }

    public function show(ShopProduct $product): View
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        $product->loadMissing('optionGroups.options');

        $relatedProducts = ShopProduct::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
