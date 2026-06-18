@php
    $total = $catalogProducts->count() + $shopProducts->count();
@endphp

@if($total > 0)
<section class="py-14 bg-slate-50 border-t border-slate-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8">
            <div>
                <div class="badge badge-outline text-violet-700 border-violet-300 font-black mb-2 inline-flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    {{ $personalized ? 'Suggested For You' : 'You Might Like' }}
                </div>
                <h2 class="text-2xl font-black text-slate-950">
                    {{ $personalized ? 'Picked based on what you\'ve been browsing.' : 'Popular products worth a look.' }}
                </h2>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">All Products</a>
                <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">Shop</a>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 lg:gap-6">

            {{-- Catalog products --}}
            @foreach($catalogProducts as $product)
                <article class="card bg-white border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group">
                    <figure class="h-44 overflow-hidden bg-slate-100">
                        <a href="{{ route('products.show', $product) }}" class="block h-full">
                            <img src="{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                 alt="{{ $product->name }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        </a>
                    </figure>
                    <div class="card-body p-4">
                        @if($product->category)
                            <span class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-bold w-fit">{{ $product->category->name }}</span>
                        @endif
                        <h3 class="font-black text-slate-950 text-sm leading-snug mt-1">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                        </h3>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-black text-pink-600">
                                {{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 0) : 'Get quote' }}
                            </span>
                            <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : $product->quoteRequestUrl() }}"
                               class="btn btn-xs btn-neutral font-black">
                                {{ $product->hasAvailablePrice() ? 'Order' : 'Quote' }}
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach

            {{-- Shop products --}}
            @foreach($shopProducts as $shopProduct)
                <article class="card bg-white border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group relative">
                    @if($shopProduct->isOnSale())
                        <div class="absolute top-3 left-3 z-10">
                            <span class="badge badge-sm bg-pink-600 border-0 text-white font-black">Sale</span>
                        </div>
                    @endif
                    <figure class="h-44 overflow-hidden bg-slate-100">
                        <a href="{{ route('shop.show', $shopProduct) }}" class="block h-full">
                            <img src="{{ $shopProduct->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                 alt="{{ $shopProduct->name }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        </a>
                    </figure>
                    <div class="card-body p-4">
                        <span class="badge badge-sm bg-emerald-100 text-emerald-700 border-0 font-bold w-fit">Buy Now</span>
                        <h3 class="font-black text-slate-950 text-sm leading-snug mt-1">
                            <a href="{{ route('shop.show', $shopProduct) }}" class="hover:text-pink-600 transition">{{ $shopProduct->name }}</a>
                        </h3>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <span class="text-sm font-black text-pink-600">NGN {{ number_format($shopProduct->currentPrice(), 0) }}</span>
                                @if($shopProduct->isOnSale())
                                    <span class="text-xs text-slate-400 line-through ml-1">{{ number_format((float)$shopProduct->price, 0) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('shop.show', $shopProduct) }}" class="btn btn-xs bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Buy</a>
                        </div>
                    </div>
                </article>
            @endforeach

        </div>
    </div>
</section>
@endif
