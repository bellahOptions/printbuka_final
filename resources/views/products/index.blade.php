@extends('layouts.theme')

@section('title', 'All Products | Printbuka')
@section('meta_description', 'Browse Printbuka products with live search and smart filters for cards, flyers, stickers, gifts, and more.')

@section('content')

@php
    $productImages = [
        'business' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
        'card' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
        'flyer' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
        'poster' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
        'sticker' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
        'label' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
        'brochure' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
        'menu' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
        'letterhead' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
        'envelope' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
        'mug' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'gift' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'shirt' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'tote' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
        'banner' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
        'event' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
        'default' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80',
    ];

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
    <section class="bg-base-200 py-16 lg:py-20 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[1fr_0.9fr] gap-12 items-center">
                <div>
                    <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-5">All Products</div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-950 leading-tight mb-5">
                        Choose what<br>you want to<br>
                        <span class="text-pink-600">print, brand</span><br>or gift.
                    </h1>
                    <p class="text-lg text-slate-500 leading-relaxed mb-8 max-w-lg">
                        Browse every Printbuka product in one continuous catalog with smart filters and faster discovery.
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
                    <div class="relative mt-4 ml-auto w-fit md:absolute md:-bottom-5 md:-right-4 md:mt-0 md:ml-0 bg-white rounded-2xl shadow-xl p-5 border border-slate-100 text-center min-w-[110px]">
                        <p class="text-3xl font-black text-pink-600">{{ $activeProductCount }}</p>
                        <p class="text-xs font-bold text-slate-500 mt-1">Products<br>Available</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="catalog" class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="badge badge-outline border-pink-300 text-pink-700 font-black mb-3">Full Catalog</div>
                <h2 class="text-4xl font-black text-slate-950">All products with smart filters and infinite loading.</h2>
            </div>

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
                            $catImage = $category->image ?? $productImages[$category->slug] ?? $productImages['default'];
                        @endphp
                        <a href="{{ route('products.category', $category) }}"
                           class="group card bg-base-100 border border-slate-200 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition {{ $colors['hover'] }} min-w-[240px] max-w-[240px] snap-start shrink-0">
                            <figure class="overflow-hidden h-32">
                                <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
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

    <section class="bg-slate-950 py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-white font-black text-2xl">Can't find what you're looking for?</p>
                    <p class="text-slate-400 text-sm mt-1">Our team handles custom jobs. Tell us what you need and we will help you out.</p>
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
