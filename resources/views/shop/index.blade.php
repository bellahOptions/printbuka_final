@extends('layouts.new-app')
@section('title', 'Shop | Printbuka — Branded Gifts & Ready-Made Products')
@section('meta_description', 'Shop branded gifts, accessories and print merchandise from Printbuka. Fixed prices, instant checkout, nationwide delivery.')

@section('content')
<main>

    {{-- ===== HERO ===== --}}
    <section class="relative overflow-hidden bg-gray-950" style="min-height:380px;">
       
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 flex flex-col lg:flex-row lg:items-center gap-12">
            <div class="flex-1">
                <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">
                    Printbuka <span class="text-[#EC268F]">Shop</span>
                </h1>
                <p class="text-slate-400 text-lg max-w-lg leading-relaxed mb-8">
                    Branded gifts, print merchandise and accessories — fixed prices, no quoting needed. Pay securely via Paystack and get it delivered nationwide.
                </p>
                <div class="flex flex-wrap gap-6">
                    <div>
                        <p class="text-2xl font-black text-white">{{ $totalCount }}+</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Products</p>
                    </div>
                    <div class="border-l border-white/10 pl-6">
                        <p class="text-2xl font-black text-gray-400">{{ $saleCount }}</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">On Sale Now</p>
                    </div>
                    <div class="border-l border-white/10 pl-6">
                        <p class="text-2xl font-black text-gray-400">3–7</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Day Delivery</p>
                    </div>
                </div>
            </div>

            <div class="lg:w-[420px] shrink-0">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm">
                    
                    <livewire:product.search />
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('shop.index') }}"
                           class="text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ !request()->anyFilled(['featured','on_sale','search']) ? 'bg-[#EC268F] border-[#EC268F] text-white' : 'border-white/20 text-slate-300 hover:border-pink-400 hover:text-white' }}">
                            All Products
                        </a>
                        <a href="{{ route('shop.index', ['featured' => 1]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ request()->boolean('featured') ? 'bg-amber-500 border-amber-500 text-white' : 'border-white/20 text-slate-300 hover:border-amber-400 hover:text-white' }}">
                            <x-heroicon-s-star class="w-3.5 h-3.5" /> Featured
                        </a>
                        <a href="{{ route('shop.index', ['on_sale' => 1]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ request()->boolean('on_sale') ? 'bg-pink-600 border-pink-600 text-white' : 'border-white/20 text-slate-300 hover:border-pink-400 hover:text-white' }}">
                            <x-heroicon-s-fire class="w-3.5 h-3.5" /> On Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FILTER / SORT BAR ===== --}}
    <div class="sticky top-[65px] z-30 bg-white/95 backdrop-blur border-b border-slate-100 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 py-3 overflow-x-auto" style="scrollbar-width:none;">
                <div class="flex items-center gap-2 shrink-0">
                    @if(request()->anyFilled(['featured','on_sale','search']))
                        <span class="text-xs font-black text-slate-500 uppercase tracking-wide">Filters:</span>
                        @if(request()->boolean('featured'))
                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-black px-2.5 py-1 rounded-full">
                                Featured
                                <a href="{{ route('shop.index', array_diff_key(request()->query(), ['featured' => ''])) }}" class="ml-0.5 hover:text-amber-900">&times;</a>
                            </span>
                        @endif
                        @if(request()->boolean('on_sale'))
                            <span class="inline-flex items-center gap-1 bg-pink-100 text-pink-700 text-xs font-black px-2.5 py-1 rounded-full">
                                On Sale
                                <a href="{{ route('shop.index', array_diff_key(request()->query(), ['on_sale' => ''])) }}" class="ml-0.5 hover:text-pink-900">&times;</a>
                            </span>
                        @endif
                        @if(request()->filled('search'))
                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 text-xs font-black px-2.5 py-1 rounded-full">
                                "{{ request('search') }}"
                                <a href="{{ route('shop.index', array_diff_key(request()->query(), ['search' => ''])) }}" class="ml-0.5 hover:text-slate-900">&times;</a>
                            </span>
                        @endif
                        <a href="{{ route('shop.index') }}" class="text-xs font-bold text-slate-400 hover:text-pink-600 transition-colors">Clear all</a>
                    @else
                        <span class="text-sm font-bold text-slate-500">
                            {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0 ml-auto">
                    <span class="text-xs font-bold text-slate-400 hidden sm:block">Sort by:</span>
                    <div class="flex items-center gap-1">
                        @foreach(['featured' => 'Recommended', 'price_asc' => 'Price ↑', 'price_desc' => 'Price ↓', 'newest' => 'Newest', 'popular' => 'Popular'] as $val => $label)
                            <a href="{{ route('shop.index', array_merge(request()->query(), ['sort' => $val])) }}"
                               class="text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors {{ request('sort', 'featured') === $val ? 'bg-slate-950 text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PRODUCTS GRID ===== --}}
    <section class="py-12 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
                    {{ session('status') }}
                </div>
            @endif

            @if($products->isEmpty())
                <div class="py-24 text-center rounded-3xl border border-dashed border-slate-200 bg-white">
                    <x-heroicon-o-shopping-bag class="w-16 h-16 text-slate-200 mx-auto mb-4" />
                    <p class="text-xl font-black text-slate-700">No products found.</p>
                    <p class="text-slate-400 mt-1 mb-6">Try changing your filters or browse our full catalog.</p>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-6 py-3 rounded-xl transition-colors">
                        View All Products
                    </a>
                </div>
            @else

                @php $midPoint = (int) ceil($products->count() / 2); @endphp

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $i => $product)

                        {{-- Mid-page inline ad --}}
                        @if($i === $midPoint)
                            @php
                                $midAd = null;
                                try {
                                    $midAd = \App\Models\Advertisement::active()
                                        ->where('placement', 'inline_banner')
                                        ->orderBy('sort_order')
                                        ->first();
                                } catch (\Throwable $e) {}
                            @endphp
                            @if($midAd)
                                <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#EC268F] to-pink-700 p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg shadow-pink-200">
                                        <div class="pointer-events-none absolute inset-0">
                                            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
                                        </div>
                                        @if($midAd->image_url)
                                            <img src="{{ $midAd->image_url }}" alt="{{ $midAd->title }}"
                                                 class="relative z-10 h-20 w-20 rounded-xl object-cover shrink-0 ring-2 ring-white/30">
                                        @else
                                            <div class="relative z-10 w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                                <x-heroicon-o-megaphone class="w-7 h-7 text-white" />
                                            </div>
                                        @endif
                                        <div class="relative z-10 flex-1 text-center sm:text-left">
                                            <p class="text-white font-black text-xl leading-snug">{{ $midAd->title }}</p>
                                            @if($midAd->body)
                                                <p class="text-pink-100 text-sm mt-1 leading-relaxed">{{ $midAd->body }}</p>
                                            @endif
                                        </div>
                                        @if($midAd->cta_url && $midAd->cta_label)
                                            <a href="{{ $midAd->cta_url }}"
                                               class="relative z-10 shrink-0 inline-flex items-center gap-2 bg-white text-[#EC268F] text-sm font-black px-6 py-3 rounded-xl hover:bg-pink-50 transition-colors shadow-md">
                                                {{ $midAd->cta_label }}
                                                <x-heroicon-o-arrow-right class="w-4 h-4" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Product card --}}
                        <article class="group bg-white rounded-2xl border border-slate-100 hover:border-pink-200 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">

                            <div class="relative h-52 overflow-hidden bg-slate-100 shrink-0">
                                <a href="{{ route('shop.show', $product) }}">
                                    @if($product->featuredImageUrl())
                                        <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                             onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
                                            <x-heroicon-o-shopping-bag class="w-14 h-14 text-slate-200" />
                                        </div>
                                    @endif
                                </a>

                                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                    @if($product->isOnSale())
                                        <span class="text-[10px] font-black text-white bg-[#EC268F] px-2 py-0.5 rounded-full uppercase tracking-wide">Sale</span>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="text-[10px] font-black text-white bg-amber-500 px-2 py-0.5 rounded-full uppercase tracking-wide">Featured</span>
                                    @endif
                                </div>

                                @if($product->manage_stock && !$product->isInStock())
                                    <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px] flex items-center justify-center">
                                        <span class="font-black text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg text-sm shadow-sm">Out of Stock</span>
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <a href="{{ route('shop.show', $product) }}"
                                       class="bg-white text-slate-900 text-xs font-black px-4 py-2 rounded-xl translate-y-2 group-hover:translate-y-0 transition-transform duration-300 shadow-lg hover:bg-pink-600 hover:text-white">
                                        Quick View
                                    </a>
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-black text-slate-950 text-sm leading-snug mb-1 flex-1">
                                    <a href="{{ route('shop.show', $product) }}" class="hover:text-pink-600 transition line-clamp-2">{{ $product->name }}</a>
                                </h3>

                                @if($product->short_description)
                                    <p class="text-xs text-slate-500 line-clamp-1 mt-1 mb-3">{{ $product->short_description }}</p>
                                @endif

                                <div class="flex items-end gap-2 mt-auto">
                                    <span class="text-lg font-black text-[#EC268F]">NGN {{ number_format($product->currentPrice(), 0) }}</span>
                                    @if($product->isOnSale())
                                        <span class="text-xs font-bold text-slate-400 line-through mb-0.5">NGN {{ number_format((float)$product->price, 0) }}</span>
                                        @php $savePct = $product->price > 0 ? round((1 - $product->currentPrice() / $product->price) * 100) : 0; @endphp
                                        @if($savePct > 0)
                                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full mb-0.5">-{{ $savePct }}%</span>
                                        @endif
                                    @endif
                                </div>

                                @if($product->manage_stock)
                                    @if($product->isInStock())
                                        <p class="text-[10px] font-bold text-emerald-500 mt-1">✓ In stock</p>
                                    @else
                                        <p class="text-[10px] font-bold text-red-500 mt-1">✗ Out of stock</p>
                                    @endif
                                @endif

                                <a href="{{ route('shop.show', $product) }}"
                                   class="mt-4 inline-flex items-center justify-center gap-2 w-full bg-slate-950 hover:bg-[#EC268F] text-white text-sm font-black py-2.5 rounded-xl transition-colors duration-300">
                                    <x-heroicon-o-shopping-bag class="w-4 h-4" />
                                    @if($product->optionGroups()->exists()) Choose Options
                                    @elseif($product->manage_stock && !$product->isInStock()) Out of Stock
                                    @else Add to Cart
                                    @endif
                                </a>
                            </div>
                        </article>

                    @endforeach
                </div>

                @if($products->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @endif

            @endif
        </div>
    </section>

    {{-- ===== BOTTOM CTA ===== --}}
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 px-10 py-12 text-center">
                
                <div class="relative">
                    <h2 class="text-3xl font-black text-white mb-3">Can't find what you need?</h2>
                    <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Our custom print catalog has hundreds more options — business cards, flyers, banners, branded packaging and specialist services.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 bg-[#EC268F] hover:bg-pink-700 text-white text-sm font-black px-7 py-3.5 rounded-xl transition-colors">
                            Browse Print Catalog
                        </a>
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center gap-2 text-white text-sm font-black px-7 py-3.5 rounded-xl transition-colors"
                           style="border: 1px solid rgba(255,255,255,0.25);">
                            View Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
