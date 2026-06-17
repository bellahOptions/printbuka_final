<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = ShopProduct::query()
            ->active()
            ->when($request->input('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('short_description', 'like', "%{$search}%"))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', compact('products'));
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
