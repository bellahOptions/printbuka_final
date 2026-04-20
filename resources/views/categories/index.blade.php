@extends('layouts.theme')

@section('title', 'Product Categories | Printbuka')

@section('content')
    <main class="min-h-screen bg-white text-slate-900">
        {{-- Hero Section --}}
        <section class="bg-gradient-to-br from-[#f4fbfb] to-white py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:gap-12 items-center">
                    <div class="flex flex-col justify-center">
                        <div class="inline-flex w-fit items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-black text-pink-700 shadow-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Product Categories
                        </div>
                        <h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Find the right print, <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-pink-500">packaging or gift</span> path.
                        </h1>
                        <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                            Browse Printbuka categories by job type, from everyday business stationery to branded gifts and event materials.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('products.index') }}" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-md shadow-pink-200">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                View All Products
                            </a>
                            <a href="{{ route('partners.create') }}" class="btn btn-outline border-slate-200 text-slate-700 hover:border-pink-300 hover:text-pink-600">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Partner With Us
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-pink-500/20 to-cyan-500/20 rounded-2xl blur-2xl"></div>
                        <img
                            src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                            alt="Printed brand materials arranged together"
                            class="relative h-[440px] w-full rounded-2xl object-cover shadow-2xl"
                        />
                    </div>
                </div>
            </div>
        </section>

        {{-- Categories Grid Section --}}
        <section class="py-16 lg:py-24 bg-slate-50/30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full bg-pink-100 px-4 py-1.5 text-sm font-medium text-pink-700 mb-4">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-pink-500"></span>
                        </span>
                        Shop by Category
                    </div>
                    <h2 class="text-3xl font-bold text-slate-950 sm:text-4xl">Browse Our Collections</h2>
                    <p class="mt-3 text-lg text-slate-600 max-w-2xl mx-auto">Discover quality printing solutions tailored to your needs</p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <article class="group card bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border border-slate-100">
                            {{-- Category Image --}}
                            <div class="relative h-56 overflow-hidden">
                                <img 
                                    src="{{ $category->image ?: 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80' }}" 
                                    alt="{{ $category->name }}" 
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="p-6">
                                {{-- Category Tag --}}
                                @if($category->tag)
                                    <p class="inline-flex text-xs font-black uppercase tracking-wide text-pink-600 bg-pink-50 rounded-full px-3 py-1">{{ $category->tag }}</p>
                                @endif
                                
                                {{-- Category Name --}}
                                <h3 class="mt-3 text-xl font-bold text-slate-900 group-hover:text-pink-600 transition-colors">
                                    {{ $category->name }}
                                </h3>
                                
                                {{-- Category Description --}}
                                <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-2">{{ $category->description }}</p>

                                {{-- Subcategories (Children) --}}
                                @if ($category->children->isNotEmpty())
                                    <div class="mt-4">
                                        <p class="text-xs font-semibold text-slate-500 mb-2">Subcategories:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($category->children as $childCategory)
                                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">{{ $childCategory->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Sample Products --}}
                                @if($category->products->isNotEmpty())
                                    <div class="mt-4">
                                        <p class="text-xs font-semibold text-slate-500 mb-2">Popular items:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($category->products->take(3) as $product)
                                                <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">{{ $product->name }}</span>
                                            @endforeach
                                            @if($category->products->count() > 3)
                                                <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-400">+{{ $category->products->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            More products coming soon
                                        </span>
                                    </div>
                                @endif

                                {{-- Browse Button --}}
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="btn btn-block mt-6 bg-slate-900 hover:bg-pink-600 border-0 text-white transition-all duration-300 group-hover:shadow-lg">
                                    Browse {{ $category->name }}
                                    <svg class="h-4 w-4 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Help Section (Use Case Guide) --}}
        <section class="py-16 lg:py-20 bg-gradient-to-r from-slate-900 to-slate-800 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-cyan-500/20 px-4 py-1.5 text-sm font-medium text-cyan-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Need help choosing?
                        </div>
                        <h2 class="mt-4 text-3xl font-bold sm:text-4xl">Start from the outcome, not the product name.</h2>
                        <p class="mt-4 text-slate-300">Tell us what you want to achieve, and we'll recommend the right print solution for your needs.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        {{-- Launching Card --}}
                        <div class="card bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 rounded-2xl p-5 border border-white/20">
                            <div class="h-12 w-12 rounded-xl bg-pink-500/20 flex items-center justify-center mb-4">
                                <svg class="h-6 w-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-white text-lg">Launching</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Use flyers, stickers, packaging and branded gifts.</p>
                        </div>

                        {{-- Hosting Card --}}
                        <div class="card bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 rounded-2xl p-5 border border-white/20">
                            <div class="h-12 w-12 rounded-xl bg-cyan-500/20 flex items-center justify-center mb-4">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-white text-lg">Hosting</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Use banners, programmes, name tags and giveaways.</p>
                        </div>

                        {{-- Gifting Card --}}
                        <div class="card bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all duration-300 rounded-2xl p-5 border border-white/20">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-4">
                                <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-white text-lg">Gifting</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Use mugs, shirts, gift sets and custom delivery packs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
                <div class="rounded-3xl bg-gradient-to-r from-pink-50 to-cyan-50 p-8 sm:p-12">
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Ready to start your print project?</h2>
                    <p class="mt-3 text-lg text-slate-600">Get a free quote or speak with our print experts today.</p>
                    <div class="mt-6 flex flex-wrap gap-4 justify-center">
                        <a href="{{ route('quotes.create') }}" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-lg shadow-pink-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                            Request a Quote
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline border-slate-200 text-slate-700 hover:border-pink-300 hover:text-pink-600">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection