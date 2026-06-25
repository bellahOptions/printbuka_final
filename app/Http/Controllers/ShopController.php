<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->input('sort', 'featured');

        $products = ShopProduct::query()
            ->active()
            ->when($request->input('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('short_description', 'like', "%{$s}%"))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->when($request->boolean('on_sale'), fn ($q) => $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->when($sort === 'price_asc',  fn ($q) => $q->orderByRaw('COALESCE(sale_price, price) ASC'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByRaw('COALESCE(sale_price, price) DESC'))
            ->when($sort === 'newest',     fn ($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'popular',    fn ($q) => $q->orderByDesc('view_count'))
            ->when(!in_array($sort, ['price_asc', 'price_desc', 'newest', 'popular']), fn ($q) => $q->orderByDesc('is_featured')->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        $totalCount = ShopProduct::active()->count();
        $saleCount  = ShopProduct::active()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count();

        return view('shop.index', compact('products', 'totalCount', 'saleCount'));
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
