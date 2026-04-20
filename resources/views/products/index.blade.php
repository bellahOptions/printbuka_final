@extends('layouts.theme')

@section('title', 'All Products | Printbuka')

@section('content')

@php
    $productImages = [
        'business' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
        'card'     => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
        'flyer'    => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
        'poster'   => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
        'sticker'  => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
        'label'    => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
        'brochure' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
        'menu'     => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
        'letterhead' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
        'envelope' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
        'mug'      => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'gift'     => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'shirt'    => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'tote'     => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'banner'   => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
        'event'    => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
        'default'  => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80',
    ];

    $categoryData = [
        ['name' => 'Business Essentials', 'tag' => 'Print',          'color' => 'pink',    'description' => 'Cards, letterheads, ID cards and office stationery.',       'image' => $productImages['business']],
        ['name' => 'Marketing Prints',    'tag' => 'Campaigns',      'color' => 'cyan',    'description' => 'Flyers, posters, brochures, menus and postcards.',           'image' => $productImages['flyer']],
        ['name' => 'Packaging',           'tag' => 'Labels & Bags',  'color' => 'emerald', 'description' => 'Stickers, labels, paper bags, courier bags and sleeves.',    'image' => $productImages['sticker']],
        ['name' => 'Branded Gifts',       'tag' => 'Core Service',   'color' => 'amber',   'description' => 'Mugs, shirts, tote bags, hampers and corporate gift sets.',  'image' => $productImages['mug']],
        ['name' => 'Event Materials',     'tag' => 'Events',         'color' => 'violet',  'description' => 'Banners, roll-ups, name tags and branded giveaways.',        'image' => $productImages['banner']],
        ['name' => 'Large Format',        'tag' => 'Outdoor',        'color' => 'slate',   'description' => 'Signage, posters and large display prints for visibility.',  'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80'],
    ];

    $colorMap = [
        'pink'    => ['badge' => 'bg-pink-100 text-pink-700',    'hover' => 'hover:border-pink-300 hover:bg-pink-50'],
        'cyan'    => ['badge' => 'bg-cyan-100 text-cyan-700',    'hover' => 'hover:border-cyan-300 hover:bg-cyan-50'],
        'emerald' => ['badge' => 'bg-emerald-100 text-emerald-700', 'hover' => 'hover:border-emerald-300 hover:bg-emerald-50'],
        'amber'   => ['badge' => 'bg-amber-100 text-amber-700',  'hover' => 'hover:border-amber-300 hover:bg-amber-50'],
        'violet'  => ['badge' => 'bg-violet-100 text-violet-700','hover' => 'hover:border-violet-300 hover:bg-violet-50'],
        'slate'   => ['badge' => 'bg-slate-100 text-slate-700',  'hover' => 'hover:border-slate-300 hover:bg-slate-50'],
    ];

    $uvDtfProducts = $products->filter(fn($p) =>
        str_contains(strtolower($p->name . ' ' . ($p->category?->name ?? '')), 'uv dtf') ||
        str_contains(strtolower($p->name . ' ' . ($p->category?->name ?? '')), 'uv-dtf')
    )->values();

    $laserProducts = $products->filter(fn($p) =>
        str_contains(strtolower($p->name . ' ' . ($p->category?->name ?? '')), 'laser')
    )->values();
@endphp

