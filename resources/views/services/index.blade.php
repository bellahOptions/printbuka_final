@extends('layouts.new-app')

@section('title', 'Print Services | Printbuka — Direct Image, UV-DTF, DTF & Laser Engraving')
@section('meta_description', 'Professional print services including direct image printing, UV-DTF transfers, DTF fabric printing and laser engraving. Clear pricing, trackable orders, nationwide delivery.')

@push('head')
<style>
.svc-slide { position:absolute; inset:0; transition: opacity 1s ease-in-out; }
.svc-dot   { width:8px; height:8px; border-radius:9999px; background:rgba(255,255,255,0.35); transition: all .3s; cursor:pointer; }
.svc-dot.active { background:#EC268F; width:24px; }
</style>
@endpush

@section('content')
<main>

    {{-- ===== HERO WITH SLIDESHOW ===== --}}
    @php
        $indexSlides = [
            ['img' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1600&q=80', 'label' => 'Direct Image Printing'],
            ['img' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1600&q=80', 'label' => 'Branded Merchandise'],
            ['img' => 'https://images.unsplash.com/photo-1524638431109-93d95c968f03?auto=format&fit=crop&w=1600&q=80', 'label' => 'DTF Fabric Printing'],
            ['img' => 'https://images.unsplash.com/photo-1590005354167-6da97870c757?auto=format&fit=crop&w=1600&q=80', 'label' => 'Laser Engraving'],
            ['img' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1600&q=80', 'label' => 'UV-DTF Transfers'],
        ];
    @endphp

    <section class="overflow-hidden">

        {{-- Slides --}}
        @foreach($indexSlides as $i => $slide)
            <div class="svc-slide {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}" aria-hidden="true" data-svc-slide="{{ $i }}">
                <img src="{{ $slide['img'] }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                {{-- Dark overlay --}}
                <div class="absolute inset-0 bg-slate-950/80"></div>
                {{-- Pink accent gradient --}}
                <div class="absolute inset-0 bg-gradient-to-br from-[#EC268F]/20 via-transparent to-cyan-900/15"></div>
            </div>
        @endforeach

        {{-- Content --}}
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="max-w-3xl">
                <h1 class="text-5xl sm:text-6xl font-black text-white leading-[1.05] mb-6">
                    Print Services Built<br>
                    for <span class="text-[#EC268F]">Business Results</span>
                </h1>
                <p class="text-slate-300 text-xl leading-relaxed mb-10 max-w-2xl">
                    From direct image printing to laser engraving — clear pricing, file checks, production tracking and dependable delivery. No guesswork, no surprises.
                </p>
                <div class="flex flex-wrap gap-4 mb-14">
                    <a href="#services"
                       class="inline-flex items-center gap-2 bg-[#EC268F] hover:bg-pink-700 text-white font-black px-8 py-4 rounded-xl transition-colors text-sm shadow-lg shadow-pink-900/30">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                        Browse All Services
                    </a>
                    <a href="#how-it-works"
                       class="inline-flex items-center gap-2 border border-white/25 hover:border-white/60 text-white font-black px-8 py-4 rounded-xl transition-colors text-sm">
                        <x-heroicon-o-play-circle class="w-5 h-5" />
                        How It Works
                    </a>
                </div>

                
            </div>
        </div>

        {{-- Slide label + dots --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-3">
            <span id="svc-index-label" class="text-xs font-black text-white/60 uppercase tracking-widest transition-all">{{ $indexSlides[0]['label'] }}</span>
            <div class="flex gap-2" id="svc-index-dots">
                @foreach($indexSlides as $i => $slide)
                    <button class="svc-dot {{ $i === 0 ? 'active' : '' }}" data-svc-target="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>       
    </section>

    <script>
    (function(){
        const slides   = document.querySelectorAll('[data-svc-slide]');
        const dots     = document.querySelectorAll('[data-svc-target]');
        const label    = document.getElementById('svc-index-label');
        const labels   = @json(array_column($indexSlides, 'label'));
        let current    = 0, timer;

        function goTo(n){
            slides[current].classList.replace('opacity-100','opacity-0');
            dots[current].classList.remove('active');
            current = (n + slides.length) % slides.length;
            slides[current].classList.replace('opacity-0','opacity-100');
            dots[current].classList.add('active');
            if(label) label.textContent = labels[current];
        }

        dots.forEach(d => d.addEventListener('click', () => { clearInterval(timer); goTo(+d.dataset.svcTarget); timer = setInterval(() => goTo(current + 1), 5000); }));
        timer = setInterval(() => goTo(current + 1), 5000);
    })();
    </script>

    {{-- ===== TRUST BAR ===== --}}
    <section class="bg-slate-50 border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $trustItems = [
                        ['icon' => 'shield-check',             'title' => 'Transparent Pricing', 'desc' => 'Prices shown before checkout — no hidden fees'],
                        ['icon' => 'clock',                    'title' => 'Fast Turnaround',     'desc' => 'Most jobs complete in 3–7 business days'],
                        ['icon' => 'map-pin',                  'title' => 'Nationwide Delivery', 'desc' => 'Pickup or delivery to any state'],
                        ['icon' => 'arrow-path',               'title' => 'Order Tracking',      'desc' => 'Live status from submission to delivery'],
                    ];
                @endphp
                @foreach($trustItems as $t)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-pink-100 flex items-center justify-center shrink-0 mt-0.5">
                            <x-dynamic-component :component="'heroicon-o-'.$t['icon']" class="w-5 h-5 text-[#EC268F]" />
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $t['title'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $t['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== SERVICES CATALOG ===== --}}
    <section id="services" class="py-20 bg-slate-50 scroll-mt-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-14">
                <span class="inline-block bg-pink-100 text-[#EC268F] text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">Service Catalog</span>
                <h2 class="text-4xl font-black text-slate-950 leading-tight mb-3">Choose Your Service</h2>
                <p class="text-slate-500 text-lg max-w-2xl mx-auto">Every service includes file review, production tracking and a dedicated support contact for your job.</p>
            </div>

            @php
                $serviceIcons = [
                    'direct-image-printing' => 'printer',
                    'uv-dtf'                => 'sparkles',
                    'dtf'                   => 'swatch',
                    'dtf-borderless'        => 'rectangle-group',
                    'laser-engraving'       => 'bolt',
                ];
                $serviceColors = [
                    'direct-image-printing' => ['bg' => 'bg-pink-600',    'light' => 'bg-pink-50',    'text' => 'text-pink-600',    'border' => 'border-pink-200'],
                    'uv-dtf'                => ['bg' => 'bg-violet-600',  'light' => 'bg-violet-50',  'text' => 'text-violet-600',  'border' => 'border-violet-200'],
                    'dtf'                   => ['bg' => 'bg-sky-600',     'light' => 'bg-sky-50',     'text' => 'text-sky-600',     'border' => 'border-sky-200'],
                    'dtf-borderless'        => ['bg' => 'bg-emerald-600', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
                    'laser-engraving'       => ['bg' => 'bg-amber-600',   'light' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
                ];
            @endphp

            <div class="grid lg:grid-cols-2 gap-7">
                @foreach($services as $service)
                    @php
                        $icon   = $serviceIcons[$service['slug']]  ?? 'star';
                        $colors = $serviceColors[$service['slug']] ?? ['bg' => 'bg-pink-600', 'light' => 'bg-pink-50', 'text' => 'text-pink-600', 'border' => 'border-pink-200'];
                    @endphp

                    <article class="group bg-white rounded-3xl border border-slate-100 hover:border-pink-200 hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col">
                        {{-- Accent header --}}
                        <div class="{{ $colors['bg'] }} px-7 pt-7 pb-0 relative">
                            <div class="flex items-start justify-between mb-5">
                                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                                    <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-7 h-7 text-white" />
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 text-white px-3 py-1.5 rounded-full border border-white/30">
                                    {{ $service['hero_kicker'] }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-white leading-snug mb-2">{{ $service['name'] }}</h3>
                            <p class="text-white/80 text-sm leading-relaxed pb-5">{{ $service['summary'] }}</p>
                            @if(!empty($service['pricing_factors']))
                                <div class="flex flex-wrap gap-2 pb-6">
                                    @foreach($service['pricing_factors'] as $factor)
                                        <span class="text-[10px] font-black text-white/90 bg-white/15 border border-white/20 px-2.5 py-1 rounded-full">{{ $factor }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="p-7 flex-1 flex flex-col gap-5">
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest {{ $colors['text'] }} mb-3">What You Get</p>
                                    <ul class="space-y-2">
                                        @foreach($service['features'] as $feat)
                                            <li class="flex items-start gap-2 text-sm text-slate-700">
                                                <x-heroicon-s-check-circle class="w-4 h-4 {{ $colors['text'] }} shrink-0 mt-0.5" />
                                                {{ $feat }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest {{ $colors['text'] }} mb-3">Best For</p>
                                    <ul class="space-y-2">
                                        @foreach($service['use_cases'] as $uc)
                                            <li class="flex items-start gap-2 text-sm text-slate-700">
                                                <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                                                {{ $uc }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            @if(!empty($service['proof_points']))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service['proof_points'] as $point)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold {{ $colors['text'] }} {{ $colors['light'] }} {{ $colors['border'] }} border px-3 py-1.5 rounded-full">
                                            <x-heroicon-s-check class="w-3 h-3" />
                                            {{ $point }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($service['price']) && $service['price'] > 0)
                                <div class="rounded-xl {{ $colors['light'] }} {{ $colors['border'] }} border px-4 py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Starting from</p>
                                        <p class="text-xl font-black {{ $colors['text'] }}">NGN {{ number_format((float)$service['price'], 0) }}</p>
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">Variable pricing applies</span>
                                </div>
                            @endif

                            <div class="mt-auto flex gap-3">
                                <a href="{{ route('services.show', $service['slug']) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 {{ $colors['bg'] }} hover:opacity-90 text-white font-black text-sm py-3.5 rounded-xl transition-all">
                                    <x-heroicon-o-shopping-cart class="w-4 h-4" />
                                    Order This Service
                                </a>
                                <a href="{{ route('services.show', $service['slug']) }}"
                                   class="inline-flex items-center justify-center gap-1 border {{ $colors['border'] }} {{ $colors['text'] }} font-black text-sm px-4 py-3.5 rounded-xl hover:{{ $colors['light'] }} transition-colors">
                                    Details
                                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== HOW IT WORKS ===== --}}
    <section id="how-it-works" class="py-20 bg-white scroll-mt-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-block bg-pink-100 text-[#EC268F] text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">Simple Process</span>
                <h2 class="text-4xl font-black text-slate-950 mb-3">From Brief to Delivery</h2>
                <p class="text-slate-500 text-lg max-w-xl mx-auto">Four steps stand between your order and your finished product — every one tracked and visible.</p>
            </div>
            <div class="relative">
                <div class="hidden lg:block absolute top-10 left-[calc(12.5%+28px)] right-[calc(12.5%+28px)] h-0.5 bg-gradient-to-r from-pink-200 via-pink-400 to-pink-200 pointer-events-none"></div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach([
                        ['icon' => 'document-text',   'num' => '01', 'title' => 'Submit Your Brief',  'desc' => 'Choose a service, share your quantity, options, and upload your design.'],
                        ['icon' => 'currency-dollar',  'num' => '02', 'title' => 'Review & Pay',       'desc' => 'We confirm your file, lock production scope, and you pay via Paystack.'],
                        ['icon' => 'cog-6-tooth',      'num' => '03', 'title' => 'Production Begins',  'desc' => 'Your job moves through 6 tracked phases — design, print, QC, packaging.'],
                        ['icon' => 'truck',            'num' => '04', 'title' => 'Pickup or Delivery', 'desc' => 'Collect in person or get delivery nationwide with status updates.'],
                    ] as $step)
                        <div class="bg-white rounded-2xl border border-slate-100 hover:border-pink-200 hover:shadow-lg transition-all p-6 text-center group">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#EC268F] to-pink-700 flex items-center justify-center mx-auto mb-4 shadow-md shadow-pink-200 group-hover:scale-110 transition-transform">
                                <x-dynamic-component :component="'heroicon-o-'.$step['icon']" class="w-6 h-6 text-white" />
                            </div>
                            <span class="text-[10px] font-black text-pink-400 uppercase tracking-widest">Step {{ $step['num'] }}</span>
                            <h3 class="text-base font-black text-slate-950 mt-1 mb-2">{{ $step['title'] }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===== WHY PRINTBUKA ===== --}}
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">
                <div class="mb-12 lg:mb-0">
                    <span class="inline-block bg-pink-100 text-[#EC268F] text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-5">Why Choose Us</span>
                    <h2 class="text-4xl font-black text-slate-950 leading-tight mb-5">
                        Production Quality You Can<br><span class="text-[#EC268F]">Bet Your Brand On</span>
                    </h2>
                    <p class="text-slate-500 text-lg leading-relaxed mb-8">
                        Printbuka runs a structured print shop — every order handled by a specialist team with defined responsibilities across each production phase.
                    </p>
                    <div class="flex flex-col gap-4">
                        @foreach([
                            ['icon' => 'user-group',               'title' => 'Specialist Teams',      'desc' => 'Each phase handled by the right person — designer, operator, QC, logistics.'],
                            ['icon' => 'eye',                      'title' => 'Full Order Visibility',  'desc' => 'Log in to see exactly where your job is in the 6-phase workflow at any time.'],
                            ['icon' => 'banknotes',                'title' => 'No Hidden Charges',     'desc' => 'Prices set by management and shown before payment. No surprises.'],
                            ['icon' => 'chat-bubble-left-right',   'title' => 'Dedicated Support',     'desc' => 'Raise a ticket, get a response. Our support team is tied to your specific job.'],
                        ] as $r)
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center shrink-0">
                                    <x-dynamic-component :component="'heroicon-o-'.$r['icon']" class="w-5 h-5 text-[#EC268F]" />
                                </div>
                                <div>
                                    <p class="font-black text-slate-950 text-sm">{{ $r['title'] }}</p>
                                    <p class="text-sm text-slate-500 mt-0.5 leading-relaxed">{{ $r['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-slate-950 rounded-3xl p-8 text-white relative overflow-hidden">
                        <div class="pointer-events-none absolute top-0 right-0 w-48 h-48 rounded-full bg-pink-600/10 -translate-y-1/3 translate-x-1/4 blur-2xl"></div>
                        <x-heroicon-s-chat-bubble-bottom-center-text class="w-8 h-8 text-pink-500 mb-4" />
                        <p class="text-lg font-bold leading-relaxed text-slate-200 mb-6">
                            "The order tracking made it easy to follow the job from the moment we paid. Our branded items arrived exactly as expected — clean print, clean finish."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-pink-600 flex items-center justify-center font-black text-white text-sm">A</div>
                            <div>
                                <p class="font-black text-white text-sm">Adaeze O.</p>
                                <p class="text-xs text-slate-400">Brand Manager, Lagos</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach([['6','Production Phases','text-[#EC268F]'],['100%','Tracked Orders','text-emerald-600'],['5+','Service Types','text-amber-600'],['3–7','Day Turnaround','text-sky-600']] as [$val,$lbl,$cls])
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 text-center hover:border-pink-200 transition-colors">
                                <p class="text-3xl font-black {{ $cls }}">{{ $val }}</p>
                                <p class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-wide">{{ $lbl }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FAQ ===== --}}
    <section class="py-20 bg-white">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block bg-pink-100 text-[#EC268F] text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">Quick Answers</span>
                <h2 class="text-4xl font-black text-slate-950">Common Questions</h2>
            </div>
            <div class="divide-y divide-slate-100" x-data="{ open: null }">
                @foreach([
                    ['q'=>'How do I get a price for my job?',        'a'=>"Click any service above to see the order form. Pricing is calculated based on quantity, paper/material type and size. You'll see the total before you pay."],
                    ['q'=>'Do I need to create an account?',          'a'=>"Yes — a free account lets you track your order, access your invoice, and communicate with our team. Sign up takes under 30 seconds."],
                    ['q'=>'How do I submit my artwork?',              'a'=>"Upload files directly on the order page. We accept PDF, AI, PSD, and high-resolution JPG/PNG. Our team checks files before production begins."],
                    ['q'=>'What if my files need corrections?',       'a'=>"We review every file before production. If adjustments are needed, we'll contact you directly before any work starts."],
                    ['q'=>'Can I collect my order in person?',        'a'=>"Yes. Choose \"Client Pickup\" at checkout and we'll notify you when your order is ready for collection at our production facility."],
                    ['q'=>'What payment methods are accepted?',       'a'=>"We accept all major debit cards and bank transfers via Paystack. All transactions are encrypted and receipts are issued automatically."],
                ] as $i => $faq)
                    <div class="py-5">
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between gap-4 text-left group">
                            <span class="font-black text-slate-950 group-hover:text-[#EC268F] transition-colors">{{ $faq['q'] }}</span>
                            <span class="shrink-0 w-8 h-8 rounded-full bg-slate-100 group-hover:bg-pink-100 flex items-center justify-center transition-colors">
                                <x-heroicon-o-plus class="w-4 h-4 text-slate-500 group-hover:text-[#EC268F]" x-show="open !== {{ $i }}" />
                                <x-heroicon-o-minus class="w-4 h-4 text-[#EC268F]" x-show="open === {{ $i }}" x-cloak />
                            </span>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition x-cloak class="mt-3 text-slate-500 text-sm leading-relaxed pr-12">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== BOTTOM CTA ===== --}}
    <section class="py-20 bg-slate-50">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-[#1a002e] to-slate-950 px-8 sm:px-14 py-14 text-center">
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-16 right-0 w-72 h-72 rounded-full bg-pink-600/10 translate-x-1/4 blur-2xl"></div>
                    <div class="absolute -bottom-16 left-0 w-60 h-60 rounded-full bg-cyan-500/10 -translate-x-1/4 blur-2xl"></div>
                </div>
                <div class="relative">
                    <div class="inline-block bg-pink-600/20 text-pink-400 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border border-pink-600/30 mb-6">Ready to print?</div>
                    <h2 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">Start Your Order Today</h2>
                    <p class="text-slate-400 text-lg max-w-xl mx-auto mb-10 leading-relaxed">Pick a service, submit your brief and let our production team handle the rest.</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="#services"
                           class="inline-flex items-center gap-2 bg-[#EC268F] hover:bg-pink-700 text-white font-black px-8 py-4 rounded-xl transition-colors text-sm shadow-lg shadow-pink-900/30">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                            Browse All Services
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 border border-white/25 hover:border-white/60 text-white font-black px-8 py-4 rounded-xl transition-colors text-sm">
                            <x-heroicon-o-user-plus class="w-5 h-5" />
                            Create Free Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
