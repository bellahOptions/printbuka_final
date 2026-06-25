@extends('layouts.new-app')
@section('title', 'Printbuka | No. 1 Online Print Shop in Nigeria')
@section('meta_description', 'Order quality prints, branded gifts, UV-DTF, DTF, and laser engraving from Printbuka with nationwide delivery across Nigeria.')
@push('head')
<style>
  .pb-hero-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 0.7s ease-in-out; pointer-events: none; }
  .pb-hero-slide.active { opacity: 1; pointer-events: auto; }
  @keyframes pb-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }
  .pb-float { animation: pb-float 5s ease-in-out infinite; }
  @keyframes pb-spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .pb-spin-slow { animation: pb-spin-slow 18s linear infinite; }
  @keyframes pb-scale-in { from { transform: scaleX(0); } to { transform: scaleX(1); } }
  .pb-progress-bar { transform-origin: left; animation: pb-scale-in 1.6s 0.4s ease-out both; }
  .pb-dot { transition: all 0.3s ease; }
  .pb-dot.active { width: 2rem; background-color: #db2777; }
  .pb-dot:not(.active) { width: 0.5rem; background-color: rgba(255,255,255,0.35); }
  .pb-dot:not(.active):hover { background-color: rgba(255,255,255,0.65); }
</style>
@endpush
@section('content')
<main class="bg-white text-slate-900">

    {{-- ===== HERO SLIDER ===== --}}
    <section class="relative bg-[#08001A] overflow-hidden" style="min-height: 640px;">

        {{-- Background blobs --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-64 -left-64 w-[700px] h-[700px] rounded-full bg-pink-700/10 blur-3xl"></div>
            <div class="absolute -bottom-48 -right-48 w-[600px] h-[600px] rounded-full bg-violet-700/10 blur-3xl"></div>
        </div>

        {{-- Slides --}}
        <div id="pb-hero" class="relative" style="min-height: 640px;">

            {{-- Slide 1 --}}
            <div class="pb-hero-slide active">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center" style="min-height: 640px;">
                    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center w-full py-20 lg:py-0">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-pink-600/20 text-pink-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6 border border-pink-600/30">
                                <span class="w-2 h-2 rounded-full bg-pink-400"></span>
                                Nigeria's #1 Print Company
                            </div>
                            <h1 class="text-4xl sm:text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-5">
                                The Leader in<br>Quality <span class="text-pink-400">Custom</span><br>Print Design
                            </h1>
                            <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                                Business cards, flyers, stickers, branded gifts, UV-DTF and laser engraving â€” from one trusted print partner. Shipped nationwide in 3â€“7 days.
                            </p>
                            <div class="mb-8">
                                <livewire:product.search />
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('products.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 btn-lg font-black px-7">
                                    Browse Products
                                </a>
                                <a href="{{ route('shop.index') }}" class="btn btn-lg font-black text-white border-white/20 hover:bg-white hover:text-slate-950 hover:border-white" style="background: transparent; border: 1px solid rgba(255,255,255,0.25);">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                        <div class="relative hidden lg:flex justify-center items-center">
                            <div class="pb-float relative z-10">
                                <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=760&q=80"
                                     alt="Premium print products"
                                     class="w-[420px] h-[380px] object-cover rounded-3xl shadow-2xl border border-white/10" />
                            </div>
                            {{-- Stats card floating --}}
                            <div class="absolute -bottom-4 -left-6 z-20 bg-white rounded-2xl shadow-2xl p-4 border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center shrink-0">
                                        <x-heroicon-s-fire class="w-5 h-5 text-pink-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400">Orders This Month</p>
                                        <p class="text-lg font-black text-slate-950">15,000+</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -top-4 -right-6 z-20 bg-white rounded-2xl shadow-xl p-4 border border-slate-100 text-center">
                                <p class="text-2xl font-black text-emerald-600">âœ“</p>
                                <p class="text-xs font-black text-slate-700 mt-0.5">Free File<br>Checks</p>
                            </div>
                            <div class="absolute top-1/2 -translate-y-1/2 -left-8 w-6 h-6 bg-pink-500/50 rounded-full blur-sm"></div>
                            <div class="absolute top-8 right-8 w-16 h-16 rounded-full border border-pink-500/30 pb-spin-slow"></div>
                            <div class="absolute bottom-14 right-4 w-8 h-8 bg-violet-500/30 rounded-lg rotate-12"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div class="pb-hero-slide">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center" style="min-height: 640px;">
                    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center w-full py-20 lg:py-0">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-cyan-600/20 text-cyan-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6 border border-cyan-600/30">
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                UV-DTF &amp; DTF Specialists
                            </div>
                            <h1 class="text-4xl sm:text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-5">
                                Advanced Print<br><span class="text-cyan-400">Technology</span><br>at Your Service
                            </h1>
                            <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                                UV-DTF transfers that bond to glass, metal, plastic and wood. DTF for garments with no minimum order. Crystal-clear finish that lasts years.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('services.index') }}" class="btn bg-cyan-600 border-0 text-white hover:bg-cyan-700 btn-lg font-black px-7">
                                    Explore Services
                                </a>
                                <a href="{{ route('products.index') }}" class="btn btn-lg font-black text-white border-white/20 hover:bg-white hover:text-slate-950 hover:border-white" style="background: transparent; border: 1px solid rgba(255,255,255,0.25);">
                                    Browse Products
                                </a>
                            </div>
                        </div>
                        <div class="relative hidden lg:flex justify-center items-center">
                            <div class="pb-float relative z-10">
                                <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=760&q=80"
                                     alt="UV-DTF printing technology"
                                     class="w-[420px] h-[380px] object-cover rounded-3xl shadow-2xl border border-white/10" />
                            </div>
                            <div class="absolute top-8 left-8 w-20 h-20 rounded-full border border-cyan-500/30 pb-spin-slow"></div>
                            <div class="absolute bottom-12 right-4 w-10 h-10 bg-cyan-600/30 rounded-lg -rotate-12"></div>
                            <div class="absolute top-1/2 right-0 w-5 h-5 bg-cyan-400/50 rounded-full blur-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div class="pb-hero-slide">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center" style="min-height: 640px;">
                    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center w-full py-20 lg:py-0">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6 border border-amber-500/30">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                Branded Gifts &amp; Merchandise
                            </div>
                            <h1 class="text-4xl sm:text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-5">
                                Personalised<br><span class="text-amber-400">Gifts</span> for Every<br>Occasion
                            </h1>
                            <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                                Branded mugs, t-shirts, tote bags, keyrings and laser-engraved awards. Perfect for corporate events, staff gifts and brand activations.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('products.index') }}" class="btn bg-amber-500 border-0 text-white hover:bg-amber-600 btn-lg font-black px-7">
                                    Shop Gifts
                                </a>
                                <a href="{{ route('shop.index') }}" class="btn btn-lg font-black text-white border-white/20 hover:bg-white hover:text-slate-950 hover:border-white" style="background: transparent; border: 1px solid rgba(255,255,255,0.25);">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                        <div class="relative hidden lg:flex justify-center items-center">
                            <div class="pb-float relative z-10">
                                <img src="https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=760&q=80"
                                     alt="Branded gift items"
                                     class="w-[420px] h-[380px] object-cover rounded-3xl shadow-2xl border border-white/10" />
                            </div>
                            <div class="absolute top-8 right-12 w-16 h-16 rounded-full border border-amber-500/30 pb-spin-slow"></div>
                            <div class="absolute bottom-8 left-12 w-8 h-8 bg-amber-500/30 rounded-full"></div>
                            <div class="absolute top-1/3 left-0 w-4 h-4 bg-amber-400/50 rounded-full blur-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /#pb-hero --}}

        {{-- Dot navigation --}}
        <div class="absolute bottom-7 left-1/2 -translate-x-1/2 flex items-center gap-2 z-30">
            <button class="pb-dot active h-2 rounded-full" onclick="pbGoToSlide(0)" aria-label="Slide 1"></button>
            <button class="pb-dot h-2 rounded-full" onclick="pbGoToSlide(1)" aria-label="Slide 2"></button>
            <button class="pb-dot h-2 rounded-full" onclick="pbGoToSlide(2)" aria-label="Slide 3"></button>
        </div>

    </section>

    {{-- ===== TRUST BAR ===== --}}
    <section class="bg-white border-b border-slate-100 py-5 shadow-sm">
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
                        <p class="text-xs text-slate-500">3â€“7 working days</p>
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

    {{-- ===== ABOUT / WHY CHOOSE US ===== --}}
    <section class="py-20 lg:py-28 bg-white overflow-hidden">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="gap-14 lg:gap-20 items-center">

                {{-- Left: Icon feature boxes --}}
                <div>
                    <div class="inline-block bg-pink-50 text-pink-600 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-200 mb-5">
                        Why Choose Printbuka
                    </div>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-950 leading-tight mb-4">
                        Print smarter with<br>Nigeria's most trusted<br><span class="text-pink-600">print shop.</span>
                    </h2>
                    <p class="text-slate-500 leading-relaxed mb-10 max-w-lg">
                        From concept to delivery, we handle every step with precision. Thousands of businesses across Nigeria rely on us for quality and speed.
                    </p>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-pink-200 hover:bg-pink-50/50 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-document-check class="w-6 h-6 text-pink-600" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-950 mb-1">Free File Review</h3>
                                <p class="text-sm text-slate-500 leading-relaxed">Every design checked by our team before print â€” zero extra charge.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-cyan-200 hover:bg-cyan-50/50 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-cyan-600" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-950 mb-1">Expert Consultations</h3>
                                <p class="text-sm text-slate-500 leading-relaxed">Our print specialists help you choose the right finish for your job.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-truck class="w-6 h-6 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-950 mb-1">Nationwide Shipping</h3>
                                <p class="text-sm text-slate-500 leading-relaxed">Door-to-door delivery to all 36 states + FCT, 3â€“7 working days.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-amber-200 hover:bg-amber-50/50 transition-colors">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-shield-check class="w-6 h-6 text-amber-600" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-950 mb-1">Quality Guarantee</h3>
                                <p class="text-sm text-slate-500 leading-relaxed">We reprint at no cost if quality falls below expectations. Always.</p>
                            </div>
                        </div>
                    </div>
                </div>

                                </div>
            </div>
        </div>
    </section>

    {{-- ===== PROMOTIONAL BANNERS ===== --}}
    <section class="py-10 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 gap-5">

                <a href="{{ route('products.index') }}" class="group relative rounded-3xl overflow-hidden block h-60 sm:h-72">
                    <img src="https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=900&q=80"
                         alt="Custom print services"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" />
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/50 to-transparent"></div>
                    <div class="absolute inset-0 p-8 flex flex-col justify-end">
                        <span class="inline-block bg-pink-600 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full mb-3 w-fit">
                            Custom Printing
                        </span>
                        <h3 class="text-2xl font-black text-white leading-snug mb-2">Business Cards,<br>Flyers &amp; More</h3>
                        <p class="text-white/70 text-sm">Professional prints from NGN 5,000 â†’</p>
                    </div>
                </a>

                <a href="{{ route('shop.index') }}" class="group relative rounded-3xl overflow-hidden block h-60 sm:h-72">
                    <img src="https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=900&q=80"
                         alt="Branded gifts and merchandise"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" />
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/50 to-transparent"></div>
                    <div class="absolute inset-0 p-8 flex flex-col justify-end">
                        <span class="inline-block bg-amber-500 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full mb-3 w-fit">
                            Shop â€” Buy Now
                        </span>
                        <h3 class="text-2xl font-black text-white leading-snug mb-2">Branded Gifts &amp;<br>Merchandise</h3>
                        <p class="text-white/70 text-sm">Fixed prices, instant checkout â†’</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- ===== PRODUCT CATEGORIES ===== --}}
    <section class="py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="inline-block bg-pink-50 text-pink-600 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-200 mb-4">
                        Product Categories
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Everything you need to print,<br class="hidden lg:block"> brand and gift.</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">
                    All Categories â†’
                </a>
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

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($homeCategories as $category)
                    @php
                        $catImage = $category->imageUrl() ?: $catFallbacks[$loop->index % count($catFallbacks)];
                        $catSummary = $category->description ?: 'Explore print and branded products in this category.';
                        $productCount = (int) ($category->active_products_count ?? 0);
                    @endphp
                    <a href="{{ route('products.category', $category) }}"
                       class="group relative rounded-3xl overflow-hidden border border-slate-100 hover:border-pink-200 hover:shadow-xl transition-all duration-300 bg-white flex flex-col">
                        <div class="h-52 overflow-hidden bg-slate-100 shrink-0">
                            <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black uppercase tracking-wide text-pink-600">{{ $category->tag ?: 'Category' }}</span>
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $productCount }} {{ \Illuminate\Support\Str::plural('product', $productCount) }}</span>
                            </div>
                            <h3 class="text-base font-black text-slate-950 mb-1">{{ $category->name }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed flex-1">{{ \Illuminate\Support\Str::limit($catSummary, 95) }}</p>
                            @if($category->children->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($category->children->take(3) as $child)
                                        <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">{{ $child->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-4 flex items-center gap-1 text-sm font-black text-pink-600 group-hover:gap-2 transition-all">
                                Browse category <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-squares-2x2 class="w-8 h-8 text-slate-300" />
                        </div>
                        <p class="text-lg font-black text-slate-700">No product categories yet.</p>
                        <p class="text-sm mt-1 text-slate-400">Please check back shortly.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== SPECIALIST SERVICES ===== --}}
    <section class="py-20 bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div class="inline-block bg-pink-600/20 text-pink-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-600/30 mb-5">
                    Specialist Services
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white">Advanced print tech, available now.</h2>
                <p class="text-slate-400 mt-3 max-w-xl mx-auto">We go beyond standard printing. These specialist services are available directly through our product catalog.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="group rounded-2xl bg-slate-800/60 border border-slate-700/50 p-6 hover:bg-pink-600 hover:border-pink-500 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-pink-600/20 group-hover:bg-white/20 flex items-center justify-center mb-5 transition-colors">
                        <x-heroicon-o-printer class="w-6 h-6 text-pink-400 group-hover:text-white" />
                    </div>
                    <h3 class="font-black text-white text-lg mb-2">Direct Image Printing</h3>
                    <p class="text-sm text-slate-400 group-hover:text-white/80 leading-relaxed mb-5 transition-colors">Vibrant full-colour prints directly onto your substrate. Ideal for branded items, gifts and promos.</p>
                    <a href="{{ route('services.index') }}" class="text-sm font-black text-pink-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                        Learn More <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="group rounded-2xl bg-slate-800/60 border border-slate-700/50 p-6 hover:bg-cyan-600 hover:border-cyan-500 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-cyan-600/20 group-hover:bg-white/20 flex items-center justify-center mb-5 transition-colors">
                        <x-heroicon-o-sparkles class="w-6 h-6 text-cyan-400 group-hover:text-white" />
                    </div>
                    <h3 class="font-black text-white text-lg mb-2">UV-DTF Transfer</h3>
                    <p class="text-sm text-slate-400 group-hover:text-white/80 leading-relaxed mb-5 transition-colors">Bonds to glass, metal, plastic, wood. Crystal-clear, long-lasting UV-cured finish.</p>
                    <a href="{{ route('services.show', 'uv-dtf') }}" class="text-sm font-black text-cyan-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                        Order Now <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="group rounded-2xl bg-slate-800/60 border border-slate-700/50 p-6 hover:bg-emerald-600 hover:border-emerald-500 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600/20 group-hover:bg-white/20 flex items-center justify-center mb-5 transition-colors">
                        <x-heroicon-o-swatch class="w-6 h-6 text-emerald-400 group-hover:text-white" />
                    </div>
                    <h3 class="font-black text-white text-lg mb-2">DTF Printing</h3>
                    <p class="text-sm text-slate-400 group-hover:text-white/80 leading-relaxed mb-5 transition-colors">Direct-to-Film for garments and fabric. No minimum order, full-colour, soft feel.</p>
                    <a href="{{ route('services.index') }}" class="text-sm font-black text-emerald-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                        Learn More <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="group rounded-2xl bg-slate-800/60 border border-slate-700/50 p-6 hover:bg-amber-500 hover:border-amber-400 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 group-hover:bg-white/20 flex items-center justify-center mb-5 transition-colors">
                        <x-heroicon-o-bolt class="w-6 h-6 text-amber-400 group-hover:text-white" />
                    </div>
                    <h3 class="font-black text-white text-lg mb-2">Laser Engraving</h3>
                    <p class="text-sm text-slate-400 group-hover:text-white/80 leading-relaxed mb-5 transition-colors">Precision engraving on wood, acrylic, leather, keyrings. Perfect for personalised gifts.</p>
                    <a href="{{ route('services.show', 'laser-engraving') }}" class="text-sm font-black text-amber-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                        Order Now <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

        </div>
    </section>

    {{-- ===== FEATURED PRODUCTS ===== --}}
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Handpicked picks for quick ordering.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">See All Products â†’</a>
            </div>

            @if($featuredProducts->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($featuredProducts as $product)
                        @php
                            $image = $product->featuredImageUrl() ?? asset('img/product-placeholder.svg');
                        @endphp
                        <article class="group bg-white rounded-3xl border border-slate-100 hover:border-pink-200 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                            <div class="h-48 overflow-hidden bg-slate-100 shrink-0">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ $image }}" alt="{{ $product->name }}"
                                         class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                                         onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                </a>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                @if($product->category)
                                    <a href="{{ route('products.category', $product->category) }}"
                                       class="text-xs font-black text-pink-600 uppercase tracking-wide hover:text-pink-700 mb-2 w-fit">
                                        {{ $product->category->name }}
                                    </a>
                                @endif
                                <h3 class="font-black text-slate-950 text-base leading-snug flex-1">
                                    <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                                </h3>
                                <p class="text-sm text-slate-500 line-clamp-2 mt-1">{{ $product->short_description }}</p>
                                @if($product->moq)
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">MOQ: {{ $product->moq }}</span>
                                        @if($product->paper_size)
                                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $product->paper_size }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="mt-3">
                                    <p class="text-xs font-bold text-slate-400">{{ $product->hasAvailablePrice() ? 'starting at' : 'pricing' }}</p>
                                    <p class="text-xl font-black text-pink-600">
                                        {{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 0) : 'Contact us' }}
                                        @if($product->hasAvailablePrice() && $product->moq)
                                            <span class="text-sm font-bold text-slate-400">/ {{ $product->moq }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                    <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : route('products.show', $product) }}"
                                       class="btn btn-sm bg-slate-950 border-0 text-white hover:bg-pink-600 font-black transition-colors">
                                        {{ $product->hasAvailablePrice() ? 'Order' : 'View' }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 rounded-3xl border border-dashed border-slate-200 bg-white">
                    <p class="text-lg font-black text-slate-500">No products available yet.</p>
                    <p class="text-sm mt-1 text-slate-400">Check back soon.</p>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== TESTIMONIALS ===== --}}
    <section class="py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div class="inline-block bg-pink-50 text-pink-600 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-200 mb-5">
                    Customer Reviews
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Trusted by businesses across Nigeria.</h2>
                <p class="text-slate-500 mt-3">Real feedback from clients who order with us regularly.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-6">
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                    <div class="flex gap-0.5 mb-4">
                        @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed italic">"This print shop exhibits professionalism in all senses. They are reliable and deliver promptly. They pay close attention to details when it comes to printing."</p>
                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-200">
                        <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-black text-pink-700">KG</span>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-950">KGS Client</p>
                            <p class="text-xs text-slate-400">Yearbook Order</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                    <div class="flex gap-0.5 mb-4">
                        @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed italic">"Quality work, fast turnaround, and the team actually managed my design too. Printbuka is my go-to print shop for everything business-related."</p>
                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-200">
                        <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-black text-cyan-700">AB</span>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-950">Adaeze B.</p>
                            <p class="text-xs text-slate-400">Business Cards + Flyers</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                    <div class="flex gap-0.5 mb-4">
                        @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed italic">"Ordered branded mugs for a corporate event and they came out perfect. Delivery was on time. Highly recommend Printbuka for any gifting or print need."</p>
                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-200">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-black text-emerald-700">TK</span>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-950">Tunde K.</p>
                            <p class="text-xs text-slate-400">Branded Mugs Order</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ===== POPULAR GIFT ITEMS ===== --}}
    @if($popularGiftItems->isNotEmpty())
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="inline-block bg-amber-50 text-amber-700 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-amber-200 mb-4">
                        Popular Gift Items
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Most popular gift-ready products.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-amber-400 hover:text-amber-700 hover:bg-amber-50 shrink-0">Browse Gifts â†’</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($popularGiftItems as $product)
                    <article class="group bg-white rounded-3xl border border-slate-100 hover:border-amber-200 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                        <div class="h-52 overflow-hidden bg-slate-100 shrink-0">
                            <a href="{{ route('products.show', $product) }}">
                                <img src="{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}" alt="{{ $product->name }}"
                                     class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            </a>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="text-lg font-black text-slate-950 flex-1">
                                <a href="{{ route('products.show', $product) }}" class="hover:text-amber-600 transition">{{ $product->name }}</a>
                            </h3>
                            <p class="text-sm text-slate-500 line-clamp-2 mt-1">{{ $product->short_description }}</p>
                            <div class="mt-3">
                                <p class="text-xs font-bold text-slate-400">{{ $product->hasAvailablePrice() ? 'starting at' : 'pricing' }}</p>
                                <p class="text-xl font-black text-pink-600">
                                    {{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 0) : 'Contact us' }}
                                </p>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-amber-400 hover:text-amber-700">View</a>
                                <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : route('products.show', $product) }}"
                                   class="btn btn-sm bg-slate-950 border-0 text-white hover:bg-amber-600 font-black transition-colors">
                                    {{ $product->hasAvailablePrice() ? 'Order' : 'View' }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== SHOP PRODUCTS ===== --}}
    @if(($featuredShopProducts ?? collect())->isNotEmpty())
    <section class="py-20 bg-white border-t border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <div class="inline-block bg-emerald-50 text-emerald-700 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-emerald-200 mb-4 inline-flex items-center gap-2">
                        <x-heroicon-o-shopping-bag class="w-3.5 h-3.5" />
                        Shop â€” Buy Now
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Ready-to-buy products.<br class="hidden lg:block"> <span class="text-pink-600">No quote needed.</span></h2>
                    <p class="text-slate-500 mt-3 max-w-xl">Fixed prices, instant checkout. Pay securely via Paystack â€” delivered to your door.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 shrink-0">Browse Shop â†’</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($featuredShopProducts as $shopProduct)
                    <article class="group bg-white rounded-3xl border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                        <div class="h-48 overflow-hidden bg-slate-100 relative shrink-0">
                            <a href="{{ route('shop.show', $shopProduct) }}">
                                <img src="{{ $shopProduct->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                     alt="{{ $shopProduct->name }}"
                                     class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            </a>
                            @if($shopProduct->isOnSale())
                                <div class="absolute top-3 left-3">
                                    <span class="text-xs font-black text-white bg-pink-600 px-2 py-1 rounded-lg">Sale</span>
                                </div>
                            @endif
                            @if(!$shopProduct->isInStock())
                                <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                    <span class="font-black text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg text-sm shadow">Out of Stock</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-black text-slate-950 text-base leading-snug flex-1">
                                <a href="{{ route('shop.show', $shopProduct) }}" class="hover:text-pink-600 transition">{{ $shopProduct->name }}</a>
                            </h3>
                            @if($shopProduct->short_description)
                                <p class="text-sm text-slate-500 line-clamp-2 mt-1">{{ $shopProduct->short_description }}</p>
                            @endif
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xl font-black text-pink-600">NGN {{ number_format($shopProduct->currentPrice(), 0) }}</span>
                                @if($shopProduct->isOnSale())
                                    <span class="text-sm font-bold text-slate-400 line-through">NGN {{ number_format((float)$shopProduct->price, 0) }}</span>
                                @endif
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('shop.show', $shopProduct) }}"
                                   class="btn btn-sm w-full bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">
                                    <x-heroicon-o-shopping-bag class="w-4 h-4" />
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
    <section class="py-20 bg-slate-950 text-white relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full bg-pink-600/5 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full bg-cyan-600/5 blur-3xl"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">

            <div class="text-center mb-14">
                <div class="inline-block bg-white/5 text-cyan-300 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-white/10 mb-5">
                    How It Works
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white">Order in 3 simple steps.</h2>
                <p class="text-slate-400 mt-3 max-w-lg mx-auto">From choosing your product to delivery at your door â€” fast, simple and stress-free.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-5">
                <div class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-pink-600/40 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-pink-600 flex items-center justify-center mb-6">
                        <span class="text-white font-black text-2xl">1</span>
                    </div>
                    <h3 class="font-black text-white text-xl mb-3">Choose Your Product</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Browse our full catalog and pick the product, size and quantity that works for your job.</p>
                </div>
                <div class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-cyan-600/40 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-600 flex items-center justify-center mb-6">
                        <span class="text-white font-black text-2xl">2</span>
                    </div>
                    <h3 class="font-black text-white text-xl mb-3">Share Your Artwork</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Upload your design or describe what you need. Our team reviews your file within 24 hours â€” for free.</p>
                </div>
                <div class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-emerald-600/40 transition-all">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center mb-6">
                        <span class="text-white font-black text-2xl">3</span>
                    </div>
                    <h3 class="font-black text-white text-xl mb-3">We Print &amp; Deliver</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">We produce and ship your order nationwide within 3â€“7 working days. Track every step.</p>
                </div>
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('orders.track') }}" class="btn btn-outline text-white border-white/20 hover:bg-white hover:text-slate-950 font-black mr-3">Track an Order</a>
                <a href="{{ route('shop.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Shop Now</a>
            </div>

        </div>
    </section>

    {{-- ===== CTA BANNER ===== --}}
    <section class="py-20 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 p-10 lg:p-16 text-white text-center">
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute top-0 right-0 w-80 h-80 rounded-full bg-pink-600/10 -translate-y-1/2 translate-x-1/3 blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-cyan-500/10 translate-y-1/2 -translate-x-1/3 blur-2xl"></div>
                </div>
                <div class="relative">
                    <div class="inline-block bg-pink-600/20 text-pink-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-600/30 mb-5">
                        20% off your first order
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-black text-white mb-4">Ready to start printing?</h2>
                    <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Join 15,000+ businesses across Nigeria who trust Printbuka for quality prints, branded gifts and fast delivery.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('register') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black btn-lg px-8">Create Free Account</a>
                        <a href="{{ route('products.index') }}" class="btn btn-lg font-black text-white border-white/20 hover:bg-white hover:text-slate-950" style="background: transparent; border: 1px solid rgba(255,255,255,0.25);">Browse Products</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
(function () {
    var slides = document.querySelectorAll('.pb-hero-slide');
    var dots = document.querySelectorAll('.pb-dot');
    var current = 0;
    var timer;

    function goToSlide(index) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
        clearInterval(timer);
        timer = setInterval(nextSlide, 5500);
    }

    function nextSlide() {
        goToSlide(current + 1);
    }

    window.pbGoToSlide = goToSlide;
    timer = setInterval(nextSlide, 5500);
})();
</script>
@endsection
