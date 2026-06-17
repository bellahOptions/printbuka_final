@extends('layouts.theme')
@section('title', 'Printbuka | No. 1 Online Print Shop in Nigeria')
@section('meta_description', 'Order quality prints, branded gifts, UV-DTF, DTF, and laser engraving from Printbuka with nationwide delivery across Nigeria.')
@section('content')
<main class="bg-base-100 text-base-content">

    {{-- ===== HERO ===== --}}
    <section class="bg-slate-950 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-pink-600/8 rounded-full -translate-y-1/3 translate-x-1/3 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-500/8 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl pointer-events-none"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                <div>
                    <div class="badge badge-outline badge-lg text-pink-400 border-pink-700 font-black mb-6 inline-flex items-center gap-1.5">
                        <x-heroicon-o-map-pin class="w-3.5 h-3.5" /> Nigeria's #1 Online Print Shop
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                        Print. Brand.<br>
                        <span class="text-pink-400">Gift.</span>
                        Delivered.
                    </h1>
                    <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                        Business cards, flyers, stickers, branded gifts, UV-DTF, laser engraving and more — from one trusted print partner. Shipped to all 36 states in 3–7 days.
                    </p>

                    <div class="mb-8">
                        <livewire:product.search />
                    </div>

                    <div class="flex flex-wrap gap-3 mb-10">
                        <a href="{{ route('products.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 btn-lg font-black">Browse Products</a>
                        <a href="{{ route('quotes.create') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">Get a Free Quote</a>
                    </div>

                    <div class="grid grid-cols-3 gap-3 max-w-sm">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center">
                            <p class="text-2xl font-black text-white">15k+</p>
                            <p class="text-xs font-bold text-slate-400 mt-0.5">Orders Done</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center">
                            <p class="text-2xl font-black text-white">36</p>
                            <p class="text-xs font-bold text-slate-400 mt-0.5">States Served</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center">
                            <p class="text-2xl font-black text-white">24h</p>
                            <p class="text-xs font-bold text-slate-400 mt-0.5">File Review</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/40">
                        <img
                            src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                            alt="Colourful print materials"
                            class="w-full h-[480px] object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent"></div>
                    </div>

                    <div class="relative mt-4 md:absolute md:-bottom-5 md:-left-4 md:mt-0 bg-white rounded-2xl shadow-2xl p-5 max-w-[220px] border border-slate-100">
                        <div class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-black mb-2 inline-flex items-center gap-1">
                            <x-heroicon-s-fire class="w-3 h-3" /> Popular Now
                        </div>
                        <p class="text-xl font-black text-slate-950 leading-snug">Flyers from<br><span class="text-pink-600">NGN 35,000</span></p>
                        <p class="text-xs text-slate-500 mt-1">per 500 copies</p>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-neutral w-full mt-3 font-black">Order Now</a>
                    </div>

                    <div class="relative mt-3 ml-auto md:absolute md:-top-4 md:-right-4 md:mt-0 md:ml-0 bg-white rounded-2xl shadow-xl p-4 border border-slate-100 text-center w-fit">
                        <p class="text-2xl font-black text-emerald-600">✓</p>
                        <p class="text-xs font-black text-slate-700 mt-1">Free File<br>Checks</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== TRUST BAR ===== --}}
    <section class="bg-white border-b border-slate-100 py-5">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-x-6 gap-y-4">

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                        <x-heroicon-o-document-check class="w-5 h-5 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Free File Review</p>
                        <p class="text-xs text-slate-500">On every order</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-pink-100 flex items-center justify-center shrink-0">
                        <x-heroicon-o-truck class="w-5 h-5 text-pink-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Nationwide Delivery</p>
                        <p class="text-xs text-slate-500">All 36 states</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <x-heroicon-o-bolt class="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Express Production</p>
                        <p class="text-xs text-slate-500">3–7 working days</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-cyan-100 flex items-center justify-center shrink-0">
                        <x-heroicon-o-lock-closed class="w-5 h-5 text-cyan-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Secure Payments</p>
                        <p class="text-xs text-slate-500">Paystack &amp; bank transfer</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                        <x-heroicon-o-star class="w-5 h-5 text-violet-600" />
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">Quality Prints</p>
                        <p class="text-xs text-slate-500">Professional grade</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== PRODUCT CATEGORIES ===== --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="badge badge-outline text-pink-700 border-pink-300 font-black mb-3">Product Categories</div>
                    <h2 class="text-4xl font-black text-slate-950">Everything you need to print,<br class="hidden lg:block"> brand and gift.</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">All Categories</a>
            </div>

            @php
                $catFallbacks = [
                    'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=900&q=80',
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($homeCategories as $category)
                    @php
                        $catImage = $category->imageUrl() ?: $catFallbacks[$loop->index % count($catFallbacks)];
                        $catSummary = $category->description ?: 'Explore print and branded products in this category.';
                        $productCount = (int) ($category->active_products_count ?? 0);
                    @endphp
                    <a href="{{ route('products.category', $category) }}" class="group card bg-base-100 border border-slate-200 hover:-translate-y-1 hover:shadow-xl transition overflow-hidden">
                        <figure class="h-52 overflow-hidden bg-slate-100">
                            <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        </figure>
                        <div class="card-body p-5">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-black uppercase tracking-wide text-pink-600">{{ $category->tag ?: 'Category' }}</p>
                                <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $productCount }} {{ \Illuminate\Support\Str::plural('product', $productCount) }}</span>
                            </div>
                            <h3 class="card-title text-base font-black text-slate-950">{{ $category->name }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ \Illuminate\Support\Str::limit($catSummary, 95) }}</p>

                            @if($category->children->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($category->children->take(3) as $child)
                                        <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $child->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <p class="text-lg font-black text-slate-900">No product categories are available right now.</p>
                        <p class="text-sm mt-2 text-slate-500">Please check back shortly.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== SPECIALIST SERVICES ===== --}}
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div class="badge badge-outline text-cyan-700 border-cyan-400 font-black mb-3">Specialist Services</div>
                <h2 class="text-4xl font-black text-slate-950">Advanced print tech, available now.</h2>
                <p class="text-slate-500 mt-3 max-w-xl mx-auto">We go beyond standard printing. These specialist services are available directly through our product catalog.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <div class="card bg-white border border-slate-200 hover:shadow-lg transition hover:-translate-y-1">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center mb-4">
                            <x-heroicon-o-printer class="w-6 h-6 text-pink-600" />
                        </div>
                        <h3 class="font-black text-slate-950 text-lg">Direct Image Printing</h3>
                        <p class="text-sm text-slate-500 leading-relaxed mt-2">Vibrant full-colour prints directly onto your chosen substrate. Ideal for branded items, gifts and promotional materials.</p>
                        <div class="card-actions mt-4">
                            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">Learn More</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-slate-200 hover:shadow-lg transition hover:-translate-y-1">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center mb-4">
                            <x-heroicon-o-sparkles class="w-6 h-6 text-cyan-600" />
                        </div>
                        <h3 class="font-black text-slate-950 text-lg">UV-DTF Transfer</h3>
                        <p class="text-sm text-slate-500 leading-relaxed mt-2">UV-cured transfers that stick to almost any surface — glass, metal, plastic, wood. Crystal-clear finish that lasts.</p>
                        <div class="card-actions mt-4">
                            <a href="{{ route('services.show', 'uv-dtf') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-cyan-400 hover:text-cyan-700">Order Now</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-slate-200 hover:shadow-lg transition hover:-translate-y-1">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                            <x-heroicon-o-swatch class="w-6 h-6 text-emerald-600" />
                        </div>
                        <h3 class="font-black text-slate-950 text-lg">DTF Printing</h3>
                        <p class="text-sm text-slate-500 leading-relaxed mt-2">Direct-to-Film transfers for garments and fabric. No minimum order, full-colour, soft feel on any t-shirt or hoodie.</p>
                        <div class="card-actions mt-4">
                            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-emerald-400 hover:text-emerald-700">Learn More</a>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-slate-200 hover:shadow-lg transition hover:-translate-y-1">
                    <div class="card-body p-6">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-4">
                            <x-heroicon-o-bolt class="w-6 h-6 text-amber-600" />
                        </div>
                        <h3 class="font-black text-slate-950 text-lg">Laser Engraving</h3>
                        <p class="text-sm text-slate-500 leading-relaxed mt-2">Precision mini laser engraving on wood, acrylic, leather, keyrings and more. Perfect for personalised gifts and awards.</p>
                        <div class="card-actions mt-4">
                            <a href="{{ route('services.show', 'laser-engraving') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-amber-400 hover:text-amber-700">Order Now</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== TESTIMONIALS (moved up before products) ===== --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div class="badge badge-outline text-pink-700 border-pink-300 font-black mb-3">Customer Reviews</div>
                <h2 class="text-4xl font-black text-slate-950">Trusted by businesses across Nigeria.</h2>
                <p class="text-slate-500 mt-3">Real feedback from clients who order with us regularly.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-6">

                <div class="card bg-white border border-slate-200 shadow-sm">
                    <div class="card-body p-6">
                        <div class="flex gap-0.5 mb-3">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"This print shop exhibits professionalism in all senses. They are reliable and they deliver promptly. They pay close attention to details when it comes to printing."</p>
                        <div class="flex items-center gap-3 mt-5">
                            <div class="avatar placeholder">
                                <div class="bg-pink-100 text-pink-700 rounded-full w-9">
                                    <span class="text-xs font-black">KG</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-950">KGS Client</p>
                                <p class="text-xs text-slate-400">Yearbook Order</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-slate-200 shadow-sm">
                    <div class="card-body p-6">
                        <div class="flex gap-0.5 mb-3">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"Quality work, fast turnaround, and the team actually managed my design too. Printbuka is my go-to print shop for everything business-related."</p>
                        <div class="flex items-center gap-3 mt-5">
                            <div class="avatar placeholder">
                                <div class="bg-cyan-100 text-cyan-700 rounded-full w-9">
                                    <span class="text-xs font-black">AB</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-950">Adaeze B.</p>
                                <p class="text-xs text-slate-400">Business Cards + Flyers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-slate-200 shadow-sm">
                    <div class="card-body p-6">
                        <div class="flex gap-0.5 mb-3">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"Ordered branded mugs for a corporate event and they came out perfect. Delivery was on time. Highly recommend Printbuka for any gifting or print need."</p>
                        <div class="flex items-center gap-3 mt-5">
                            <div class="avatar placeholder">
                                <div class="bg-emerald-100 text-emerald-700 rounded-full w-9">
                                    <span class="text-xs font-black">TK</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-950">Tunde K.</p>
                                <p class="text-xs text-slate-400">Branded Mugs Order</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== FEATURED PRODUCTS ===== --}}
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="badge badge-outline text-pink-700 border-pink-300 font-black mb-3">Featured Products</div>
                    <h2 class="text-4xl font-black text-slate-950">Handpicked picks for quick ordering.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">See All Products</a>
            </div>

            @if($featuredProducts->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($featuredProducts as $product)
                        @php
                            $image = $product->featuredImageUrl() ?? asset('img/product-placeholder.svg');
                        @endphp

                        <article class="card bg-white border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition">
                            <figure class="h-48 overflow-hidden">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                         onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                </a>
                            </figure>
                            <div class="card-body p-5">
                                @if($product->category)
                                    <a href="{{ route('products.category', $product->category) }}" class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-bold w-fit hover:bg-pink-200 transition">
                                        {{ $product->category->name }}
                                    </a>
                                @endif
                                <h3 class="card-title font-black text-slate-950 text-base leading-snug mt-1">
                                    <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                                </h3>
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $product->short_description }}</p>

                                @if($product->moq)
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">MOQ: {{ $product->moq }}</span>
                                        @if($product->paper_size)
                                            <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_size }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <p class="text-xs font-bold text-slate-400">{{ $product->hasAvailablePrice() ? 'starting at' : 'pricing' }}</p>
                                    <p class="text-xl font-black text-pink-600">
                                        {{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 0) : 'Request quote' }}
                                        @if($product->hasAvailablePrice() && $product->moq)
                                            <span class="text-sm font-bold text-slate-400">/ {{ $product->moq }}</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="card-actions mt-4 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                    <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : $product->quoteRequestUrl() }}" class="btn btn-sm btn-neutral font-black">{{ $product->hasAvailablePrice() ? 'Order' : 'Quote' }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <p class="text-lg font-black">No products available yet.</p>
                    <p class="text-sm mt-1">Check back soon.</p>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== POPULAR GIFT ITEMS ===== --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="badge badge-outline border-amber-300 text-amber-700 font-black mb-3">Popular Gift Items</div>
                    <h2 class="text-4xl font-black text-slate-950">Most viewed gift-ready products.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-amber-400 hover:text-amber-700 hover:bg-amber-50 shrink-0">Browse Gifts</a>
            </div>

            @if($popularGiftItems->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($popularGiftItems as $product)
                        <article class="card bg-white border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition">
                            <figure class="h-52 overflow-hidden">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}" alt="{{ $product->name }}"
                                         class="h-full w-full object-cover transition duration-500 hover:scale-105"
                                         onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                </a>
                            </figure>
                            <div class="card-body p-5">
                                <h3 class="card-title text-lg font-black text-slate-950">
                                    <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600">{{ $product->name }}</a>
                                </h3>
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $product->short_description }}</p>

                                <div class="mt-3">
                                    <p class="text-xs font-bold text-slate-400">{{ $product->hasAvailablePrice() ? 'starting at' : 'pricing' }}</p>
                                    <p class="text-xl font-black text-pink-600">
                                        {{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 0) : 'Request quote' }}
                                    </p>
                                </div>

                                <div class="card-actions mt-3 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                    <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : $product->quoteRequestUrl() }}" class="btn btn-sm btn-neutral font-black">{{ $product->hasAvailablePrice() ? 'Order' : 'Quote' }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                    <p class="text-xl font-black text-slate-900">Gift items will appear here automatically once added.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== SHOP PRODUCTS (buy now, no quote needed) ===== --}}
    @if(($featuredShopProducts ?? collect())->isNotEmpty())
    <section class="bg-white py-20 border-t border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="badge badge-outline text-emerald-700 border-emerald-400 font-black mb-3 inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop — Buy Now
                    </div>
                    <h2 class="text-4xl font-black text-slate-950">Ready-to-buy products.<br class="hidden lg:block"> <span class="text-pink-600">No quote needed.</span></h2>
                    <p class="text-slate-500 mt-3 max-w-xl">Fixed prices, instant checkout. Pick your options and pay securely via Paystack — delivered to your door.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">Browse Shop</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($featuredShopProducts as $shopProduct)
                    <article class="card bg-base-100 border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-xl transition overflow-hidden group">
                        <figure class="h-48 overflow-hidden bg-slate-100 relative">
                            <a href="{{ route('shop.show', $shopProduct) }}">
                                <img src="{{ $shopProduct->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                     alt="{{ $shopProduct->name }}"
                                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            </a>
                            @if($shopProduct->isOnSale())
                                <div class="absolute top-3 left-3">
                                    <span class="badge badge-sm bg-pink-600 border-0 text-white font-black">Sale</span>
                                </div>
                            @endif
                            @if(!$shopProduct->isInStock())
                                <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                    <span class="badge badge-lg bg-slate-900 text-white border-0 font-black">Out of Stock</span>
                                </div>
                            @endif
                        </figure>
                        <div class="card-body p-5">
                            <h3 class="card-title font-black text-slate-950 text-base leading-snug">
                                <a href="{{ route('shop.show', $shopProduct) }}" class="hover:text-pink-600 transition">{{ $shopProduct->name }}</a>
                            </h3>
                            @if($shopProduct->short_description)
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $shopProduct->short_description }}</p>
                            @endif
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xl font-black text-pink-600">NGN {{ number_format($shopProduct->currentPrice(), 0) }}</span>
                                @if($shopProduct->isOnSale())
                                    <span class="text-sm font-bold text-slate-400 line-through">NGN {{ number_format((float)$shopProduct->price, 0) }}</span>
                                @endif
                            </div>
                            <div class="card-actions mt-4">
                                <a href="{{ route('shop.show', $shopProduct) }}"
                                   class="btn btn-sm bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Buy Now
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    {{-- ===== HOW IT WORKS ===== --}}
    <section class="bg-slate-950 py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-14">
                <div class="badge badge-outline text-cyan-300 border-cyan-600 font-black mb-3">How It Works</div>
                <h2 class="text-4xl font-black">Order in 3 simple steps.</h2>
                <p class="text-slate-400 mt-3 max-w-lg mx-auto">From choosing your product to delivery at your door — we keep it fast, simple and stress-free.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-6">
                <div class="card bg-white text-slate-950 border-0">
                    <div class="card-body p-7">
                        <div class="w-12 h-12 rounded-xl bg-pink-600 flex items-center justify-center mb-5">
                            <span class="text-white font-black text-xl">1</span>
                        </div>
                        <h3 class="font-black text-xl mb-2">Choose Your Product</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Browse our full catalog and pick the product, size and quantity that works for your job.</p>
                    </div>
                </div>
                <div class="card bg-white text-slate-950 border-0">
                    <div class="card-body p-7">
                        <div class="w-12 h-12 rounded-xl bg-cyan-600 flex items-center justify-center mb-5">
                            <span class="text-white font-black text-xl">2</span>
                        </div>
                        <h3 class="font-black text-xl mb-2">Share Your Artwork</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Upload your design or describe what you need. Our team reviews your file within 24 hours — for free.</p>
                    </div>
                </div>
                <div class="card bg-white text-slate-950 border-0">
                    <div class="card-body p-7">
                        <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center mb-5">
                            <span class="text-white font-black text-xl">3</span>
                        </div>
                        <h3 class="font-black text-xl mb-2">We Print &amp; Deliver</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">We produce and ship your order nationwide within 3–7 working days. Track every step of the way.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('orders.track') }}" class="btn btn-outline text-white border-white/30 hover:bg-white hover:text-slate-950 font-black mr-3">Track an Order</a>
                <a href="{{ route('quotes.create') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Get a Free Quote</a>
            </div>

        </div>
    </section>

    {{-- ===== CTA BANNER ===== --}}
    <section class="py-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-950 rounded-3xl p-10 lg:p-16 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-pink-600/10 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-cyan-500/10 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>
                <div class="relative">
                    <div class="badge badge-outline text-cyan-300 border-cyan-700 font-black mb-4">20% off your first order</div>
                    <h2 class="text-4xl lg:text-5xl font-black mb-4">Ready to start printing?</h2>
                    <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Join 15,000+ businesses across Nigeria who trust Printbuka for quality prints, branded gifts and fast delivery.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('register') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black btn-lg">Create Free Account</a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline text-white border-white/30 hover:bg-white hover:text-slate-950 font-black btn-lg">Browse Products</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