<main class="bg-base-100 text-base-content">

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <section class="bg-base-200 py-16 lg:py-20 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[1fr_0.9fr] gap-12 items-center">
                <div>
                    <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-5">All Products</div>
                    <h1 class="text-5xl lg:text-6xl font-black text-slate-950 leading-tight mb-5">
                        Choose what<br>you want to<br>
                        <span class="text-pink-600">print, brand</span><br>or gift.
                    </h1>
                    <p class="text-lg text-slate-500 leading-relaxed mb-8 max-w-lg">
                        Browse every Printbuka product — from business printing and marketing materials to packaging, event supplies and branded gifts.
                    </p>
                    <div class="mb-8">
                        <livewire:product.search />
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="#catalog" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Browse Catalog</a>
                        <a href="#categories" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View Categories</a>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                        alt="Colourful printed brand materials"
                        class="w-full h-[420px] object-cover rounded-2xl shadow-2xl shadow-cyan-900/10"
                    />
                    {{-- Floating count badge --}}
                    <div class="absolute -bottom-5 -right-4 bg-white rounded-2xl shadow-xl p-5 border border-slate-100 text-center min-w-[110px]">
                        <p class="text-3xl font-black text-pink-600">{{ $products->count() }}</p>
                        <p class="text-xs font-bold text-slate-500 mt-1">Products<br>Available</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         CATEGORIES
    ═══════════════════════════════════════════ --}}
    <section id="categories" class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-3">Categories</div>
                    <h2 class="text-4xl font-black text-slate-950">Shop by what you need done.</h2>
                    <p class="text-slate-500 mt-2 max-w-xl">Gifts are a core Printbuka service, sitting beside print and packaging as a major product line.</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($categoryData as $cat)
                    @php $colors = $colorMap[$cat['color']]; @endphp
                    <a href="#catalog" class="group card bg-base-100 border border-slate-200 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition {{ $colors['hover'] }}">
                        <figure class="overflow-hidden h-48">
                            <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                        </figure>
                        <div class="card-body p-5">
                            <span class="badge badge-sm {{ $colors['badge'] }} border-0 font-black w-fit">{{ $cat['tag'] }}</span>
                            <h3 class="card-title font-black text-slate-950 mt-1">{{ $cat['name'] }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $cat['description'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         UV-DTF PRODUCTS
    ═══════════════════════════════════════════ --}}
    <section id="uv-dtf-products" class="bg-slate-950 py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
                <div>
                    <div class="badge badge-outline border-cyan-500 text-cyan-300 font-black mb-3">Specialist Service</div>
                    <h2 class="text-4xl font-black">UV DTF Products</h2>
                    <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">
                        UV-cured transfers that stick to almost any surface — glass, metal, plastic, wood. Crystal-clear finish that lasts. Order directly from the catalog below.
                    </p>
                </div>
                <a href="#catalog" class="btn btn-outline text-white border-white/25 hover:bg-white hover:text-slate-950 font-black shrink-0">View Full Catalog</a>
            </div>

            @if($uvDtfProducts->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($uvDtfProducts as $product)
                        <article class="card bg-white/5 border border-white/10 backdrop-blur hover:bg-white/10 hover:-translate-y-1 transition">
                            <div class="card-body p-6">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <h3 class="font-black text-white text-lg leading-snug">{{ $product->name }}</h3>
                                    @if($product->moq)
                                        <span class="badge badge-sm bg-cyan-900/60 text-cyan-300 border-0 font-bold shrink-0">MOQ {{ $product->moq }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-400 leading-relaxed mb-4">{{ $product->short_description }}</p>

                                @if($product->paper_size || $product->finishing)
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @if($product->paper_size)
                                            <span class="badge badge-sm bg-white/10 border-0 text-slate-300 font-bold">{{ $product->paper_size }}</span>
                                        @endif
                                        @if($product->finishing)
                                            <span class="badge badge-sm bg-white/10 border-0 text-slate-300 font-bold">{{ $product->finishing }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-auto">
                                    <p class="text-xs font-bold uppercase tracking-wide text-cyan-400 mb-1">Starting at</p>
                                    <p class="text-2xl font-black text-pink-300 mb-5">NGN {{ number_format((float) $product->price, 0) }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline text-white border-white/20 hover:border-cyan-300 hover:text-cyan-200 font-black">View</a>
                                        <a href="{{ route('orders.create', $product) }}" class="btn btn-sm bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Order</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-white/15 bg-white/5 p-12 text-center">
                    <div class="text-5xl mb-4">✨</div>
                    <p class="text-xl font-black text-white mb-2">No UV DTF products yet</p>
                    <p class="text-sm text-slate-400 max-w-sm mx-auto">Products with "UV DTF" or "UV-DTF" in the name or category will auto-populate here.</p>
                </div>
            @endif
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         LASER ENGRAVING PRODUCTS
    ═══════════════════════════════════════════ --}}
    <section id="laser-engraving-products" class="bg-base-200 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
                <div>
                    <div class="badge badge-outline border-amber-400 text-amber-600 font-black mb-3">Specialist Service</div>
                    <h2 class="text-4xl font-black text-slate-950">Laser Engraving Products</h2>
                    <p class="text-slate-500 mt-2 max-w-2xl text-sm leading-relaxed">
                        Precision mini laser engraving on wood, acrylic, leather and more. Perfect for personalised gifts, awards and branded keepsakes.
                    </p>
                </div>
                <a href="#catalog" class="btn btn-outline font-black border-slate-300 hover:border-pink-400 hover:text-pink-700 shrink-0">View Full Catalog</a>
            </div>

            @if($laserProducts->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($laserProducts as $product)
                        <article class="card bg-base-100 border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition">
                            <div class="card-body p-6">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <h3 class="font-black text-slate-950 text-lg leading-snug">{{ $product->name }}</h3>
                                    @if($product->moq)
                                        <span class="badge badge-sm bg-amber-100 text-amber-700 border-0 font-bold shrink-0">MOQ {{ $product->moq }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 leading-relaxed mb-4">{{ $product->short_description }}</p>

                                @if($product->paper_size || $product->finishing)
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @if($product->paper_size)
                                            <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_size }}</span>
                                        @endif
                                        @if($product->finishing)
                                            <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->finishing }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-auto">
                                    <p class="text-xs font-bold uppercase tracking-wide text-pink-600 mb-1">Starting at</p>
                                    <p class="text-2xl font-black text-pink-600 mb-5">NGN {{ number_format((float) $product->price, 0) }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                        <a href="{{ route('orders.create', $product) }}" class="btn btn-sm btn-neutral font-black hover:bg-pink-700">Order</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-base-100 p-12 text-center">
                    <div class="text-5xl mb-4">⚡</div>
                    <p class="text-xl font-black text-slate-950 mb-2">No laser engraving products yet</p>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto">Products with "Laser" in the name or category will auto-populate here.</p>
                </div>
            @endif
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         FULL CATALOG
    ═══════════════════════════════════════════ --}}
    <section id="catalog" class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-3">Full Catalog</div>
                    <h2 class="text-4xl font-black text-slate-950">All available products.</h2>
                </div>
                <p class="text-sm font-bold text-slate-400 shrink-0">
                    {{ $products->count() }} {{ Str::plural('product', $products->count()) }} listed
                </p>
            </div>

            @if($products->isNotEmpty())

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $product)
                        @php
                            $pName = strtolower($product->name);
                            $img   = $productImages['default'];
                            foreach ($productImages as $kw => $url) {
                                if ($kw !== 'default' && str_contains($pName, $kw)) { $img = $url; break; }
                            }
                        @endphp

                        <article class="card bg-base-100 border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group">
                            <figure class="overflow-hidden h-48">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                </a>
                            </figure>
                            <div class="card-body p-5">

                                {{-- Category badge --}}
                                @if($product->category)
                                    <span class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-bold w-fit">{{ $product->category->name }}</span>
                                @endif

                                <h3 class="font-black text-slate-950 text-base leading-snug mt-1">
                                    <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                                </h3>

                                <p class="text-sm text-slate-500 leading-relaxed min-h-[3rem]">{{ $product->short_description }}</p>

                                {{-- Spec chips --}}
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @if($product->moq)
                                        <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">MOQ {{ $product->moq }}</span>
                                    @endif
                                    @if($product->paper_size)
                                        <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_size }}</span>
                                    @endif
                                    @if($product->paper_density)
                                        <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_density }}</span>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="mt-3">
                                    <p class="text-xs font-bold text-slate-400">starting at</p>
                                    <p class="text-xl font-black text-pink-600">
                                        NGN {{ number_format($product->price, 0) }}
                                        @if($product->moq)
                                            <span class="text-sm font-bold text-slate-400">/ {{ $product->moq }}</span>
                                        @endif
                                    </p>
                                </div>

                                {{-- CTAs --}}
                                <div class="card-actions mt-4 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                    <a href="{{ route('orders.create', $product) }}" class="btn btn-sm btn-neutral font-black hover:bg-pink-700">Order</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

            @else

                {{-- ══════════════════════════════
                     CREATIVE EMPTY STATE
                ══════════════════════════════ --}}
                <div class="py-24 flex flex-col items-center text-center">

                    {{-- Animated print illustration --}}
                    <div class="relative w-40 h-40 mb-8">
                        {{-- Outer ring --}}
                        <div class="absolute inset-0 rounded-full border-4 border-dashed border-pink-200 animate-spin" style="animation-duration: 12s;"></div>
                        {{-- Inner circle --}}
                        <div class="absolute inset-4 rounded-full bg-pink-50 flex items-center justify-center border-2 border-pink-100">
                            <div class="text-center">
                                {{-- Mini print icon made of divs --}}
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-10 h-7 rounded-t-lg border-2 border-pink-300 bg-white flex items-end justify-center pb-1">
                                        <div class="w-6 h-0.5 bg-pink-300 rounded"></div>
                                    </div>
                                    <div class="w-12 h-6 bg-pink-400 rounded-sm flex items-center justify-around px-1.5">
                                        <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                                        <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                                        <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                                    </div>
                                    <div class="w-10 h-4 rounded-b border-2 border-pink-300 bg-white flex items-center justify-center">
                                        <div class="w-5 h-0.5 bg-pink-200 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Orbiting dots --}}
                        <div class="absolute top-1 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-pink-400"></div>
                        <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-cyan-400"></div>
                        <div class="absolute left-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-amber-400"></div>
                        <div class="absolute right-1 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>

                    {{-- Dashed lines accent --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 border-t-2 border-dashed border-pink-200"></div>
                        <div class="badge bg-pink-100 text-pink-700 border-0 font-black text-xs">Coming Soon</div>
                        <div class="w-16 border-t-2 border-dashed border-pink-200"></div>
                    </div>

                    <h3 class="text-3xl font-black text-slate-950 mb-3">Our catalog is getting a fresh print run.</h3>
                    <p class="text-slate-500 max-w-md leading-relaxed mb-8">
                        Products are being loaded into the system. In the meantime, get a custom quote and our team will walk you through every option — from flyers to laser engraving.
                    </p>

                    {{-- Action cards --}}
                    <div class="grid sm:grid-cols-3 gap-4 w-full max-w-2xl mb-8">
                        <a href="{{ route('quotes.create') }}" class="card bg-pink-600 border-0 text-white hover:bg-pink-700 hover:-translate-y-1 transition shadow-lg shadow-pink-200">
                            <div class="card-body p-5 text-center">
                                <div class="text-2xl mb-2">📋</div>
                                <p class="font-black text-sm">Get a Quote</p>
                                <p class="text-xs text-pink-200 mt-1">Tell us what you need</p>
                            </div>
                        </a>
                        <a href="{{ route('services.index') }}" class="card bg-base-100 border border-slate-200 hover:border-cyan-300 hover:-translate-y-1 transition shadow-sm">
                            <div class="card-body p-5 text-center">
                                <div class="text-2xl mb-2">⚡</div>
                                <p class="font-black text-sm text-slate-950">View Services</p>
                                <p class="text-xs text-slate-400 mt-1">See what we offer</p>
                            </div>
                        </a>
                        <a href="{{ route('orders.track') }}" class="card bg-base-100 border border-slate-200 hover:border-emerald-300 hover:-translate-y-1 transition shadow-sm">
                            <div class="card-body p-5 text-center">
                                <div class="text-2xl mb-2">📦</div>
                                <p class="font-black text-sm text-slate-950">Track Order</p>
                                <p class="text-xs text-slate-400 mt-1">Check your order</p>
                            </div>
                        </a>
                    </div>

                    <p class="text-sm text-slate-400">
                        Questions? Call us on
                        <a href="tel:08035245784" class="font-black text-slate-700 hover:text-pink-600 transition">{{ $siteSettings['contact_phone'] ?? '08035245784' }}</a>
                    </p>
                </div>

            @endif
        </div>
    </section>


    {{-- ═══════════════════════════════════════════
         CTA STRIP
    ═══════════════════════════════════════════ --}}
    <section class="bg-slate-950 py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-white font-black text-2xl">Can't find what you're looking for?</p>
                    <p class="text-slate-400 text-sm mt-1">Our team handles custom jobs — just tell us what you need.</p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ route('quotes.create') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Get a Free Quote</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline text-white border-white/25 hover:bg-white hover:text-slate-950 font-black">View Services</a>
                </div>
            </div>
        </div>
    </section>

</main>

@endsection