@extends('layouts.theme')

@section('title', 'All Products | Printbuka')

@section('content')
    @php
        $categories = [
            [
                'name' => 'Business Essentials',
                'tag' => 'Print',
                'description' => 'Business cards, letterheads, ID cards and office stationery.',
                'image' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Marketing Prints',
                'tag' => 'Campaigns',
                'description' => 'Flyers, posters, brochures, menus, catalogues and postcards.',
                'image' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Packaging',
                'tag' => 'Labels and bags',
                'description' => 'Stickers, labels, paper bags, courier bags and product sleeves.',
                'image' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Branded Gifts',
                'tag' => 'Core Service',
                'description' => 'Mugs, shirts, tote bags, notebooks, hampers and corporate gift sets.',
                'image' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Event Materials',
                'tag' => 'Events',
                'description' => 'Banners, roll-ups, tags, programmes and branded giveaways.',
                'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Large Format',
                'tag' => 'Outdoor',
                'description' => 'Posters, banners, signage and display prints for visibility.',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
            ],
        ];

        $productImages = [
            'business' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
            'flyer' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
            'sticker' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
            'brochure' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
            'letterhead' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
            'default' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80',
        ];

        $uvDtfProducts = $products->filter(function ($product) {
            $haystack = strtolower(trim($product->name.' '.($product->category?->name ?? '')));

            return str_contains($haystack, 'uv dtf') || str_contains($haystack, 'uv-dtf');
        })->values();

        $laserEngravingProducts = $products->filter(function ($product) {
            $haystack = strtolower(trim($product->name.' '.($product->category?->name ?? '')));

            return str_contains($haystack, 'laser engrav') || str_contains($haystack, 'laser');
        })->values();
    @endphp

    <main class="bg-white text-slate-900">
        <section class="bg-[#f4fbfb] py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:px-8">
                <div>
                    <p class="inline-flex rounded-md bg-white px-4 py-2 text-sm font-black text-pink-700 shadow-sm">All Products</p>
                    <h1 class="mt-5 max-w-3xl text-5xl leading-tight text-slate-950 sm:text-6xl">Choose what you want to print, brand or gift.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Browse Printbuka products across business printing, marketing materials, packaging, event materials and branded gifts.</p>
                    <div class="mt-8">
                        <livewire:product.search />
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="#catalog" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Browse Catalog</a>
                        <a href="#categories" class="rounded-md border border-slate-200 bg-white px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">View Categories</a>
                    </div>
                </div>

                <img
                    src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                    alt="Colourful printed brand materials"
                    class="h-[420px] w-full rounded-md object-cover shadow-2xl shadow-cyan-900/10"
                />
            </div>
        </section>

        <section id="categories" class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Categories</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Shop by what you need done.</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-6 text-slate-600">Gifts are a core Printbuka service, so they sit beside print and packaging as a major product line.</p>
                </div>

                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="#catalog" class="group overflow-hidden rounded-md border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                            <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div class="p-5">
                                <p class="text-xs font-black uppercase tracking-wide text-pink-700">{{ $category['tag'] }}</p>
                                <h3 class="mt-2 text-xl font-black text-slate-950">{{ $category['name'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $category['description'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="uv-dtf-products" class="bg-slate-950 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-300">UV DTF Products</p>
                        <h2 class="mt-2 text-4xl">Order UV DTF from Product Catalog</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Service-page orders for UV DTF are handled here so clients choose exact items and specs before checkout.</p>
                    </div>
                    <a href="#catalog" class="rounded-md border border-white/30 px-5 py-3 text-sm font-black transition hover:border-white">View Full Catalog</a>
                </div>

                @if ($uvDtfProducts->isNotEmpty())
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($uvDtfProducts as $product)
                            <article class="rounded-md border border-white/15 bg-white/5 p-5 backdrop-blur">
                                <h3 class="text-xl font-black text-white">{{ $product->name }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-300">{{ $product->short_description }}</p>
                                <p class="mt-4 text-xs font-bold uppercase tracking-wide text-cyan-300">Starting at</p>
                                <p class="mt-1 text-2xl font-black text-pink-300">NGN {{ number_format((float) $product->price, 2) }}</p>
                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="inline-flex justify-center rounded-md border border-white/20 px-4 py-3 text-sm font-black text-white transition hover:border-cyan-300 hover:text-cyan-200">View</a>
                                    <a href="{{ route('orders.create', $product) }}" class="inline-flex justify-center rounded-md bg-pink-600 px-4 py-3 text-sm font-black text-white transition hover:bg-pink-700">Order</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-8 rounded-md border border-dashed border-white/25 bg-white/5 p-8">
                        <p class="text-lg font-black text-white">No UV DTF products yet.</p>
                        <p class="mt-2 text-sm text-slate-300">Add products with “UV DTF” in name or category to auto-populate this section.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="laser-engraving-products" class="bg-[#f6f9ff] py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Laser Engraving Products</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Order Laser Engraving from Product Catalog</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Laser engraving requests are routed through products for better item-level selection and pricing control.</p>
                    </div>
                    <a href="#catalog" class="rounded-md border border-slate-300 px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">View Full Catalog</a>
                </div>

                @if ($laserEngravingProducts->isNotEmpty())
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($laserEngravingProducts as $product)
                            <article class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                                <h3 class="text-xl font-black text-slate-950">{{ $product->name }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                                <p class="mt-4 text-xs font-bold uppercase tracking-wide text-pink-700">Starting at</p>
                                <p class="mt-1 text-2xl font-black text-pink-700">NGN {{ number_format((float) $product->price, 2) }}</p>
                                <div class="mt-5 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="inline-flex justify-center rounded-md border border-slate-200 px-4 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">View</a>
                                    <a href="{{ route('orders.create', $product) }}" class="inline-flex justify-center rounded-md bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-pink-700">Order</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-8 rounded-md border border-dashed border-slate-300 bg-white p-8">
                        <p class="text-lg font-black text-slate-950">No Laser Engraving products yet.</p>
                        <p class="mt-2 text-sm text-slate-600">Add products with “Laser” or “Engraving” in name or category to auto-populate this section.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="catalog" class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Catalog</p>
                        <h2 class="mt-2 text-4xl text-slate-950">All available products.</h2>
                    </div>
                    <p class="text-sm font-bold text-slate-500">{{ $products->count() }} products listed</p>
                </div>

                @if ($products->isNotEmpty())
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($products as $product)
                            @php
                                $productName = strtolower($product->name);
                                $image = $productImages['default'];

                                foreach ($productImages as $keyword => $imageUrl) {
                                    if ($keyword !== 'default' && str_contains($productName, $keyword)) {
                                        $image = $imageUrl;
                                        break;
                                    }
                                }
                            @endphp

                            <article class="rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="h-48 w-full rounded-t-md object-cover" />
                                </a>
                                <div class="p-5">
                                    <h3 class="text-lg font-black text-slate-950">
                                        <a href="{{ route('products.show', $product) }}" class="transition hover:text-pink-700">{{ $product->name }}</a>
                                    </h3>
                                    <p class="mt-2 min-h-12 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-slate-500">
                                        <span class="rounded-md bg-slate-100 px-3 py-2">MOQ: {{ $product->moq }}</span>
                                        <span class="rounded-md bg-slate-100 px-3 py-2">{{ $product->paper_size }}</span>
                                        <span class="rounded-md bg-slate-100 px-3 py-2">{{ $product->paper_density }}</span>
                                    </div>
                                    <p class="mt-5 text-sm font-bold text-slate-500">starting at</p>
                                    <p class="text-2xl font-black text-pink-700">NGN {{ number_format($product->price, 2) }}</p>
                                    <div class="mt-5 grid grid-cols-2 gap-2">
                                        <a href="{{ route('products.show', $product) }}" class="inline-flex justify-center rounded-md border border-slate-200 px-4 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">View</a>
                                        <a href="{{ route('orders.create', $product) }}" class="inline-flex justify-center rounded-md bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-pink-700">Order</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-8 rounded-md border border-dashed border-slate-300 bg-white p-8 text-center">
                        <h3 class="text-2xl font-black text-slate-950">No products yet.</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">PLease check back later</p>                        
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection
