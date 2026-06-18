<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use App\Services\ProductSuggestionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $products = ShopProduct::query()
            ->active()
            ->when($request->input('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('short_description', 'like', "%{$search}%"))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Shop/Index', [
            'products' => $products->map(fn (ShopProduct $p) => $this->productProps($p))->values(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    public function show(ShopProduct $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        app(ProductSuggestionService::class)->record('shop', $product->id);
        $product->loadMissing('optionGroups.options');

        $relatedProducts = ShopProduct::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return Inertia::render('Shop/Show', [
            'product' => array_merge($this->productProps($product), [
                'description'      => $product->description,
                'short_description' => $product->short_description,
                'additional_images' => $product->additionalImageUrls(),
                'option_groups'    => $product->optionGroups->map(fn ($g) => [
                    'id'      => $g->id,
                    'name'    => $g->name,
                    'options' => $g->options->map(fn ($o) => ['id' => $o->id, 'label' => $o->label, 'price_modifier' => $o->price_modifier])->all(),
                ])->all(),
            ]),
            'relatedProducts' => $relatedProducts->map(fn (ShopProduct $p) => $this->productProps($p))->values(),
        ]);
    }

    private function productProps(ShopProduct $p): array
    {
        return [
            'id'       => $p->id,
            'proImg'   => $p->featuredImageUrl() ?? '/img/product-placeholder.svg',
            'title'    => $p->name,
            'slug'     => $p->slug,
            'price'    => number_format($p->currentPrice(), 2, '.', ''),
            'delPrice' => $p->isOnSale() ? number_format((float) $p->price, 2, '.', '') : null,
            'brand'    => 'Printbuka',
        ];
    }
}
