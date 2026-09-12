@extends('layouts.new-app')
@section('title', 'Shop | Printbuka — Branded Gifts & Ready-Made Products')
@section('meta_description', 'Shop branded gifts, accessories and print merchandise from Printbuka. Fixed prices, instant checkout, nationwide delivery.')

@section('content')
<main>

    {{-- ===== HERO ===== --}}
    <section class="relative overflow-hidden bg-gray-950" style="min-height:380px;">
       
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 flex flex-col lg:flex-row lg:items-center gap-12">
            <div class="flex-1">
                <p class="pb-eyebrow mb-3 text-brand-400">Fixed Prices, Instant Checkout</p>
                <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">
                    Printbuka <span class="pb-display-italic text-4xl sm:text-5xl">Shop</span>
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
                           class="text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ !request()->anyFilled(['featured','on_sale','search']) ? 'bg-brand-600 border-brand-600 text-white' : 'border-white/20 text-slate-300 hover:border-brand-400 hover:text-white' }}">
                            All Products
                        </a>
                        <a href="{{ route('shop.index', ['featured' => 1]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ request()->boolean('featured') ? 'bg-amber-500 border-amber-500 text-white' : 'border-white/20 text-slate-300 hover:border-amber-400 hover:text-white' }}">
                            <x-heroicon-s-star class="w-3.5 h-3.5" /> Featured
                        </a>
                        <a href="{{ route('shop.index', ['on_sale' => 1]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ request()->boolean('on_sale') ? 'bg-brand-600 border-brand-600 text-white' : 'border-white/20 text-slate-300 hover:border-brand-400 hover:text-white' }}">
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
                            <span class="inline-flex items-center gap-1 bg-brand-100 text-brand-700 text-xs font-black px-2.5 py-1 rounded-full">
                                On Sale
                                <a href="{{ route('shop.index', array_diff_key(request()->query(), ['on_sale' => ''])) }}" class="ml-0.5 hover:text-brand-900">&times;</a>
                            </span>
                        @endif
                        @if(request()->filled('search'))
                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 text-xs font-black px-2.5 py-1 rounded-full">
                                "{{ request('search') }}"
                                <a href="{{ route('shop.index', array_diff_key(request()->query(), ['search' => ''])) }}" class="ml-0.5 hover:text-slate-900">&times;</a>
                            </span>
                        @endif
                        <a href="{{ route('shop.index') }}" class="text-xs font-bold text-slate-400 hover:text-brand-600 transition-colors">Clear all</a>
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

            <livewire:shop.infinite-catalog :filters="$filters" />
        </div>
    </section>

    {{-- ===== BOTTOM CTA ===== --}}
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 px-10 py-12 text-center">
                
                <div class="relative">
                    <p class="pb-eyebrow mb-3 text-brand-400">Need Something Custom?</p>
                    <h2 class="pb-display text-3xl text-white mb-3">Can't find what you need?</h2>
                    <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Our custom print catalog has hundreds more options — business cards, flyers, banners, branded packaging and specialist services.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('products.index') }}" class="pb-cta-primary">
                            Browse Print Catalog
                        </a>
                        <a href="{{ route('services.index') }}" class="pb-cta border border-white/25 text-white hover:bg-white/10">
                            View Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
