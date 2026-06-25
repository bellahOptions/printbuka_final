@extends('layouts.new-app')

@section('title', 'Product Categories | Printbuka')
@section('meta_description', 'Explore all Printbuka product categories — business cards, flyers, branded gifts, UV-DTF, DTF, laser engraving and more.')

@section('content')
<main>

    {{-- ===== HERO ===== --}}
    <section class="bg-[#EC268F] overflow-hidden" style="min-height: 320px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <div>
                <span class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-5 border border-white/30">
                    <span class="w-2 h-2 rounded-full bg-white"></span>
                    Product Catalog
                </span>
                <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-3">
                    Browse by Category
                </h1>
                <p class="text-white/80 text-lg max-w-xl">
                    Everything you need to print, brand and gift — organised for quick ordering.
                    @if(isset($categories) && $categories->isNotEmpty())
                        <span class="text-white font-bold">{{ $categories->count() }} {{ \Illuminate\Support\Str::plural('category', $categories->count()) }} available.</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-white text-[#EC268F] text-sm font-black px-5 py-3 rounded-xl hover:bg-pink-50 transition-colors">
                    <x-heroicon-o-tag class="w-4 h-4" />
                    Browse All Products
                </a>
                <a href="{{ route('shop.index') }}"
                   class="inline-flex items-center gap-2 bg-white/15 border border-white/30 text-white text-sm font-black px-5 py-3 rounded-xl hover:bg-white/25 transition-colors">
                    <x-heroicon-o-shopping-bag class="w-4 h-4" />
                    Ready-Made Shop
                </a>
            </div>
        </div>
    </section>

    {{-- ===== CATEGORIES GRID ===== --}}
    <section class="py-16 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(isset($categories) && $categories->isNotEmpty())

                @php
                    $catFallbacks = [
                        'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=900&q=80',
                    ];
                @endphp

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        @php
                            $catImage     = $category->imageUrl() ?: $catFallbacks[$loop->index % count($catFallbacks)];
                            $catSummary   = $category->description ?: 'Explore print and branded products in this category.';
                            $productCount = (int) ($category->active_products_count ?? 0);
                        @endphp

                        <a href="{{ route('products.category', $category) }}"
                           class="group relative rounded-3xl overflow-hidden border border-slate-100 hover:border-pink-200 hover:shadow-2xl transition-all duration-300 bg-white flex flex-col">

                            {{-- Image --}}
                            <div class="relative h-52 overflow-hidden bg-slate-100 shrink-0">
                                <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-600"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                                {{-- Product count badge --}}
                                <div class="absolute top-4 right-4">
                                    <span class="bg-white/90 backdrop-blur-sm text-slate-700 text-xs font-black px-2.5 py-1 rounded-full shadow-sm">
                                        {{ $productCount }} {{ \Illuminate\Support\Str::plural('product', $productCount) }}
                                    </span>
                                </div>

                                {{-- Category tag --}}
                                @if($category->tag)
                                <div class="absolute bottom-4 left-4">
                                    <span class="bg-[#EC268F] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                        {{ $category->tag }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="p-6 flex-1 flex flex-col">
                                <h2 class="text-lg font-black text-slate-950 mb-1 group-hover:text-[#EC268F] transition-colors">{{ $category->name }}</h2>
                                <p class="text-sm text-slate-500 leading-relaxed flex-1">{{ \Illuminate\Support\Str::limit($catSummary, 110) }}</p>

                                @if($category->children->isNotEmpty())
                                    <div class="mt-4 flex flex-wrap gap-1.5">
                                        @foreach($category->children->take(4) as $child)
                                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">
                                                {{ $child->name }}
                                            </span>
                                        @endforeach
                                        @if($category->children->count() > 4)
                                            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200">
                                                +{{ $category->children->count() - 4 }} more
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-5 flex items-center justify-between">
                                    <span class="text-sm font-black text-[#EC268F] flex items-center gap-1 group-hover:gap-2 transition-all">
                                        Browse category <x-heroicon-o-arrow-right class="w-4 h-4" />
                                    </span>
                                    <div class="w-8 h-8 rounded-full bg-pink-50 group-hover:bg-[#EC268F] flex items-center justify-center transition-colors">
                                        <x-heroicon-o-arrow-right class="w-4 h-4 text-[#EC268F] group-hover:text-white transition-colors" />
                                    </div>
                                </div>
                            </div>
                        </a>

                    @endforeach
                </div>

            @else
                <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-pink-50 flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-squares-2x2 class="w-8 h-8 text-pink-300" />
                    </div>
                    <p class="text-xl font-black text-slate-900">No categories yet.</p>
                    <p class="text-sm mt-2 text-slate-500">Products are being added. Check back shortly.</p>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== CTA BANNER ===== --}}
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 px-10 py-14 text-white text-center">
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute top-0 right-0 w-72 h-72 rounded-full bg-pink-600/10 -translate-y-1/2 translate-x-1/3 blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 w-60 h-60 rounded-full bg-cyan-500/10 translate-y-1/2 -translate-x-1/3 blur-2xl"></div>
                </div>
                <div class="relative">
                    <div class="inline-block bg-pink-600/20 text-pink-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-600/30 mb-5">
                        Ready to print?
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-black text-white mb-3">Can't find what you need?</h2>
                    <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Browse our full product catalog or contact our team — we'll help you find the right print solution for your brief.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 bg-[#EC268F] hover:bg-pink-700 text-white text-sm font-black px-7 py-3.5 rounded-xl transition-colors">
                            <x-heroicon-o-tag class="w-4 h-4" />
                            Browse All Products
                        </a>
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center gap-2 text-white text-sm font-black px-7 py-3.5 rounded-xl transition-colors"
                           style="border: 1px solid rgba(255,255,255,0.25);">
                            Get a Quote
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
