@extends('layouts/new-app')
@section('title', 'Printbuka | No. 1 Online Print Shop in Nigeria')
@section('meta_description', 'Order quality prints, branded gifts, UV-DTF, DTF, and laser engraving from Printbuka with nationwide delivery across Nigeria.')
@section('content')
    <main role="main">
        {{-- ===== HERO SLIDER ===== --}}
        <section class="relative overflow-hidden" style="min-height: 640px;">

            {{-- Slide backgrounds --}}
            @php
                $heroSlides = [
                    'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1600&q=80',
                    'https://images.unsplash.com/photo-1525909002-1b05e0c869d8?auto=format&fit=crop&w=1600&q=80',
                    'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=1600&q=80',
                    'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1600&q=80',
                    'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1600&q=80',
                ];
            @endphp

            @foreach($heroSlides as $i => $slide)
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
                    {{-- Background image --}}
                    <img src="{{ $slide }}" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
                    {{-- Pink overlay --}}
                    <div class="absolute inset-0" style="background-color: #EC268F; opacity: 0.82;"></div>
                </div>
            @endforeach

            {{-- Content — sits above slides --}}
            <div class="relative z-10 flex items-center justify-center" style="min-height: 640px;">
                <div class="w-[70%] mx-auto px-4 sm:px-6 lg:px-8 py-20 flex flex-col items-center text-center">
                    <h1 class="text-4xl sm:text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-5">
                        The Leader in<br>Quality <span class="text-pink-200">Custom</span><br>Print Design
                    </h1>
                    <p class="text-white/90 text-lg leading-relaxed mb-8 max-w-3xl">
                        Business cards, flyers, stickers, branded gifts, UV-DTF and laser engraving — from one trusted print partner. Shipped nationwide in 3–7 days.
                    </p>
                    <div class="w-full max-w-2xl">
                        <livewire:product.search />
                    </div>
                </div>
            </div>

            {{-- Dot navigation --}}
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                @foreach($heroSlides as $i => $slide)
                    <button class="hero-dot transition-all duration-300 rounded-full {{ $i === 0 ? 'w-6 h-2 bg-white' : 'w-2 h-2 bg-white/40' }}"
                            data-index="{{ $i }}"
                            aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>

        </section>

        @push('head')
        <style>
            .hero-dot.active { width: 1.5rem; background-color: white; opacity: 1; }
            .hero-dot:not(.active) { width: 0.5rem; background-color: rgba(255,255,255,0.45); }
        </style>
        @endpush

        <script>
            (function () {
                const slides = document.querySelectorAll('.hero-slide');
                const dots   = document.querySelectorAll('.hero-dot');
                let current  = 0;
                let timer;

                function goTo(index) {
                    slides[current].classList.remove('opacity-100');
                    slides[current].classList.add('opacity-0');
                    dots[current].classList.remove('active', 'w-6');
                    dots[current].classList.add('w-2');
                    current = (index + slides.length) % slides.length;
                    slides[current].classList.remove('opacity-0');
                    slides[current].classList.add('opacity-100');
                    dots[current].classList.add('active', 'w-6');
                    dots[current].classList.remove('w-2');
                    clearInterval(timer);
                    timer = setInterval(() => goTo(current + 1), 5500);
                }

                dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
                timer = setInterval(() => goTo(current + 1), 5500);
            })();
        </script>
        {{-- Clients --}}
        <section class="py-12 overflow-hidden">
            <h2 class="text-3xl font-bold text-center mb-8">Trusted by Leading Brands</h2>
            <div class="relative overflow-hidden">
                <div class="flex gap-16 items-center"
                    style="animation: marquee-ltr 18s linear infinite; width: max-content;">
                    <img src="{{ asset('client1.png') }}" alt="Client 1" class="h-12 flex-shrink-0">
                    <img src="{{ asset('client2.png') }}" alt="Client 2" class="h-12 flex-shrink-0">
                    <img src="{{ asset('client3.png') }}" alt="Client 3" class="h-12 flex-shrink-0">
                    <img src="{{ asset('client4.png') }}" alt="Client 4" class="h-12 flex-shrink-0">
                    {{-- duplicate for seamless loop --}}
                    <img src="{{ asset('client1.png') }}" alt="" class="h-12 flex-shrink-0" aria-hidden="true">
                    <img src="{{ asset('client2.png') }}" alt="" class="h-12 flex-shrink-0" aria-hidden="true">
                    <img src="{{ asset('client3.png') }}" alt="" class="h-12 flex-shrink-0" aria-hidden="true">
                    <img src="{{ asset('client4.png') }}" alt="" class="h-12 flex-shrink-0" aria-hidden="true">
                </div>
            </div>
        </section>
        {{-- Featured Products --}}
        <section class="py-16 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-10">Featured Products</h2>
            <div class="relative max-w-7xl mx-auto" id="fp-carousel">
                {{-- Prev --}}
                <button id="fp-prev" aria-label="Previous products"
                    class="absolute -left-5 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 shadow-md rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                    <x-heroicon-o-chevron-left class="w-5 h-5 text-gray-700" />
                </button>

                {{-- Track --}}
                <div class="overflow-hidden">
                    <div id="fp-track" class="flex gap-6 transition-transform duration-500">
                        @foreach($featuredProducts as $product)
                            <div class="fp-slide group relative flex-shrink-0 rounded-2xl overflow-hidden"
                                style="width: calc(25% - 18px); height: 340px;">

                                {{-- Full-bleed image --}}
                                <img src="{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                    alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';">

                                {{-- Persistent gradient --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                                {{-- Category pill --}}
                                @if($product->category)
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-[#EC268F] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                            {{ $product->category->name }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Quick-view icon --}}
                                <a href="{{ route('products.show', $product) }}"
                                    class="absolute top-4 right-4 w-8 h-8 bg-white/15 backdrop-blur-sm border border-white/25 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 hover:bg-white hover:text-slate-900 transition-all duration-300"
                                    aria-label="View {{ $product->name }}">
                                    <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                                </a>

                                {{-- Resting state: name + price --}}
                                <div
                                    class="absolute bottom-0 left-0 right-0 p-5 group-hover:opacity-0 group-hover:translate-y-2 transition-all duration-300">
                                    <h3 class="text-white font-black text-base leading-snug line-clamp-2">{{ $product->name }}
                                    </h3>
                                    @if($product->hasAvailablePrice())
                                        <p class="text-pink-300 text-sm font-bold mt-1">from NGN
                                            {{ number_format($product->price, 0) }}</p>
                                    @else
                                        <p class="text-white/60 text-xs mt-1">Request a quote</p>
                                    @endif
                                </div>

                                {{-- Hover panel: slides up --}}
                                <div
                                    class="absolute bottom-0 left-0 right-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-400 ease-out rounded-t-2xl p-5">
                                    <h3 class="text-slate-900 font-black text-sm leading-snug mb-2 line-clamp-2">
                                        {{ $product->name }}</h3>
                                    <p class="text-slate-500 text-xs leading-relaxed mb-4 line-clamp-2">
                                        {{ $product->short_description ?? $product->description }}
                                    </p>
                                    <div class="flex items-center justify-between gap-3">
                                        @if($product->hasAvailablePrice())
                                            <span class="text-base font-black text-[#EC268F]">NGN
                                                {{ number_format($product->price, 0) }}</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">Quote on request</span>
                                        @endif
                                        <a href="{{ route('products.show', $product) }}"
                                            class="inline-flex items-center gap-1 bg-[#EC268F] hover:bg-pink-700 text-white text-xs font-black px-4 py-2 rounded-lg transition-colors shrink-0">
                                            Order <x-heroicon-o-arrow-right class="w-3 h-3" />
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Next --}}
                <button id="fp-next" aria-label="Next products"
                    class="absolute -right-5 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 shadow-md rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                    <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-700" />
                </button>

                {{-- Dot indicators --}}
                <div id="fp-dots" class="flex justify-center gap-2 mt-6"></div>
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

        {{-- ===== PROMOTIONAL BANNERS ===== --}}
        <section class="py-10 bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-2 gap-5">

                    <a href="{{ route('products.index') }}"
                        class="group relative rounded-3xl overflow-hidden block h-60 sm:h-72">
                        <img src="https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=900&q=80"
                            alt="Custom print services"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/50 to-transparent">
                        </div>
                        <div class="absolute inset-0 p-8 flex flex-col justify-end">
                            <span
                                class="inline-block bg-pink-600 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full mb-3 w-fit">
                                Custom Printing
                            </span>
                            <h3 class="text-2xl font-black text-white leading-snug mb-2">Business Cards,<br>Flyers &amp;
                                More</h3>
                            <p class="text-white/70 text-sm">Professional prints from NGN 5,000 →</p>
                        </div>
                    </a>

                    <a href="{{ route('shop.index') }}"
                        class="group relative rounded-3xl overflow-hidden block h-60 sm:h-72">
                        <img src="https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=900&q=80"
                            alt="Branded gifts and merchandise"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/50 to-transparent">
                        </div>
                        <div class="absolute inset-0 p-8 flex flex-col justify-end">
                            <span
                                class="inline-block bg-amber-500 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full mb-3 w-fit">
                                Shop — Buy Now
                            </span>
                            <h3 class="text-2xl font-black text-white leading-snug mb-2">Branded Gifts &amp;<br>Merchandise
                            </h3>
                            <p class="text-white/70 text-sm">Fixed prices, instant checkout →</p>
                        </div>
                    </a>

                </div>
            </div>
        </section>

        {{-- ===== SPECIALIST SERVICES ===== --}}
        <section class="py-20 bg-pink-100 text-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900">Advanced print tech, available now.</h2>
                    <p class="text-gray-700 mt-3 max-w-xl mx-auto">We go beyond standard printing. These specialist services
                        are available directly through our product catalog.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

                    {{-- Direct Image Printing --}}
                    <div class="group relative rounded-2xl overflow-hidden h-80">
                        <video class="svc-video absolute inset-0 w-full h-full object-cover"
                            data-src="{{ asset('v (1).mp4') }}" autoplay loop muted playsinline></video>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/10 group-hover:from-pink-950/95 group-hover:via-pink-900/60 transition-all duration-500">
                        </div>
                        <div class="absolute inset-0 p-6 flex flex-col justify-end">
                            <h3 class="font-black text-white text-lg mb-2">Direct Image Printing</h3>
                            <p class="text-sm text-white/70 leading-relaxed mb-5">Vibrant full-colour prints directly onto
                                your substrate. Ideal for branded items, gifts and promos.</p>
                            <a href="{{ route('services.index') }}"
                                class="text-sm font-black text-pink-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                                Learn More <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                    {{-- UV-DTF Transfer --}}
                    <div class="group relative rounded-2xl overflow-hidden h-80">
                        <video class="svc-video absolute inset-0 w-full h-full object-cover"
                            data-src="{{ asset('v (2).mp4') }}" autoplay loop muted playsinline></video>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/10 group-hover:from-cyan-950/95 group-hover:via-cyan-900/60 transition-all duration-500">
                        </div>
                        <div class="absolute inset-0 p-6 flex flex-col justify-end">
                            <h3 class="font-black text-white text-lg mb-2">UV-DTF Transfer</h3>
                            <p class="text-sm text-white/70 leading-relaxed mb-5">Bonds to glass, metal, plastic, wood.
                                Crystal-clear, long-lasting UV-cured finish.</p>
                            <a href="{{ route('services.show', 'uv-dtf') }}"
                                class="text-sm font-black text-cyan-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                                Order Now <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                    {{-- DTF Printing --}}
                    <div class="group relative rounded-2xl overflow-hidden h-80">
                        <video class="svc-video absolute inset-0 w-full h-full object-cover"
                            data-src="{{ asset('v (3).mp4') }}" autoplay loop muted playsinline></video>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/10 group-hover:from-emerald-950/95 group-hover:via-emerald-900/60 transition-all duration-500">
                        </div>
                        <div class="absolute inset-0 p-6 flex flex-col justify-end">
                            <h3 class="font-black text-white text-lg mb-2">DTF Printing</h3>
                            <p class="text-sm text-white/70 leading-relaxed mb-5">Direct-to-Film for garments and fabric. No
                                minimum order, full-colour, soft feel.</p>
                            <a href="{{ route('services.index') }}"
                                class="text-sm font-black text-emerald-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                                Learn More <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                    {{-- Laser Engraving --}}
                    <div class="group relative rounded-2xl overflow-hidden h-80">
                        <video class="svc-video absolute inset-0 w-full h-full object-cover"
                            data-src="{{ asset('v (2).mp4') }}" autoplay loop muted playsinline></video>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/10 group-hover:from-amber-950/95 group-hover:via-amber-900/60 transition-all duration-500">
                        </div>
                        <div class="absolute inset-0 p-6 flex flex-col justify-end">
                            <h3 class="font-black text-white text-lg mb-2">Laser Engraving</h3>
                            <p class="text-sm text-white/70 leading-relaxed mb-5">Precision engraving on wood, acrylic,
                                leather, keyrings. Perfect for personalised gifts.</p>
                            <a href="{{ route('services.show', 'laser-engraving') }}"
                                class="text-sm font-black text-amber-400 group-hover:text-white flex items-center gap-1 hover:gap-2 transition-all">
                                Order Now <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        {{-- ===== WHY CHOOSE US ===== --}}
        <section class="py-20 lg:py-28 bg-white overflow-hidden">
            <div class="md:w-[80%] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="gap-14 lg:gap-20 items-center">

                    {{-- Left: Icon feature boxes --}}
                    <div>
                        <h2
                            class="text-3xl text-center sm:text-4xl mx-auto lg:text-5xl font-black text-slate-950 leading-tight mb-4">
                            Print smarter with<br>Nigeria's most trusted<br><span class="text-pink-600">print shop.</span>
                        </h2>
                        <p class="text-slate-500 text-center leading-relaxed mb-10 mx-auto max-w-lg">
                            From concept to delivery, we handle every step with precision. Thousands of businesses across
                            Nigeria rely on us for quality and speed.
                        </p>
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div
                                class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-pink-200 hover:bg-pink-50/50 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-o-document-check class="w-6 h-6 text-pink-600" />
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-950 mb-1">Free File Review</h3>
                                    <p class="text-sm text-slate-500 leading-relaxed">Every design checked by our team
                                        before print — zero extra charge.</p>
                                </div>
                            </div>
                            <div
                                class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-cyan-200 hover:bg-cyan-50/50 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-cyan-600" />
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-950 mb-1">Expert Consultations</h3>
                                    <p class="text-sm text-slate-500 leading-relaxed">Our print specialists help you choose
                                        the right finish for your job.</p>
                                </div>
                            </div>
                            <div
                                class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-o-truck class="w-6 h-6 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-950 mb-1">Nationwide Shipping</h3>
                                    <p class="text-sm text-slate-500 leading-relaxed">Door-to-door delivery to all 36 states
                                        + FCT, 3–7 working days.</p>
                                </div>
                            </div>
                            <div
                                class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:border-amber-200 hover:bg-amber-50/50 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-o-shield-check class="w-6 h-6 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-950 mb-1">Quality Guarantee</h3>
                                    <p class="text-sm text-slate-500 leading-relaxed">We reprint at no cost if quality falls
                                        below expectations. Always.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== PRODUCT CATEGORIES ===== --}}
        <section class="py-20 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Everything you need to print,<br
                                class="hidden lg:block"> brand and gift.</h2>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button id="cat-prev" aria-label="Previous categories"
                            class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center hover:bg-slate-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                            <x-heroicon-o-chevron-left class="w-5 h-5 text-slate-700" />
                        </button>
                        <button id="cat-next" aria-label="Next categories"
                            class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center hover:bg-slate-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                            <x-heroicon-o-chevron-right class="w-5 h-5 text-slate-700" />
                        </button>
                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center gap-1.5 border border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 text-sm font-black px-5 py-2.5 rounded-xl transition-colors">
                            All <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
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
                    $catItems = $homeCategories ?? collect();
                @endphp

                @if($catItems->isNotEmpty())
                    <div class="relative" id="cat-carousel">
                        <div class="overflow-hidden">
                            <div id="cat-track" class="flex gap-6 transition-transform duration-500">
                                @foreach($catItems as $category)
                                    @php
                                        $catImage = $category->imageUrl() ?: $catFallbacks[$loop->index % count($catFallbacks)];
                                        $catSummary = $category->description ?: 'Explore print and branded products in this category.';
                                        $productCount = (int) ($category->active_products_count ?? 0);
                                    @endphp
                                    <a href="{{ route('products.category', $category) }}"
                                        class="cat-slide group flex-shrink-0 rounded-3xl overflow-hidden border border-slate-100 hover:border-pink-200 hover:shadow-xl transition-all duration-300 bg-white flex flex-col"
                                        style="width: calc(33.333% - 16px);">
                                        <div class="h-52 overflow-hidden bg-slate-100 shrink-0">
                                            <img src="{{ $catImage }}" alt="{{ $category->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                                onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                        </div>
                                        <div class="p-5 flex-1 flex flex-col">
                                            <div class="flex items-center justify-between mb-2">
                                                <span
                                                    class="text-xs font-black uppercase tracking-wide text-pink-600">{{ $category->tag ?: 'Category' }}</span>
                                                <span
                                                    class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $productCount }}
                                                    {{ \Illuminate\Support\Str::plural('product', $productCount) }}</span>
                                            </div>
                                            <h3 class="text-base font-black text-slate-950 mb-1">{{ $category->name }}</h3>
                                            <p class="text-sm text-slate-500 leading-relaxed flex-1">
                                                {{ \Illuminate\Support\Str::limit($catSummary, 95) }}</p>
                                            @if($category->children->isNotEmpty())
                                                <div class="mt-3 flex flex-wrap gap-1.5">
                                                    @foreach($category->children->take(3) as $child)
                                                        <span
                                                            class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">{{ $child->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div
                                                class="mt-4 flex items-center gap-1 text-sm font-black text-pink-600 group-hover:gap-2 transition-all">
                                                Browse category <x-heroicon-o-arrow-right class="w-4 h-4" />
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div id="cat-dots" class="flex justify-center gap-2 mt-6"></div>
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-squares-2x2 class="w-8 h-8 text-slate-300" />
                        </div>
                        <p class="text-lg font-black text-slate-700">No product categories yet.</p>
                        <p class="text-sm mt-1 text-slate-400">Please check back shortly.</p>
                    </div>
                @endif

            </div>
        </section>

        {{-- ===== TESTIMONIALS ===== --}}
        <section class="py-20 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Trusted by businesses across Nigeria.</h2>
                    <p class="text-slate-500 mt-3">Real feedback from clients who order with us regularly.</p>
                </div>
                <div class="grid sm:grid-cols-3 gap-6">
                    <div
                        class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                        <div class="flex gap-0.5 mb-4">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"This print shop exhibits professionalism
                            in all senses. They are reliable and deliver promptly. They pay close attention to details when
                            it comes to printing."</p>
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
                    <div
                        class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                        <div class="flex gap-0.5 mb-4">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"Quality work, fast turnaround, and the
                            team actually managed my design too. Printbuka is my go-to print shop for everything
                            business-related."</p>
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
                    <div
                        class="rounded-3xl bg-slate-50 border border-slate-100 p-7 hover:border-pink-200 hover:shadow-md transition-all">
                        <div class="flex gap-0.5 mb-4">
                            @for($i = 0; $i < 5; $i++)<x-heroicon-s-star class="w-4 h-4 text-amber-400" />@endfor
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed italic">"Ordered branded mugs for a corporate event
                            and they came out perfect. Delivery was on time. Highly recommend Printbuka for any gifting or
                            print need."</p>
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
        @if(($popularGiftItems ?? collect())->isNotEmpty())
            <section class="py-20 bg-slate-50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                        <div>
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Most popular gift-ready products.</h2>
                        </div>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center gap-1.5 border border-slate-200 hover:border-amber-400 hover:text-amber-700 hover:bg-amber-50 text-sm font-black px-5 py-2.5 rounded-xl transition-colors shrink-0">
                            Browse Gifts <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($popularGiftItems as $product)
                            <article class="group relative rounded-2xl overflow-hidden cursor-pointer" style="height: 320px;">
                                <img src="{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}"
                                    alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">Gift</span>
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 right-0 p-5 group-hover:opacity-0 group-hover:translate-y-2 transition-all duration-300">
                                    <h3 class="text-white font-black text-base leading-snug line-clamp-2">{{ $product->name }}</h3>
                                    @if($product->hasAvailablePrice())
                                        <p class="text-amber-300 text-sm font-bold mt-1">from NGN
                                            {{ number_format($product->price, 0) }}</p>
                                    @else
                                        <p class="text-white/60 text-xs mt-1">Request a quote</p>
                                    @endif
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 right-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-400 ease-out rounded-t-2xl p-5">
                                    <h3 class="text-slate-900 font-black text-sm leading-snug mb-2 line-clamp-2">
                                        {{ $product->name }}</h3>
                                    <p class="text-slate-500 text-xs leading-relaxed mb-3 line-clamp-2">
                                        {{ $product->short_description }}</p>
                                    <div class="flex items-center justify-between gap-3">
                                        @if($product->hasAvailablePrice())
                                            <span class="text-base font-black text-amber-600">NGN
                                                {{ number_format($product->price, 0) }}</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400">Quote on request</span>
                                        @endif
                                        <a href="{{ $product->hasAvailablePrice() ? route('orders.create', $product) : route('products.show', $product) }}"
                                            class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black px-4 py-2 rounded-lg transition-colors shrink-0">
                                            Order <x-heroicon-o-arrow-right class="w-3 h-3" />
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
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-950">Ready-to-buy products.<br
                                    class="hidden lg:block"> <span class="text-pink-600">No quote needed.</span></h2>
                            <p class="text-slate-500 mt-3 max-w-xl">Fixed prices, instant checkout. Pay securely via Paystack —
                                delivered to your door.</p>
                        </div>
                        <a href="{{ route('shop.index') }}"
                            class="inline-flex items-center gap-1.5 border border-slate-200 hover:border-pink-400 hover:text-pink-700 hover:bg-pink-50 text-sm font-black px-5 py-2.5 rounded-xl transition-colors shrink-0">
                            Browse Shop <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach($featuredShopProducts as $shopProduct)
                            <article
                                class="group bg-white rounded-3xl border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
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
                                            <span
                                                class="font-black text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg text-sm shadow">Out
                                                of Stock</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5 flex-1 flex flex-col">
                                    <h3 class="font-black text-slate-950 text-base leading-snug flex-1">
                                        <a href="{{ route('shop.show', $shopProduct) }}"
                                            class="hover:text-pink-600 transition">{{ $shopProduct->name }}</a>
                                    </h3>
                                    @if($shopProduct->short_description)
                                        <p class="text-sm text-slate-500 line-clamp-2 mt-1">{{ $shopProduct->short_description }}</p>
                                    @endif
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="text-xl font-black text-pink-600">NGN
                                            {{ number_format($shopProduct->currentPrice(), 0) }}</span>
                                        @if($shopProduct->isOnSale())
                                            <span class="text-sm font-bold text-slate-400 line-through">NGN
                                                {{ number_format((float) $shopProduct->price, 0) }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('shop.show', $shopProduct) }}"
                                            class="inline-flex items-center justify-center gap-2 w-full bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-4 py-2.5 rounded-xl transition-colors">
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
                    <h2 class="text-3xl sm:text-4xl font-black text-white">Order in 3 simple steps.</h2>
                    <p class="text-slate-400 mt-3 max-w-lg mx-auto">From choosing your product to delivery at your door —
                        fast, simple and stress-free.</p>
                </div>
                <div class="grid sm:grid-cols-3 gap-5">
                    <div
                        class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-pink-600/40 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-pink-600 flex items-center justify-center mb-6">
                            <span class="text-white font-black text-2xl">1</span>
                        </div>
                        <h3 class="font-black text-white text-xl mb-3">Choose Your Product</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Browse our full catalog and pick the product, size
                            and quantity that works for your job.</p>
                    </div>
                    <div
                        class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-cyan-600/40 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-cyan-600 flex items-center justify-center mb-6">
                            <span class="text-white font-black text-2xl">2</span>
                        </div>
                        <h3 class="font-black text-white text-xl mb-3">Share Your Artwork</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Upload your design or describe what you need. Our
                            team reviews your file within 24 hours — for free.</p>
                    </div>
                    <div
                        class="rounded-3xl bg-white/5 border border-white/10 p-7 hover:bg-white/8 hover:border-emerald-600/40 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center mb-6">
                            <span class="text-white font-black text-2xl">3</span>
                        </div>
                        <h3 class="font-black text-white text-xl mb-3">We Print &amp; Deliver</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">We produce and ship your order nationwide within
                            3–7 working days. Track every step.</p>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-10">
                    <a href="{{ route('orders.track') }}"
                        class="inline-flex items-center gap-2 border border-white/20 text-white hover:bg-white hover:text-slate-950 text-sm font-black px-6 py-3 rounded-xl transition-colors">Track
                        an Order</a>
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-6 py-3 rounded-xl transition-colors">Shop
                        Now</a>
                </div>
            </div>
        </section>

        {{-- ===== CTA BANNER ===== --}}
        <section class="py-20 bg-white">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div
                    class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 p-10 lg:p-16 text-white text-center">
                    <div class="pointer-events-none absolute inset-0">
                        <div
                            class="absolute top-0 right-0 w-80 h-80 rounded-full bg-pink-600/10 -translate-y-1/2 translate-x-1/3 blur-2xl">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-cyan-500/10 translate-y-1/2 -translate-x-1/3 blur-2xl">
                        </div>
                    </div>
                    <div class="relative">
                        <h2 class="text-4xl lg:text-5xl font-black text-white mb-4">Ready to start printing?</h2>
                        <p class="text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">Join 15,000+ businesses across
                            Nigeria who trust Printbuka for quality prints, branded gifts and fast delivery.</p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-8 py-4 rounded-xl transition-colors">Create
                                Free Account</a>
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center gap-2 text-white text-sm font-black px-8 py-4 rounded-xl transition-colors"
                                style="border: 1px solid rgba(255,255,255,0.25);">Browse Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            (function () {
                const track = document.getElementById('fp-track');
                const prevBtn = document.getElementById('fp-prev');
                const nextBtn = document.getElementById('fp-next');
                const dotsEl = document.getElementById('fp-dots');
                const perPage = 4;
                const slides = track ? Array.from(track.querySelectorAll('.fp-slide')) : [];
                const total = slides.length;
                const pages = Math.ceil(total / perPage);
                let current = 0;

                if (!track || total === 0) return;

                // Build dots
                for (let i = 0; i < pages; i++) {
                    const d = document.createElement('button');
                    d.className = 'w-2.5 h-2.5 rounded-full bg-gray-300 transition-colors';
                    d.setAttribute('aria-label', 'Go to page ' + (i + 1));
                    d.addEventListener('click', () => goTo(i));
                    dotsEl.appendChild(d);
                }

                function goTo(page) {
                    current = Math.max(0, Math.min(page, pages - 1));
                    const slideWidth = slides[0].offsetWidth + 24; // 24 = gap-6
                    track.style.transform = 'translateX(-' + (current * perPage * slideWidth) + 'px)';
                    prevBtn.disabled = current === 0;
                    nextBtn.disabled = current === pages - 1;
                    dotsEl.querySelectorAll('button').forEach((d, i) => {
                        d.classList.toggle('bg-[#EC268F]', i === current);
                        d.classList.toggle('bg-gray-300', i !== current);
                    });
                }

                prevBtn.addEventListener('click', () => goTo(current - 1));
                nextBtn.addEventListener('click', () => goTo(current + 1));

                goTo(0);
            })();
        </script>

        <script>
            (function () {
                const track = document.getElementById('cat-track');
                const prevBtn = document.getElementById('cat-prev');
                const nextBtn = document.getElementById('cat-next');
                const dotsEl = document.getElementById('cat-dots');
                const perPage = 3;
                const slides = track ? Array.from(track.querySelectorAll('.cat-slide')) : [];
                const total = slides.length;
                const pages = Math.ceil(total / perPage);
                let current = 0;

                if (!track || total === 0) return;

                for (let i = 0; i < pages; i++) {
                    const d = document.createElement('button');
                    d.className = 'w-2.5 h-2.5 rounded-full bg-slate-300 transition-colors';
                    d.setAttribute('aria-label', 'Go to page ' + (i + 1));
                    d.addEventListener('click', () => catGoTo(i));
                    dotsEl.appendChild(d);
                }

                function catGoTo(page) {
                    current = Math.max(0, Math.min(page, pages - 1));
                    const slideWidth = slides[0].offsetWidth + 24;
                    track.style.transform = 'translateX(-' + (current * perPage * slideWidth) + 'px)';
                    prevBtn.disabled = current === 0;
                    nextBtn.disabled = current === pages - 1;
                    dotsEl.querySelectorAll('button').forEach((d, i) => {
                        d.classList.toggle('bg-[#EC268F]', i === current);
                        d.classList.toggle('bg-slate-300', i !== current);
                    });
                }

                prevBtn.addEventListener('click', () => catGoTo(current - 1));
                nextBtn.addEventListener('click', () => catGoTo(current + 1));
                catGoTo(0);
            })();
        </script>

        <script>
            (function () {
                const videos = document.querySelectorAll('video.svc-video[data-src]');
                if (!videos.length) return;

                const observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        const v = entry.target;
                        v.src = v.dataset.src;
                        v.load();
                        v.play().catch(function () { });
                        observer.unobserve(v);
                    });
                }, { threshold: 0.15 });

                videos.forEach(function (v) { observer.observe(v); });
            })();
        </script>

        <style>
            @keyframes marquee-ltr {
                from {
                    transform: translateX(-50%);
                }

                to {
                    transform: translateX(0%);
                }
            }
        </style>
    </main>
@endsection