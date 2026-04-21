@extends('layouts.theme')

@section('title', $category->name . ' | Printbuka')
@section('meta_description', \Illuminate\Support\Str::limit($category->description ?: ('Browse available '.strtolower($category->name).' products from Printbuka.'), 155))
@section('og_image', $category->imageUrl() ?: asset('logo.png'))

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('products.index') }}" class="hover:text-pink-600 transition">Products</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-slate-700">{{ $category->name }}</span>
        </div>

        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-xl">
            <div class="p-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="mb-3 inline-flex rounded-full bg-pink-500/20 px-3 py-1 text-xs font-bold text-pink-300">
                            {{ $category->tag ?? 'Category' }}
                        </div>
                        <h1 class="text-3xl font-black lg:text-4xl">{{ $category->name }}</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-300">{{ $category->description }}</p>
                        <p class="mt-4 text-sm text-slate-400">{{ $products->total() }} products available</p>
                    </div>
                    @if($category->imageUrl())
                        <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="h-32 w-32 rounded-2xl object-cover shadow-lg" />
                    @endif
                </div>
            </div>
        </div>

        @if($products->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <article class="group rounded-2xl border border-slate-100 bg-white shadow-md transition hover:-translate-y-1 hover:shadow-xl">
                        <figure class="h-48 overflow-hidden rounded-t-2xl">
                            <a href="{{ route('products.show', $product) }}">
                                <img src="{{ $product->featuredImageUrl() ?? 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80' }}"
                                     alt="{{ $product->name }}"
                                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            </a>
                        </figure>
                        <div class="p-5">
                            <h3 class="text-base font-bold text-slate-900">
                                <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                            </h3>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $product->short_description }}</p>
                            <div class="mt-3">
                                <p class="text-xs font-bold text-slate-400">starting at</p>
                                <p class="text-xl font-black text-pink-600">₦{{ number_format((float) $product->price, 0) }}</p>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                <a href="{{ route('orders.create', $product) }}" class="btn btn-sm border-0 bg-pink-600 text-white hover:bg-pink-700">Order</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @else
            <div class="py-20 text-center">
                <p class="font-medium text-slate-500">No active products in this category yet.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-lg border border-slate-200 px-4 py-2 text-sm font-black text-slate-700 hover:border-pink-400 hover:text-pink-700">
                    Browse all products
                </a>
            </div>
        @endif
    </div>
</main>
@endsection
