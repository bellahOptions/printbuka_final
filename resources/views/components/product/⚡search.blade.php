<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\ShopProduct;
use Illuminate\Support\Collection;

new class extends Component
{
    public string $query   = '';
    public string $variant = 'default';

    public function render()
    {
        $q = trim($this->query);

        $products  = collect();
        $shopItems = collect();

        if ($q !== '') {
            $products = Product::query()
                ->where('is_active', true)
                ->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                            ->orWhere('short_description', 'like', "%{$q}%");
                })
                ->limit(6)
                ->get();

            $shopItems = ShopProduct::query()
                ->active()
                ->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                            ->orWhere('short_description', 'like', "%{$q}%");
                })
                ->limit(4)
                ->get();
        }

        return $this->view([
            'products'  => $products,
            'shopItems' => $shopItems,
        ]);
    }
}
?>

<div @class([
    'w-full',
    'max-w-3xl' => $variant !== 'nav',
    'max-w-none' => $variant === 'nav',
])>
    <div @class([
        'flex w-full rounded-md bg-white',
        'flex-col gap-3 p-2 shadow-xl shadow-cyan-950/10 sm:flex-row' => $variant !== 'nav',
        'items-center border border-slate-200 p-1 shadow-sm' => $variant === 'nav',
    ])>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            @class([
                'w-full rounded-md font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-4 focus:ring-pink-100',
                'min-h-14 border border-slate-200 px-4 text-base' => $variant !== 'nav',
                'min-h-10 border-0 px-3 text-sm focus:ring-0' => $variant === 'nav',
            ])
            placeholder="{{ $variant === 'nav' ? 'Search products & shop items...' : 'Search print products, branded gifts, mugs, shirts, UV-DTF or packaging' }}"
        />
        <button
            type="button"
            @class([
                'rounded-md bg-pink-600 font-black text-white shadow-sm shadow-pink-200 transition hover:bg-pink-700',
                'min-h-14 px-6 text-sm sm:w-52' => $variant !== 'nav',
                'min-h-10 px-4 text-xs' => $variant === 'nav',
            ])
        >
            Search
        </button>
    </div>

    <div class="mt-4">
        <div wire:loading wire:target="query" class="text-sm text-pink-600 font-semibold">
            Searching...
        </div>

        @if (trim($query) !== '')
            <div @class([
                'mt-3 space-y-2',
                'max-h-80 overflow-auto rounded-md border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10' => $variant === 'nav',
            ])>
                @if ($products->isEmpty() && $shopItems->isEmpty())
                    <p class="py-6 text-center text-sm text-slate-500">No results found for "<strong>{{ $query }}</strong>".</p>
                @else

                    {{-- Custom Order Products --}}
                    @if ($products->isNotEmpty())
                        <p class="px-1 pb-1 text-[10px] font-black uppercase tracking-widest text-slate-400">Custom Print Products</p>
                        @foreach ($products as $product)
                            <article class="group flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm hover:border-pink-200 hover:shadow-md transition-all">
                                @if($product->featuredImageUrl())
                                    <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                         class="h-12 w-12 rounded-lg object-cover shrink-0 border border-slate-100">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-pink-50 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-printer class="w-5 h-5 text-pink-300" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <a href="{{ route('products.show', $product) }}" class="font-black text-sm text-slate-900 hover:text-pink-600 transition truncate">{{ $product->name }}</a>
                                        <span class="shrink-0 text-[9px] font-black uppercase tracking-wider bg-pink-100 text-pink-700 px-1.5 py-0.5 rounded-full">Custom Order</span>
                                    </div>
                                    @if($product->short_description)
                                        <p class="text-xs text-slate-500 line-clamp-1">{{ $product->short_description }}</p>
                                    @endif
                                    <div class="mt-1.5 flex items-center gap-3">
                                        <span class="text-xs font-black text-pink-600">
                                            {{ $product->hasAvailablePrice() ? 'from NGN '.number_format($product->price, 0) : 'Quote on request' }}
                                        </span>
                                        @if($product->moq)
                                            <span class="text-[10px] text-slate-400 font-bold">MOQ: {{ $product->moq }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : route('products.show', $product) }}"
                                   class="shrink-0 self-center rounded-lg bg-slate-900 group-hover:bg-pink-600 px-3 py-1.5 text-xs font-black text-white transition-colors">
                                    {{ $product->hasAvailablePrice() ? 'Order' : 'View' }}
                                </a>
                            </article>
                        @endforeach
                    @endif

                    {{-- Shop / Ready-Made Items --}}
                    @if ($shopItems->isNotEmpty())
                        <p class="px-1 pb-1 pt-2 text-[10px] font-black uppercase tracking-widest text-slate-400">Ready-Made Shop</p>
                        @foreach ($shopItems as $item)
                            <article class="group flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all">
                                @if($item->featuredImageUrl())
                                    <img src="{{ $item->featuredImageUrl() }}" alt="{{ $item->name }}"
                                         class="h-12 w-12 rounded-lg object-cover shrink-0 border border-slate-100">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-shopping-bag class="w-5 h-5 text-emerald-300" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <a href="{{ route('shop.show', $item) }}" class="font-black text-sm text-slate-900 hover:text-emerald-600 transition truncate">{{ $item->name }}</a>
                                        <span class="shrink-0 text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">Buy Now</span>
                                        @if($item->isOnSale())
                                            <span class="shrink-0 text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">Sale</span>
                                        @endif
                                    </div>
                                    @if($item->short_description)
                                        <p class="text-xs text-slate-500 line-clamp-1">{{ $item->short_description }}</p>
                                    @endif
                                    <div class="mt-1.5 flex items-center gap-3">
                                        <span class="text-xs font-black text-emerald-600">NGN {{ number_format($item->currentPrice(), 0) }}</span>
                                        @if($item->isOnSale())
                                            <span class="text-[10px] text-slate-400 font-bold line-through">NGN {{ number_format((float)$item->price, 0) }}</span>
                                        @endif
                                        @if(!$item->isInStock())
                                            <span class="text-[10px] font-black text-red-500">Out of stock</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('shop.show', $item) }}"
                                   class="shrink-0 self-center rounded-lg bg-emerald-600 group-hover:bg-emerald-700 px-3 py-1.5 text-xs font-black text-white transition-colors">
                                    Buy Now
                                </a>
                            </article>
                        @endforeach
                    @endif

                @endif
            </div>
        @endif
    </div>
</div>
