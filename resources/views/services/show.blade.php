@extends('layouts.new-app')

@section('title', $service['name'].' | Printbuka Services')
@section('meta_description', \Illuminate\Support\Str::limit($service['summary'] ?? ('Order '.$service['name'].' from Printbuka.'), 155))

@push('head')
<style>
.svc-slide { position:absolute; inset:0; transition: opacity 1s ease-in-out; }
.svc-dot   { width:8px; height:8px; border-radius:9999px; background:rgba(255,255,255,0.35); transition: all .3s; cursor:pointer; border:none; padding:0; }
.svc-dot.active { background:#EC268F; width:24px; }
</style>
@endpush

@section('content')
@php
    $heroKicker     = (string)  ($service['hero_kicker']    ?? 'Professional Service');
    $heroTitle      = (string)  ($service['hero_title']     ?? $service['name']);
    $heroSummary    = (string)  ($service['hero_summary']   ?? $service['summary'] ?? '');
    $proofPoints    = (array)   ($service['proof_points']   ?? []);
    $features       = (array)   ($service['features']       ?? []);
    $useCases       = (array)   ($service['use_cases']      ?? []);
    $processSteps   = (array)   ($service['process_steps']  ?? []);
    $trustPoints    = (array)   ($service['trust_points']   ?? []);
    $pricingMode    = (string)  ($service['pricing_mode']   ?? 'fixed');
    $pricingFactors = (array)   ($service['pricing_factors'] ?? []);

    $productSectionRedirect = match ($service['slug']) {
        'uv-dtf'          => route('products.index').'#catalog',
        'laser-engraving' => route('products.index').'#catalog',
        default           => null,
    };
    $usesLivewireForm    = in_array($service['slug'], ['direct-image-printing', 'dtf'], true);
    $usesCatalogRedirect = filled($productSectionRedirect);
    $primaryCtaUrl       = $usesCatalogRedirect ? $productSectionRedirect : '#service-order-form';
    $primaryCtaLabel     = $usesCatalogRedirect ? 'Browse Related Products' : 'Place Order Now';

    $serviceIcons = [
        'direct-image-printing' => 'printer',
        'uv-dtf'                => 'sparkles',
        'dtf'                   => 'swatch',
        'dtf-borderless'        => 'rectangle-group',
        'laser-engraving'       => 'bolt',
    ];
    $serviceColors = [
        'direct-image-printing' => ['bg'=>'bg-pink-600',    'hex'=>'#db2777', 'from'=>'from-pink-600',    'light'=>'bg-pink-50',    'text'=>'text-pink-600',    'border'=>'border-pink-200'],
        'uv-dtf'                => ['bg'=>'bg-violet-600',  'hex'=>'#7c3aed', 'from'=>'from-violet-600',  'light'=>'bg-violet-50',  'text'=>'text-violet-600',  'border'=>'border-violet-200'],
        'dtf'                   => ['bg'=>'bg-sky-600',     'hex'=>'#0284c7', 'from'=>'from-sky-600',     'light'=>'bg-sky-50',     'text'=>'text-sky-600',     'border'=>'border-sky-200'],
        'dtf-borderless'        => ['bg'=>'bg-emerald-600', 'hex'=>'#059669', 'from'=>'from-emerald-600', 'light'=>'bg-emerald-50', 'text'=>'text-emerald-600', 'border'=>'border-emerald-200'],
        'laser-engraving'       => ['bg'=>'bg-amber-600',   'hex'=>'#d97706', 'from'=>'from-amber-600',   'light'=>'bg-amber-50',   'text'=>'text-amber-600',   'border'=>'border-amber-200'],
    ];
    $icon   = $serviceIcons[$service['slug']]  ?? 'star';
    $colors = $serviceColors[$service['slug']] ?? $serviceColors['direct-image-printing'];

    // Per-service hero slides
    $slidesByService = [
        'direct-image-printing' => [
            ['img' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1600&q=80', 'caption' => 'High-clarity direct image output'],
            ['img' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Sharp color, clean finish'],
            ['img' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Branded campaign materials'],
            ['img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1600&q=80', 'caption' => 'From brief to print-ready'],
        ],
        'uv-dtf' => [
            ['img' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Premium hard-surface branding'],
            ['img' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Scratch-resistant UV transfers'],
            ['img' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Corporate gifts & promo items'],
        ],
        'dtf' => [
            ['img' => 'https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Vibrant fabric color output'],
            ['img' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Branded apparel & uniforms'],
            ['img' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Wash-fast, durable prints'],
        ],
        'dtf-borderless' => [
            ['img' => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Full-surface all-over prints'],
            ['img' => 'https://images.unsplash.com/photo-1571945153237-4929e783af4a?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Edge-to-edge borderless coverage'],
            ['img' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Sportswear & activewear'],
            ['img' => 'https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Vibrant campaign merchandise'],
        ],
        'laser-engraving' => [
            ['img' => 'https://images.unsplash.com/photo-1590005354167-6da97870c757?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Precision laser engraving'],
            ['img' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Corporate gifts & plaques'],
            ['img' => 'https://images.unsplash.com/photo-1550710182-583769a09aaa?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Premium branded keepsakes'],
        ],
    ];
    $heroSlides = $slidesByService[$service['slug']] ?? [
        ['img' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1600&q=80', 'caption' => $service['name']],
        ['img' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1600&q=80', 'caption' => 'Professional print production'],
    ];
    $sliderId = 'svc-hero-'.Str::slug($service['slug']);
@endphp

<main>

    {{-- ===== HERO WITH PER-SERVICE SLIDESHOW ===== --}}
    <section class="relative overflow-hidden" style="min-height:580px;">

        {{-- Slides --}}
        @foreach($heroSlides as $i => $slide)
            <div class="svc-slide {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}" aria-hidden="true" data-slide="{{ $i }}">
                <img src="{{ $slide['img'] }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                {{-- Base dark overlay --}}
                <div class="absolute inset-0 bg-slate-950/75"></div>
                {{-- Service-color tint --}}
                <div class="absolute inset-0 opacity-20" style="background-color:{{ $colors['hex'] }};"></div>
                {{-- Vignette --}}
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
            </div>
        @endforeach

        {{-- Decorative blobs over slides --}}
        <div class="pointer-events-none absolute inset-0 z-[1]">
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl opacity-20" style="background:{{ $colors['hex'] }};"></div>
            <div class="absolute -bottom-20 right-0 w-72 h-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-10 pb-0">

            {{-- Breadcrumb --}}
            <a href="{{ route('services.index') }}"
               class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm font-bold transition-colors mb-8">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                All Services
            </a>

            <div class="flex flex-col lg:flex-row lg:items-end gap-10">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl {{ $colors['bg'] }} flex items-center justify-center shadow-lg">
                            <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-7 h-7 text-white" />
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-pink-400 bg-pink-600/20 border border-pink-600/30 px-4 py-2 rounded-full">
                            {{ $heroKicker }}
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.05] mb-5">
                        {{ $heroTitle }}
                    </h1>
                    <p class="text-slate-300 text-lg leading-relaxed max-w-2xl mb-8">
                        {{ $heroSummary }}
                    </p>

                    @if($proofPoints)
                        <div class="flex flex-wrap gap-2 mb-10">
                            @foreach($proofPoints as $point)
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white/80 bg-white/10 border border-white/15 px-3 py-1.5 rounded-full">
                                    <x-heroicon-s-check class="w-3 h-3 text-emerald-400" />
                                    {{ $point }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $primaryCtaUrl }}"
                           class="inline-flex items-center gap-2 {{ $colors['bg'] }} hover:opacity-90 text-white font-black px-7 py-4 rounded-xl transition-all text-sm shadow-lg">
                            <x-heroicon-o-shopping-cart class="w-5 h-5" />
                            {{ $primaryCtaLabel }}
                        </a>
                        <a href="{{ route('services.index') }}"
                           class="inline-flex items-center gap-2 border border-white/25 hover:border-white/60 text-white font-black px-7 py-4 rounded-xl transition-colors text-sm">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                            All Services
                        </a>
                    </div>
                </div>

                {{-- Pricing card --}}
                <div class="lg:w-80 shrink-0">
                    <div class="bg-white/8 border border-white/15 rounded-t-2xl p-6 backdrop-blur-sm">
                        @if($pricingMode === 'variable')
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Pricing Model</p>
                            <p class="text-3xl font-black text-white mb-1">Variable</p>
                            @if($pricingFactors)
                                <p class="text-sm text-slate-400 mb-4">Based on: {{ implode(', ', $pricingFactors) }}.</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($pricingFactors as $f)
                                        <span class="text-[10px] font-black text-white/70 bg-white/10 px-2.5 py-1 rounded-full border border-white/15">{{ $f }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Starting From</p>
                            <p class="text-3xl font-black text-white mb-1">NGN {{ number_format((float)$service['price'], 0) }}</p>
                            <p class="text-xs text-slate-400">Current pricing configuration applies.</p>
                        @endif
                        <div class="mt-5 pt-4 border-t border-white/10 space-y-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-300">
                                <x-heroicon-o-shield-check class="w-4 h-4 text-emerald-400 shrink-0" />
                                Secure Paystack checkout
                            </div>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-300">
                                <x-heroicon-o-map-pin class="w-4 h-4 text-emerald-400 shrink-0" />
                                Pickup or nationwide delivery
                            </div>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-300">
                                <x-heroicon-o-arrow-path class="w-4 h-4 text-emerald-400 shrink-0" />
                                6-phase tracked workflow
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Slide caption + dots --}}
        <div class="absolute bottom-14 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-3">
            <span id="{{ $sliderId }}-label" class="text-xs font-black text-white/50 uppercase tracking-widest">{{ $heroSlides[0]['caption'] }}</span>
            <div class="flex gap-2" id="{{ $sliderId }}-dots">
                @foreach($heroSlides as $i => $slide)
                    <button class="svc-dot {{ $i === 0 ? 'active' : '' }}" data-target="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>

        {{-- Wave --}}
        <div class="absolute bottom-0 left-0 right-0 h-10 overflow-hidden pointer-events-none">
            <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute bottom-0 w-full" preserveAspectRatio="none">
                <path d="M0 40L1440 40L1440 20C1200 0 960 40 720 20C480 0 240 40 0 20L0 40Z" fill="white"/>
            </svg>
        </div>
    </section>

    <script>
    (function(){
        const prefix   = {{ Js::from($sliderId) }};
        const slides   = document.querySelectorAll('[data-slide]');
        const dots     = document.querySelectorAll('#' + prefix + '-dots [data-target]');
        const label    = document.getElementById(prefix + '-label');
        const captions = @json(array_column($heroSlides, 'caption'));
        let current    = 0, timer;

        function goTo(n){
            slides[current].classList.replace('opacity-100','opacity-0');
            dots[current].classList.remove('active');
            current = (n + slides.length) % slides.length;
            slides[current].classList.replace('opacity-0','opacity-100');
            dots[current].classList.add('active');
            if(label) label.textContent = captions[current];
        }
        dots.forEach(d => d.addEventListener('click', function(){ clearInterval(timer); goTo(+this.dataset.target); timer = setInterval(() => goTo(current + 1), 5000); }));
        timer = setInterval(() => goTo(current + 1), 5000);
    })();
    </script>

    {{-- ===== DETAIL SECTIONS ===== --}}
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-6">

                {{-- What You Get --}}
                <div class="bg-white rounded-2xl border border-slate-100 hover:border-pink-200 hover:shadow-lg transition-all p-7">
                    <div class="w-10 h-10 rounded-xl {{ $colors['light'] }} flex items-center justify-center mb-4">
                        <x-heroicon-o-check-badge class="w-5 h-5 {{ $colors['text'] }}" />
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $colors['text'] }} mb-1">Service Highlights</p>
                    <h2 class="text-xl font-black text-slate-950 mb-5">What You Get</h2>
                    <ul class="space-y-3">
                        @forelse($features as $feat)
                            <li class="flex items-start gap-3 text-sm text-slate-700">
                                <x-heroicon-s-check-circle class="w-4 h-4 {{ $colors['text'] }} shrink-0 mt-0.5" />
                                {{ $feat }}
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Service details confirmed before production begins.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Best For --}}
                <div class="bg-white rounded-2xl border border-slate-100 hover:border-pink-200 hover:shadow-lg transition-all p-7">
                    <div class="w-10 h-10 rounded-xl {{ $colors['light'] }} flex items-center justify-center mb-4">
                        <x-heroicon-o-building-office class="w-5 h-5 {{ $colors['text'] }}" />
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest {{ $colors['text'] }} mb-1">Best For</p>
                    <h2 class="text-xl font-black text-slate-950 mb-5">Use Cases</h2>
                    <ul class="space-y-3">
                        @forelse($useCases as $uc)
                            <li class="flex items-start gap-3 text-sm text-slate-700">
                                <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                                {{ $uc }}
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Ideal for businesses needing consistent output.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Trust Promise --}}
                <div class="rounded-2xl {{ $colors['bg'] }} p-7 text-white relative overflow-hidden">
                    <div class="pointer-events-none absolute top-0 right-0 w-40 h-40 rounded-full bg-white/10 -translate-y-1/3 translate-x-1/3 blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                            <x-heroicon-o-shield-check class="w-5 h-5 text-white" />
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/70 mb-1">Our Promise</p>
                        <h2 class="text-xl font-black text-white mb-5">Why Trust Us</h2>
                        <ul class="space-y-3">
                            @forelse($trustPoints as $tp)
                                <li class="flex items-start gap-3 text-sm text-white/90">
                                    <x-heroicon-s-check class="w-4 h-4 text-white shrink-0 mt-0.5" />
                                    {{ $tp }}
                                </li>
                            @empty
                                <li class="text-sm text-white/80">Clear scope before production. Dependable delivery timelines.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== PROCESS STEPS ===== --}}
    <section class="py-16 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block {{ $colors['light'] }} {{ $colors['text'] }} text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">Process</span>
                <h2 class="text-4xl font-black text-slate-950">How Your Order Works</h2>
            </div>
            <div class="relative max-w-4xl mx-auto">
                <div class="hidden md:block absolute top-7 left-[calc(16.66%+20px)] right-[calc(16.66%+20px)] h-0.5 opacity-30 pointer-events-none {{ $colors['bg'] }}"></div>
                <div class="grid sm:grid-cols-3 gap-6">
                    @forelse($processSteps as $i => $step)
                        <div class="bg-white rounded-2xl border border-slate-100 hover:border-pink-200 hover:shadow-lg transition-all p-6 text-center group">
                            <div class="w-14 h-14 rounded-2xl {{ $colors['bg'] }} flex items-center justify-center mx-auto mb-4 shadow-md group-hover:scale-110 transition-transform">
                                <span class="text-xl font-black text-white">{{ $i + 1 }}</span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $step }}</p>
                        </div>
                    @empty
                        @foreach(['Submit brief & quantity', 'Review & complete payment', 'Production, QC & delivery'] as $i => $step)
                            <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center">
                                <div class="w-14 h-14 rounded-2xl {{ $colors['bg'] }} flex items-center justify-center mx-auto mb-4 shadow-md">
                                    <span class="text-xl font-black text-white">{{ $i + 1 }}</span>
                                </div>
                                <p class="text-sm text-slate-600 leading-relaxed">{{ $step }}</p>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ===== ORDER SECTION ===== --}}
    <section id="service-order-form" class="py-16 bg-white scroll-mt-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <span class="inline-block {{ $colors['light'] }} {{ $colors['text'] }} text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                    @if($usesCatalogRedirect) Order via Catalog @elseif($usesLivewireForm) Place Your Order @else Order Form @endif
                </span>
                <h2 class="text-4xl font-black text-slate-950 mb-2">Order {{ $service['name'] }}</h2>
                <p class="text-slate-500 text-base max-w-xl mx-auto">
                    @if($usesCatalogRedirect)
                        {{ $service['name'] }} orders are placed through our product catalog where you select exact items and specifications.
                    @else
                        Fill in your details below and proceed to secure payment via Paystack.
                    @endif
                </p>
            </div>

            @if($usesCatalogRedirect)
                <div class="bg-slate-50 rounded-3xl border border-slate-100 p-10 text-center">
                    <div class="w-16 h-16 rounded-2xl {{ $colors['bg'] }} flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-2xl font-black text-slate-950 mb-2">Find {{ $service['name'] }} Products</h3>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-md mx-auto mb-8">
                        Browse the product catalog to select the specific item you want — size, material and customization options are all on the product page.
                    </p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ $productSectionRedirect }}"
                           class="inline-flex items-center gap-2 {{ $colors['bg'] }} hover:opacity-90 text-white font-black px-7 py-4 rounded-xl transition-all text-sm shadow-lg">
                            <x-heroicon-o-tag class="w-5 h-5" />
                            Go to {{ $service['name'] }} Products
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 border border-slate-200 hover:border-pink-300 text-slate-700 hover:text-pink-700 font-black px-7 py-4 rounded-xl transition-colors text-sm">
                            Browse Full Catalog
                        </a>
                    </div>
                </div>

            @elseif($usesLivewireForm)
                @auth
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="{{ $colors['bg'] }} px-7 py-5">
                            <p class="text-white font-black text-sm">Logged in as {{ auth()->user()->displayName() }} — your details are pre-filled below.</p>
                        </div>
                        <div class="p-7">
                            @if($service['slug'] === 'direct-image-printing')
                                <livewire:services.direct-image-order-form :service="$service" />
                            @elseif($service['slug'] === 'dtf')
                                <livewire:services.dtf-order-form :service="$service" />
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-slate-50 rounded-3xl border border-slate-100 p-10 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-200 flex items-center justify-center mx-auto mb-5">
                            <x-heroicon-o-lock-closed class="w-8 h-8 text-slate-500" />
                        </div>
                        <h3 class="text-2xl font-black text-slate-950 mb-2">Sign In to Place Your Order</h3>
                        <p class="text-slate-500 text-sm leading-relaxed max-w-md mx-auto mb-8">
                            A free account lets you track your order, access your invoice and communicate with our team.
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 {{ $colors['bg'] }} hover:opacity-90 text-white font-black px-7 py-4 rounded-xl transition-all text-sm shadow-lg">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center gap-2 border border-slate-200 hover:border-pink-300 text-slate-700 hover:text-pink-700 font-black px-7 py-4 rounded-xl transition-colors text-sm">
                                <x-heroicon-o-user-plus class="w-5 h-5" />
                                Create Free Account
                            </a>
                        </div>
                    </div>
                @endauth

            @else
                {{-- Standard order form --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="{{ $colors['bg'] }} px-7 py-5 flex items-center justify-between">
                        <p class="text-white font-black">Order {{ $service['name'] }}</p>
                        @if($pricingMode === 'variable')
                            <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 text-white px-3 py-1.5 rounded-full border border-white/30">Variable Pricing</span>
                        @else
                            <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 text-white px-3 py-1.5 rounded-full border border-white/30">From NGN {{ number_format((float)$service['price'], 0) }}</span>
                        @endif
                    </div>

                    <div class="p-7 sm:p-10">

                        @if(session('status') || session('warning'))
                            <div class="mb-6 flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-bold
                                {{ session('status') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
                                <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
                                {{ session('status') ?? session('warning') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">
                                <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-black mb-1">Please fix the highlighted fields.</p>
                                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('services.orders.store', $service['slug']) }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="quantity" class="block text-sm font-black text-slate-800 mb-1.5">Quantity <span class="text-pink-600">*</span></label>
                                    <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required
                                           class="w-full h-12 rounded-xl border {{ $errors->has('quantity') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div>
                                    <label for="delivery_method" class="block text-sm font-black text-slate-800 mb-1.5">Delivery Method <span class="text-pink-600">*</span></label>
                                    <select id="delivery_method" name="delivery_method" required
                                            class="w-full h-12 rounded-xl border {{ $errors->has('delivery_method') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 bg-white">
                                        @foreach($deliveryMethods as $method)
                                            <option value="{{ $method }}" @selected(old('delivery_method','Client Pickup') === $method)>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-slate-800 mb-1.5">Your Name <span class="text-pink-600">*</span></label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name', $customer?->displayName()) }}" required
                                           class="w-full h-12 rounded-xl border {{ $errors->has('customer_name') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-slate-800 mb-1.5">Email Address <span class="text-pink-600">*</span></label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email', $customer?->email) }}" required
                                           class="w-full h-12 rounded-xl border {{ $errors->has('customer_email') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-slate-800 mb-1.5">Phone Number <span class="text-pink-600">*</span></label>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $customer?->phone) }}" required
                                           class="w-full h-12 rounded-xl border {{ $errors->has('customer_phone') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div>
                                    <label for="delivery_city" class="block text-sm font-black text-slate-800 mb-1.5">Delivery City</label>
                                    <input id="delivery_city" type="text" name="delivery_city" value="{{ old('delivery_city') }}"
                                           class="w-full h-12 rounded-xl border {{ $errors->has('delivery_city') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="delivery_address" class="block text-sm font-black text-slate-800 mb-1.5">Delivery Address</label>
                                    <input id="delivery_address" type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                                           class="w-full h-12 rounded-xl border {{ $errors->has('delivery_address') ? 'border-red-400' : 'border-slate-200' }} px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-black text-slate-800 mb-1.5">Order Notes / Artwork Instructions <span class="text-pink-600">*</span></label>
                                    <textarea name="artwork_notes" rows="4" required
                                              class="w-full rounded-xl border {{ $errors->has('artwork_notes') ? 'border-red-400' : 'border-slate-200' }} px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 resize-none">{{ old('artwork_notes') }}</textarea>
                                    <p class="text-xs text-slate-400 mt-1">Include dimensions, colours, file format, or any special requirements.</p>
                                </div>
                            </div>

                            <div class="rounded-2xl {{ $colors['light'] }} {{ $colors['border'] }} border p-5 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-0.5">Estimated Total</p>
                                    <p id="estimated_total" class="text-2xl font-black {{ $colors['text'] }}">NGN {{ number_format((float)$service['price'], 0) }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Final invoice may include delivery adjustments.</p>
                                </div>
                                <x-heroicon-o-calculator class="w-8 h-8 text-slate-200 shrink-0" />
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 {{ $colors['bg'] }} hover:opacity-90 text-white font-black px-7 py-4 rounded-xl transition-all text-sm shadow-lg">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                                Proceed to Paystack — Secure Payment
                            </button>

                            <p class="text-center text-xs text-slate-400">
                                By placing this order you agree to our
                                <a href="{{ route('policies.show', 'terms') }}" class="underline hover:text-slate-600">Terms of Service</a>.
                                Payments processed securely by Paystack.
                            </p>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== RELATED SERVICES ===== --}}
    <section class="py-16 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-slate-950 mb-6">Explore Other Services</h2>
            @php
                $allServices = collect(config('printbuka_services.services', []))->map(fn($s,$slug) => [...$s,'slug'=>$slug])->values();
                $siIcons     = ['direct-image-printing'=>'printer','uv-dtf'=>'sparkles','dtf'=>'swatch','dtf-borderless'=>'rectangle-group','laser-engraving'=>'bolt'];
                $siColors    = ['direct-image-printing'=>'bg-pink-600','uv-dtf'=>'bg-violet-600','dtf'=>'bg-sky-600','dtf-borderless'=>'bg-emerald-600','laser-engraving'=>'bg-amber-600'];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-{{ min(5, $allServices->count()) }} gap-4">
                @foreach($allServices as $s)
                    <a href="{{ route('services.show', $s['slug']) }}"
                       class="group flex items-center gap-4 bg-white rounded-2xl border {{ $s['slug'] === $service['slug'] ? 'border-pink-300 bg-pink-50 pointer-events-none' : 'border-slate-100 hover:border-pink-200 hover:shadow-md' }} p-5 transition-all">
                        <div class="w-10 h-10 rounded-xl {{ $siColors[$s['slug']] ?? 'bg-slate-600' }} flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'heroicon-o-'.($siIcons[$s['slug']] ?? 'star')" class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-sm text-slate-950 group-hover:text-[#EC268F] transition-colors truncate">{{ $s['name'] }}</p>
                            @if($s['slug'] === $service['slug'])
                                <p class="text-[10px] font-bold text-pink-600 uppercase tracking-wide">Current</p>
                            @else
                                <p class="text-xs text-slate-400 truncate">{{ \Illuminate\Support\Str::limit($s['summary'] ?? '', 45) }}</p>
                            @endif
                        </div>
                        @if($s['slug'] !== $service['slug'])
                            <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-300 group-hover:text-pink-500 shrink-0 transition-colors" />
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>

</main>

@if(!in_array($service['slug'], ['direct-image-printing','dtf','uv-dtf','laser-engraving','dtf-borderless'], true))
<script>
(function(){
    const unitPrice = {{ json_encode((float)$service['price']) }};
    const qtyInput  = document.getElementById('quantity');
    const total     = document.getElementById('estimated_total');
    const dMethod   = document.getElementById('delivery_method');
    const dCity     = document.getElementById('delivery_city');
    const dAddr     = document.getElementById('delivery_address');
    const fmt = v => 'NGN ' + new Intl.NumberFormat('en-NG',{minimumFractionDigits:0,maximumFractionDigits:0}).format(v);
    const render = () => { if(total) total.textContent = fmt(Math.max(1,parseInt(qtyInput?.value||'1',10)) * unitPrice); };
    const toggle = () => { const p = (dMethod?.value||'') === 'Client Pickup'; if(dCity){dCity.required=!p;if(p)dCity.value='';} if(dAddr){dAddr.required=!p;if(p)dAddr.value='';} };
    qtyInput?.addEventListener('input', render);
    dMethod?.addEventListener('change', toggle);
    render(); toggle();
})();
</script>
@endif

@endsection
