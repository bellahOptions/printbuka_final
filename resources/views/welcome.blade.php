@extends('layouts.theme')
@section('title', 'Printbuka | No. 1 Online Print Shop in Nigeria')
@section('meta_description', 'Order quality prints, branded gifts, UV-DTF, DTF, and laser engraving from Printbuka with nationwide delivery across Nigeria.')
@section('content')
<main class="bg-base-100 text-base-content">

    {{-- ===== HERO CAROUSEL (5 slides) ===== --}}
    <section
        x-data="{
            current: 0,
            total: 5,
            timer: null,
            paused: false,
            init() {
                this.startTimer();
            },
            startTimer() {
                this.timer = setInterval(() => {
                    if (!this.paused) this.next();
                }, 5500);
            },
            go(i) {
                this.current = i;
                clearInterval(this.timer);
                this.startTimer();
            },
            next() { this.go((this.current + 1) % this.total); },
            prev() { this.go((this.current - 1 + this.total) % this.total); }
        }"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        class="relative overflow-hidden"
        style="min-height: 580px;"
    >

        {{-- ── SLIDE 1 · BRAND HERO ── --}}
        <div
            x-show="current === 0"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-950"
        >
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-pink-600/8 rounded-full -translate-y-1/3 translate-x-1/3 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-500/8 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl pointer-events-none"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 h-full flex items-center">
                <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                    <div>
                        <div class="badge badge-outline badge-lg text-pink-400 border-pink-700 font-black mb-6 inline-flex items-center gap-1.5">
                            <x-heroicon-o-map-pin class="w-3.5 h-3.5" /> Nigeria's #1 Online Print Shop
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                            Print. Brand.<br><span class="text-pink-400">Gift.</span> Delivered.
                        </h1>
                        <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                            Business cards, flyers, stickers, branded gifts, UV-DTF, laser engraving and more — from one trusted print partner. Shipped to all 36 states in 3–7 days.
                        </p>
                        <div class="mb-8"><livewire:product.search /></div>
                        <div class="flex flex-wrap gap-3 mb-10">
                            <a href="{{ route('products.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 btn-lg font-black">Browse Products</a>
                            <a href="{{ route('quotes.create') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">Get a Free Quote</a>
                        </div>
                        <div class="grid grid-cols-3 gap-3 max-w-sm">
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-2xl font-black text-white">15k+</p><p class="text-xs font-bold text-slate-400 mt-0.5">Orders Done</p></div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-2xl font-black text-white">36</p><p class="text-xs font-bold text-slate-400 mt-0.5">States Served</p></div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-2xl font-black text-white">24h</p><p class="text-xs font-bold text-slate-400 mt-0.5">File Review</p></div>
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/40">
                            <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80" alt="Print materials" class="w-full h-[420px] object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent"></div>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-2xl p-5 max-w-[200px] border border-slate-100">
                            <div class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-black mb-2 inline-flex items-center gap-1"><x-heroicon-s-fire class="w-3 h-3" /> Popular Now</div>
                            <p class="text-lg font-black text-slate-950 leading-snug">Flyers from<br><span class="text-pink-600">NGN 35,000</span></p>
                            <a href="{{ route('products.index') }}" class="btn btn-xs btn-neutral w-full mt-3 font-black">Order Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SLIDE 2 · SHOP (buy now) ── --}}
        <div
            x-show="current === 1"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gradient-to-br from-pink-950 via-slate-950 to-slate-950"
            style="display:none;"
        >
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 70% 50%, #ec4899 0%, transparent 60%);"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 h-full flex items-center">
                <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                    <div>
                        <div class="badge badge-outline badge-lg text-pink-300 border-pink-700 font-black mb-6 inline-flex items-center gap-1.5">
                            <x-heroicon-o-shopping-bag class="w-3.5 h-3.5" /> Shop — Fixed Prices
                        </div>
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                            Buy Now.<br><span class="text-pink-400">No Quote</span> Needed.
                        </h2>
                        <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                            Browse ready-made products with fixed prices — pick your options and pay securely via Paystack. Delivered to your door, nationwide.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('shop.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 btn-lg font-black">Shop Now</a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">Browse All Products</a>
                        </div>
                        @if(($featuredShopProducts ?? collect())->isNotEmpty())
                        <div class="mt-10 flex gap-3 flex-wrap">
                            @foreach(($featuredShopProducts ?? collect())->take(3) as $sp)
                                <a href="{{ route('shop.show', $sp) }}" class="flex items-center gap-3 bg-white/10 border border-white/15 rounded-xl px-4 py-2 hover:bg-white/20 transition">
                                    @if($sp->featuredImageUrl())
                                        <img src="{{ $sp->featuredImageUrl() }}" alt="{{ $sp->name }}" class="w-9 h-9 rounded-lg object-cover shrink-0" />
                                    @endif
                                    <div>
                                        <p class="text-xs font-black text-white leading-snug">{{ \Illuminate\Support\Str::limit($sp->name, 22) }}</p>
                                        <p class="text-xs font-bold text-pink-400">NGN {{ number_format($sp->currentPrice(), 0) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="grid grid-cols-2 gap-4">
                            <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=600&q=80" class="rounded-2xl h-48 w-full object-cover shadow-2xl" alt="Shop products" />
                            <img src="https://images.unsplash.com/photo-1601924582970-9238bcb495d9?auto=format&fit=crop&w=600&q=80" class="rounded-2xl h-48 w-full object-cover shadow-2xl mt-8" alt="Branded gifts" />
                            <img src="https://images.unsplash.com/photo-1572635148818-ef6fd45eb394?auto=format&fit=crop&w=600&q=80" class="rounded-2xl h-48 w-full object-cover shadow-2xl -mt-8" alt="Mugs and prints" />
                            <img src="https://images.unsplash.com/photo-1612831456875-11f0bc7e07b5?auto=format&fit=crop&w=600&q=80" class="rounded-2xl h-48 w-full object-cover shadow-2xl" alt="Packaging" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SLIDE 3 · SPECIALIST SERVICES ── --}}
        <div
            x-show="current === 2"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950 to-cyan-950"
            style="display:none;"
        >
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 30% 60%, #06b6d4 0%, transparent 55%);"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 h-full flex items-center">
                <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                    <div>
                        <div class="badge badge-outline badge-lg text-cyan-300 border-cyan-700 font-black mb-6 inline-flex items-center gap-1.5">
                            <x-heroicon-o-sparkles class="w-3.5 h-3.5" /> Advanced Print Tech
                        </div>
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                            UV-DTF. DTF.<br><span class="text-cyan-400">Laser Engraving.</span>
                        </h2>
                        <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                            Specialist production services for surfaces, fabrics and personalised gifts. Crystal-clear UV transfers, full-colour DTF, and precision laser work — on demand.
                        </p>
                        <div class="flex flex-wrap gap-3 mb-8">
                            <a href="{{ route('services.index') }}" class="btn bg-cyan-600 border-0 text-white hover:bg-cyan-700 btn-lg font-black">Explore Services</a>
                            <a href="{{ route('quotes.create') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">Get a Free Quote</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3 max-w-md">
                            @foreach([['UV-DTF Transfer','Sticks to glass, metal, plastic','cyan'],['DTF Printing','Full-colour on any fabric','emerald'],['Laser Engraving','Wood, acrylic, leather','amber'],['Direct Image','Vibrant prints on gifts','pink']] as [$svc, $desc, $col])
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                <p class="text-xs font-black text-{{ $col }}-400 mb-1">{{ $svc }}</p>
                                <p class="text-xs text-slate-400">{{ $desc }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/50">
                            <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=1200&q=80" alt="UV-DTF printing" class="w-full h-[420px] object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent"></div>
                            <div class="absolute bottom-5 left-5">
                                <span class="badge badge-lg bg-cyan-500 border-0 text-white font-black">Instant quote available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SLIDE 4 · BRANDED GIFTS ── --}}
        <div
            x-show="current === 3"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gradient-to-br from-amber-950 via-slate-950 to-slate-950"
            style="display:none;"
        >
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 75% 40%, #f59e0b 0%, transparent 55%);"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 h-full flex items-center">
                <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                    <div>
                        <div class="badge badge-outline badge-lg text-amber-300 border-amber-700 font-black mb-6 inline-flex items-center gap-1.5">
                            <x-heroicon-o-gift class="w-3.5 h-3.5" /> Corporate Gifting
                        </div>
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                            Branded Gifts for<br><span class="text-amber-400">Every Occasion.</span>
                        </h2>
                        <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                            Mugs, t-shirts, keyrings, tote bags and more — printed with your logo and delivered to all 36 states. Minimum orders available for every budget.
                        </p>
                        <div class="flex flex-wrap gap-3 mb-8">
                            <a href="{{ route('products.index') }}" class="btn bg-amber-600 border-0 text-white hover:bg-amber-700 btn-lg font-black">Browse Gift Products</a>
                            <a href="{{ route('quotes.create') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">Get Bulk Pricing</a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['Branded Mugs','Custom T-Shirts','Tote Bags','Keyrings','Award Plaques','Notebooks'] as $gift)
                                <span class="badge badge-lg bg-white/10 border-white/15 text-white font-bold">{{ $gift }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/50">
                            <img src="https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=1200&q=80" alt="Branded gifts" class="w-full h-[420px] object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-amber-950/60 to-transparent"></div>
                            <div class="absolute bottom-5 left-5">
                                <span class="badge badge-lg bg-amber-500 border-0 text-white font-black">Available from 12 pcs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SLIDE 5 · PARTNER PROGRAMME ── --}}
        <div
            x-show="current === 4"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-slate-950 to-slate-950"
            style="display:none;"
        >
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 25% 55%, #10b981 0%, transparent 55%);"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 h-full flex items-center">
                <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                    <div>
                        <div class="badge badge-outline badge-lg text-emerald-300 border-emerald-700 font-black mb-6 inline-flex items-center gap-1.5">
                            <x-heroicon-o-users class="w-3.5 h-3.5" /> Partner Programme
                        </div>
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                            Grow Your Business<br><span class="text-emerald-400">with Printbuka.</span>
                        </h2>
                        <p class="text-lg text-slate-400 leading-relaxed mb-8 max-w-xl">
                            Join hundreds of entrepreneurs earning from referrals, reselling and white-label print services. No upfront cost — get your partner link today.
                        </p>
                        <div class="flex flex-wrap gap-3 mb-8">
                            <a href="{{ route('partners.create') }}" class="btn bg-emerald-600 border-0 text-white hover:bg-emerald-700 btn-lg font-black">Become a Partner</a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline btn-lg font-black text-white border-white/25 hover:bg-white hover:text-slate-950 hover:border-white">View Services</a>
                        </div>
                        <div class="grid grid-cols-3 gap-3 max-w-sm">
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-xl font-black text-white">Free</p><p class="text-xs font-bold text-slate-400 mt-0.5">To Join</p></div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-xl font-black text-white">15%</p><p class="text-xs font-bold text-slate-400 mt-0.5">Commission</p></div>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-center"><p class="text-xl font-black text-white">∞</p><p class="text-xs font-bold text-slate-400 mt-0.5">Earnings</p></div>
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/50">
                            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80" alt="Business partners" class="w-full h-[420px] object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/60 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CONTROLS ── --}}
        {{-- Prev/Next arrows --}}
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/10 border border-white/20 text-white hover:bg-white/25 transition flex items-center justify-center backdrop-blur-sm">
            <x-heroicon-o-chevron-left class="w-5 h-5" />
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/10 border border-white/20 text-white hover:bg-white/25 transition flex items-center justify-center backdrop-blur-sm">
            <x-heroicon-o-chevron-right class="w-5 h-5" />
        </button>

        {{-- Dot indicators --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            @for($dot = 0; $dot < 5; $dot++)
                <button
                    @click="go({{ $dot }})"
                    :class="current === {{ $dot }} ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"
                    class="h-2 rounded-full transition-all duration-300"
                ></button>
            @endfor
        </div>

        {{-- Slide counter --}}
        <div class="absolute bottom-6 right-6 z-20 text-xs font-black text-white/50 tabular-nums">
            <span x-text="current + 1"></span>/<span>5</span>
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

    {{-- ===== SUGGESTED FOR YOU (personalised, hidden for new visitors with no history) ===== --}}
    <livewire:product.suggestions />

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
