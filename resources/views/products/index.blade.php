@extends('layouts.theme')

@section('title', 'All Products | Printbuka')
@section('meta_description', 'Browse Printbuka products with live search and smart filters for cards, flyers, stickers, gifts, and more.')

@section('content')

@php
    $placeholder = asset('img/product-placeholder.svg');

    $colorMap = [
        'pink' => ['badge' => 'bg-pink-100 text-pink-700', 'hover' => 'hover:border-pink-300 hover:bg-pink-50'],
        'cyan' => ['badge' => 'bg-cyan-100 text-cyan-700', 'hover' => 'hover:border-cyan-300 hover:bg-cyan-50'],
        'emerald' => ['badge' => 'bg-emerald-100 text-emerald-700', 'hover' => 'hover:border-emerald-300 hover:bg-emerald-50'],
        'amber' => ['badge' => 'bg-amber-100 text-amber-700', 'hover' => 'hover:border-amber-300 hover:bg-amber-50'],
        'violet' => ['badge' => 'bg-violet-100 text-violet-700', 'hover' => 'hover:border-violet-300 hover:bg-violet-50'],
        'slate' => ['badge' => 'bg-slate-100 text-slate-700', 'hover' => 'hover:border-slate-300 hover:bg-slate-50'],
    ];
@endphp

