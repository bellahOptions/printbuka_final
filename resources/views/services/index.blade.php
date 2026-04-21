@extends('layouts.theme')

@section('title', 'Services | Printbuka')
@section('meta_description', 'Explore Printbuka services including direct image printing, UV-DTF, DTF, and laser engraving.')

@section('content')
    @php
        $serviceCount = count($services);
        $fixedServicePrices = collect($services)
            ->filter(fn (array $service): bool => (string) ($service['pricing_mode'] ?? 'fixed') !== 'variable')
            ->pluck('price')
            ->filter(fn ($price): bool => is_numeric($price) && (float) $price > 0);
        $startingPrice = $fixedServicePrices->min();
    @endphp

    <style>
        .services-landing {
            --heading-font: "Sora", "Montserrat", "Segoe UI", sans-serif;
            --body-font: "Manrope", "Nunito Sans", "Segoe UI", sans-serif;
            font-family: var(--body-font);
        }

        .services-landing h1,
        .services-landing h2,
        .services-landing h3 {
            font-family: var(--heading-font);
            letter-spacing: -0.02em;
        }

        .services-fade-up {
            animation: services-fade-up 620ms cubic-bezier(0.21, 0.98, 0.6, 0.99) both;
        }

        .services-fade-up-delay-1 { animation-delay: 90ms; }
        .services-fade-up-delay-2 { animation-delay: 170ms; }
        .services-fade-up-delay-3 { animation-delay: 250ms; }

        @keyframes services-fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <main class="services-landing relative overflow-hidden bg-[#f3faf8] px-4 py-12 text-slate-900 sm:px-6 lg:px-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-56 w-56 rounded-full bg-cyan-300/30 blur-3xl"></div>
            <div class="absolute top-24 right-0 h-72 w-72 rounded-full bg-pink-200/40 blur-3xl"></div>
            <div class="absolute bottom-24 left-1/3 h-56 w-56 rounded-full bg-emerald-200/40 blur-3xl"></div>
        </div>

        <section class="relative mx-auto max-w-7xl space-y-10">
            <div class="services-fade-up overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-cyan-950/10">
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-cyan-900 px-7 py-10 text-white sm:px-10">
                    <p class="inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-cyan-100">Printbuka Services</p>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight sm:text-5xl">Our Services</h1>
                    <p class="mt-5 max-w-3xl text-base leading-7 text-slate-100/90">From direct image printing to engraving, we provide business-ready production with clear pricing, secure checkout, and dependable delivery support.</p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="#services-grid" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-500 px-6 text-sm font-black text-white transition hover:bg-pink-600">Place Order Now</a>
                        <a href="#how-it-works" class="inline-flex min-h-12 items-center justify-center rounded-md border border-white/40 px-6 text-sm font-black text-white transition hover:border-white">How It Works</a>
                    </div>
                </div>

                <div class="grid gap-4 bg-white px-7 py-7 sm:grid-cols-3 sm:px-10">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-500">Service Lines</p>
                        <p class="mt-2 text-2xl font-black text-slate-950">{{ $serviceCount }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-500">Starting From</p>
                        <p class="mt-2 text-2xl font-black text-pink-700">
                            @if ($startingPrice !== null)
                                NGN {{ number_format((float) $startingPrice, 2) }}
                            @else
                                Custom Pricing
                            @endif
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-500">Checkout</p>
                        <p class="mt-2 text-sm font-bold text-slate-700">Secure Paystack payment with order-linked invoice tracking.</p>
                    </div>
                </div>
            </div>

            <div class="services-fade-up services-fade-up-delay-1 grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-pink-700">Production Quality</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Output You Can Trust</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Every job moves through a controlled production process with visibility and update points.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-pink-700">Clear Pricing</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">No Guesswork</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Current prices are shown clearly before checkout so you can decide confidently.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-pink-700">Order Confidence</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Trackable Workflow</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Orders stay tied to invoice and status updates so you always know what is next.</p>
                </article>
            </div>

            <div id="services-grid" class="services-fade-up services-fade-up-delay-2 scroll-mt-28 space-y-5">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">Service Catalog</p>
                        <h2 class="mt-2 text-4xl font-black text-slate-950">Choose A Service and Start Ordering</h2>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($services as $service)
                        @php
                            $orderUrl = match ($service['slug']) {
                                'uv-dtf' => route('products.index').'#catalog',
                                'laser-engraving' => route('products.index').'#catalog',
                                default => route('services.show', $service['slug']).'#service-order-form',
                            };
                        @endphp
                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                            <p class="text-xs font-black uppercase tracking-[0.15em] text-pink-700">Service</p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950">{{ $service['name'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $service['summary'] }}</p>

                            <ul class="mt-5 space-y-2 text-sm font-semibold text-slate-700">
                                @foreach (array_slice((array) ($service['features'] ?? []), 0, 3) as $feature)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-2 h-1.5 w-1.5 rounded-full bg-cyan-600"></span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                @if (($service['pricing_mode'] ?? 'fixed') === 'variable')
                                    <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-500">Pricing Model</p>
                                    <p class="mt-1 text-xl font-black text-pink-700">Variable Pricing</p>
                                    @if (filled($service['pricing_factors'] ?? []))
                                        <p class="mt-1 text-xs font-semibold text-slate-500">Based on: {{ implode(', ', (array) $service['pricing_factors']) }}</p>
                                    @endif
                                @else
                                    <p class="text-xs font-black uppercase tracking-[0.15em] text-slate-500">Starting Price</p>
                                    <p class="mt-1 text-2xl font-black text-pink-700">NGN {{ number_format((float) $service['price'], 2) }}</p>
                                @endif
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a href="{{ route('services.show', $service['slug']) }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-slate-900 px-5 text-sm font-black text-white transition group-hover:bg-slate-950">View Service</a>
                                <a href="{{ $orderUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Place Order Now</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="how-it-works" class="services-fade-up services-fade-up-delay-3 grid gap-6 rounded-2xl border border-slate-200 bg-white p-7 shadow-sm lg:grid-cols-2 lg:p-10">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">How It Works</p>
                    <h2 class="mt-3 text-4xl font-black text-slate-950">From Brief to Delivery, Step by Step</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600">We designed the flow to keep communication clear, payment secure, and execution consistent across all service categories.</p>
                </div>

                <ol class="space-y-4">
                    <li class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">1</span>
                        <span class="pt-1 text-sm font-semibold text-slate-700">Choose your service and submit order details.</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">2</span>
                        <span class="pt-1 text-sm font-semibold text-slate-700">Review pricing, then complete secure payment.</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">3</span>
                        <span class="pt-1 text-sm font-semibold text-slate-700">Production begins with role-based processing and updates.</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">4</span>
                        <span class="pt-1 text-sm font-semibold text-slate-700">Pickup or delivery is completed according to your selected method.</span>
                    </li>
                </ol>
            </div>

            <div class="md:hidden fixed inset-x-0 bottom-4 z-40 px-4">
                <a href="#services-grid" class="inline-flex min-h-12 w-full items-center justify-center rounded-md bg-pink-600 px-5 text-sm font-black text-white shadow-lg shadow-pink-900/30 transition hover:bg-pink-700">
                    Place Order Now
                </a>
            </div>
        </section>
    </main>
@endsection
