@extends('layouts.theme')
@section('title', 'Shop | Printbuka')
@section('meta_description', 'Shop physical products from Printbuka — branded gifts, accessories, and more. Add to cart and pay online.')
@section('content')
<main class="bg-base-100 text-base-content min-h-screen">

    <section class="bg-slate-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-500 mb-5">
                <a href="{{ route('home') }}" class="hover:text-slate-300 transition">Home</a>
                <span>/</span>
                <span class="text-slate-300">Shop</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight">Printbuka Shop</h1>
                    <p class="text-slate-400 text-sm mt-2">Physical products ready to order — choose options and pay online with Paystack.</p>
                </div>
                <a href="{{ route('shop.cart') }}" class="btn btn-sm btn-outline text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white font-black relative">
                    <x-heroicon-o-shopping-cart class="w-4 h-4" />
                    Cart
                    @php $cartCount = count(session()->get('shop.cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="badge badge-sm bg-pink-600 border-0 text-white absolute -top-2 -right-2">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>

            <form method="GET" action="{{ route('shop.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products…"
                       class="input input-bordered bg-white/10 text-white placeholder-slate-400 border-white/20 focus:border-pink-400 flex-1" />
                <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Search</button>
                @if(request('search'))
                    <a href="{{ route('shop.index') }}" class="btn btn-outline text-white border-white/25 font-black">Clear</a>
                @endif
            </form>
        </div>
    </section>

    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('status'))
                <div class="alert alert-success mb-6 font-bold">{{ session('status') }}</div>
            @endif

            @if($products->isEmpty())
                <div class="py-24 text-center">
                    <x-heroicon-o-shopping-bag class="w-16 h-16 text-slate-200 mx-auto mb-4" />
                    <p class="text-xl font-black text-slate-700">No products available yet.</p>
                    <p class="text-slate-400 mt-1">Check back soon or browse our print catalog.</p>
                    <a href="{{ route('products.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black mt-6">Browse Print Products</a>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $product)
                        <article class="card bg-white border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group">
                            <figure class="h-52 overflow-hidden bg-slate-100">
                                <a href="{{ route('shop.show', $product) }}">
                                    @if($product->featuredImageUrl())
                                        <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                             onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <x-heroicon-o-shopping-bag class="w-16 h-16 text-slate-200" />
                                        </div>
                                    @endif
                                </a>
                            </figure>
                            <div class="card-body p-5">
                                <div class="flex items-center justify-between mb-1">
                                    @if($product->isOnSale())
                                        <span class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-black">Sale</span>
                                    @elseif($product->is_featured)
                                        <span class="badge badge-sm bg-amber-100 text-amber-700 border-0 font-black">Featured</span>
                                    @else
                                        <span></span>
                                    @endif
                                    @if($product->manage_stock)
                                        <span class="text-xs font-bold {{ $product->isInStock() ? 'text-emerald-600' : 'text-red-500' }}">
                                            {{ $product->isInStock() ? 'In stock' : 'Out of stock' }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-black text-slate-950 text-base leading-snug">
                                    <a href="{{ route('shop.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                                </h3>
                                <p class="text-sm text-slate-500 line-clamp-2 mt-1">{{ $product->short_description }}</p>

                                <div class="mt-3">
                                    @if($product->isOnSale())
                                        <p class="text-xs font-bold text-slate-400 line-through">NGN {{ number_format((float)$product->price, 0) }}</p>
                                        <p class="text-xl font-black text-pink-600">NGN {{ number_format($product->currentPrice(), 0) }}</p>
                                    @else
                                        <p class="text-xs font-bold text-slate-400">from</p>
                                        <p class="text-xl font-black text-pink-600">NGN {{ number_format($product->currentPrice(), 0) }}</p>
                                    @endif
                                </div>

                                <div class="card-actions mt-4">
                                    <a href="{{ route('shop.show', $product) }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full">
                                        @if($product->optionGroups()->exists()) Choose Options @else Add to Cart @endif
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