<main class="bg-base-100 text-base-content">
    {{-- Compact dark header — gets users to the catalog faster --}}
    <section class="bg-slate-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-500 mb-5">
                <a href="{{ route('home') }}" class="hover:text-slate-300 transition">Home</a>
                <span>/</span>
                <span class="text-slate-300">Products</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight">Browse All Products</h1>
                    <p class="text-slate-400 text-sm mt-2">
                        <span class="font-black text-pink-400">{{ $activeProductCount }}</span> products across every category — filtered, sorted and ready to order.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="#categories" class="btn btn-sm btn-outline text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white font-black">Categories</a>
                    <a href="{{ route('shop.index') }}" class="btn btn-sm bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Shop Now</a>
                </div>
            </div>
            <livewire:product.search />
        </div>
    </section>

    <section id="catalog" class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <form method="GET" action="{{ route('products.index') }}" class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <label class="form-control">
                        <span class="label-text text-xs font-bold uppercase text-slate-500">Search</span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Product name or keyword" class="input input-bordered border-slate-200 w-full" />
                    </label>

                    <label class="form-control">
                        <span class="label-text text-xs font-bold uppercase text-slate-500">Category</span>
                        <select name="category" class="select select-bordered border-slate-200 w-full">
                            <option value="">All Categories</option>
                            @foreach($filterCategories as $filterCategory)
                                <option value="{{ $filterCategory->slug }}" @selected(($filters['category'] ?? '') === $filterCategory->slug)>
                                    {{ $filterCategory->parent ? $filterCategory->parent->name.' / '.$filterCategory->name : $filterCategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="form-control">
                        <span class="label-text text-xs font-bold uppercase text-slate-500">Sort By</span>
                        <select name="sort" class="select select-bordered border-slate-200 w-full">
                            <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Name (A-Z)</option>
                            <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name (Z-A)</option>
                            <option value="price_low_high" @selected(($filters['sort'] ?? '') === 'price_low_high')>Price (Low to High)</option>
                            <option value="price_high_low" @selected(($filters['sort'] ?? '') === 'price_high_low')>Price (High to Low)</option>
                            <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Newest</option>
                            <option value="most_viewed" @selected(($filters['sort'] ?? '') === 'most_viewed')>Most Viewed</option>
                        </select>
                    </label>

                    <label class="form-control">
                        <span class="label-text text-xs font-bold uppercase text-slate-500">Min Price (NGN)</span>
                        <input type="number" min="0" step="1" name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="input input-bordered border-slate-200 w-full" />
                    </label>

                    <label class="form-control">
                        <span class="label-text text-xs font-bold uppercase text-slate-500">Max Price (NGN)</span>
                        <input type="number" min="0" step="1" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="input input-bordered border-slate-200 w-full" />
                    </label>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Apply Filters</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline border-slate-300 hover:border-slate-400 font-black">Reset</a>
                </div>
            </form>

            <livewire:product.infinite-catalog :filters="$filters" />
        </div>
    </section>

    <section id="categories" class="pt-4 pb-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                <div>
                    <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-3">Categories</div>
                    <h2 class="text-4xl font-black text-slate-950">Quick category browse.</h2>
                    <p class="text-slate-500 mt-2 max-w-xl">Swipe across categories, then explore the full category directory.</p>
                </div>
                <a href="{{ route('categories.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black shrink-0">Explore Categories</a>
            </div>

            @if($categories->isNotEmpty())
                <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory">
                    @foreach($categories as $index => $category)
                        @php
                            $colorKeys = ['pink', 'cyan', 'emerald', 'amber', 'violet', 'slate'];
                            $colorKey = $colorKeys[$index % count($colorKeys)];
                            $colors = $colorMap[$colorKey];
                            $productCount = (int) ($category->active_products_count ?? 0);
                            $catImage = $category->imageUrl() ?: $placeholder;
                        @endphp
                        <a href="{{ route('products.category', $category) }}"
                           class="group card bg-base-100 border border-slate-200 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition {{ $colors['hover'] }} min-w-[240px] max-w-[240px] snap-start shrink-0">
                            <figure class="overflow-hidden h-32">
                                <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            </figure>
                            <div class="card-body p-4">
                                <div class="flex items-center justify-between">
                                    <span class="badge badge-sm {{ $colors['badge'] }} border-0 font-black">
                                        {{ $category->tag ?? ucfirst($colorKey) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400">{{ $productCount }} {{ Str::plural('product', $productCount) }}</span>
                                </div>
                                <h3 class="card-title font-black text-slate-950 mt-1">{{ $category->name }}</h3>
                                <p class="text-sm text-slate-500 leading-relaxed line-clamp-2">{{ $category->description ?? 'High-quality products in this category.' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-base-100 p-10 text-center">
                    <p class="text-lg font-black text-slate-900">No categories available right now.</p>
                    <p class="text-sm mt-2 text-slate-500">Please check back shortly.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== SHOP PRODUCTS — instant buy strip ===== --}}
    @if(($shopProducts ?? collect())->isNotEmpty())
    <section class="py-16 border-t border-slate-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                <div>
                    <div class="badge badge-outline text-emerald-700 border-emerald-400 font-black mb-3 inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop — Instant Checkout
                    </div>
                    <h2 class="text-3xl font-black text-slate-950">Fixed-price items. <span class="text-pink-600">Buy now, no quote needed.</span></h2>
                    <p class="text-slate-500 mt-2 max-w-xl">These products have a set price — choose your options and pay securely via Paystack.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black shrink-0">View All Shop Products</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($shopProducts as $shopItem)
                    <article class="card bg-base-100 border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group flex flex-row overflow-hidden">
                        <figure class="w-28 shrink-0 overflow-hidden bg-slate-100">
                            <a href="{{ route('shop.show', $shopItem) }}" class="block h-full">
                                <img src="{{ $shopItem->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                     alt="{{ $shopItem->name }}"
                                     class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            </a>
                        </figure>
                        <div class="p-4 flex flex-col justify-between flex-1 min-w-0">
                            <div>
                                <h3 class="font-black text-slate-950 text-sm leading-snug">
                                    <a href="{{ route('shop.show', $shopItem) }}" class="hover:text-pink-600 transition">{{ $shopItem->name }}</a>
                                </h3>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-base font-black text-pink-600">NGN {{ number_format($shopItem->currentPrice(), 0) }}</span>
                                    @if($shopItem->isOnSale())
                                        <span class="text-xs font-bold text-slate-400 line-through">{{ number_format((float)$shopItem->price, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('shop.show', $shopItem) }}"
                               class="btn btn-xs bg-pink-600 border-0 text-white hover:bg-pink-700 font-black mt-3 w-fit">
                                Buy Now
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="bg-slate-950 py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-white font-black text-2xl">Can't find what you're looking for?</p>
                    <p class="text-slate-400 text-sm mt-1">Our team handles custom jobs. Tell us what you need and we will help you out.</p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ route('services.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">View Services</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline text-white border-white/25 hover:bg-white hover:text-slate-950 font-black">View Services</a>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection
