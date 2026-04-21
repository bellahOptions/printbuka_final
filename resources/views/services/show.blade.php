@extends('layouts.theme')

@section('title', $service['name'].' | Printbuka Services')

@section('content')
    @php
        $heroKicker = (string) ($service['hero_kicker'] ?? 'Professional Service');
        $heroTitle = (string) ($service['hero_title'] ?? $service['name']);
        $heroSummary = (string) ($service['hero_summary'] ?? $service['summary'] ?? '');
        $proofPoints = (array) ($service['proof_points'] ?? []);
        $features = (array) ($service['features'] ?? []);
        $useCases = (array) ($service['use_cases'] ?? []);
        $processSteps = (array) ($service['process_steps'] ?? []);
        $trustPoints = (array) ($service['trust_points'] ?? []);
        $pricingMode = (string) ($service['pricing_mode'] ?? 'fixed');
        $pricingFactors = (array) ($service['pricing_factors'] ?? []);
        $productSectionRedirect = match ($service['slug']) {
            'uv-dtf' => route('products.index').'#uv-dtf-products',
            'laser-engraving' => route('products.index').'#laser-engraving-products',
            default => null,
        };
        $usesLivewireForm = in_array($service['slug'], ['direct-image-printing', 'dtf'], true);
        $usesCatalogRedirect = filled($productSectionRedirect);
        $primaryCtaUrl = $usesCatalogRedirect ? $productSectionRedirect : '#service-order-form';
        $primaryCtaLabel = $usesCatalogRedirect ? 'Browse Related Products' : 'Place Order Now';
    @endphp

    <main class="bg-[#f4fbfb] px-4 py-14 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto max-w-7xl space-y-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-cyan-950/10">
                <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-cyan-900 px-6 py-10 text-white sm:px-10">
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-cyan-100 transition hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to services
                    </a>
                    <p class="mt-7 inline-flex rounded-md bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-cyan-100">{{ $heroKicker }}</p>
                    <h1 class="mt-5 max-w-4xl text-4xl font-black leading-tight sm:text-5xl">{{ $heroTitle }}</h1>
                    <p class="mt-5 max-w-3xl text-base leading-7 text-slate-100/90">{{ $heroSummary }}</p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ $primaryCtaUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-500 px-6 text-sm font-black text-white transition hover:bg-pink-600">{{ $primaryCtaLabel }}</a>
                        <a href="{{ route('services.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-white/30 px-6 text-sm font-black text-white transition hover:border-white">Explore All Services</a>
                    </div>
                </div>

                <div class="grid gap-4 bg-white px-6 py-6 sm:grid-cols-3 sm:px-10">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        @if ($pricingMode === 'variable')
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Pricing Model</p>
                            <p class="mt-2 text-2xl font-black text-pink-700">Variable Pricing</p>
                            @if ($pricingFactors !== [])
                                <p class="mt-1 text-xs font-semibold text-slate-500">Based on: {{ implode(', ', $pricingFactors) }}.</p>
                            @endif
                        @else
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Starting Price</p>
                            <p class="mt-2 text-2xl font-black text-pink-700">NGN {{ number_format((float) $service['price'], 2) }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Displayed using current pricing configuration.</p>
                        @endif
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Production Confidence</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">Quality checks and secure order tracking from request to completion.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Business Ready</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">Suitable for startup, SME, enterprise, and recurring campaign orders.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">Why Clients Choose Printbuka</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">Credibility You Can Trust</h2>
                    <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                        @forelse ($proofPoints as $point)
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-pink-500"></span>
                                <span>{{ $point }}</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-pink-500"></span>
                                <span>Transparent communication and dependable delivery timelines.</span>
                            </li>
                        @endforelse
                    </ul>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">Service Highlights</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">What You Get</h2>
                    <ul class="mt-6 space-y-3 text-sm font-semibold text-slate-700">
                        @forelse ($features as $feature)
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-cyan-600"></span>
                                <span>{{ $feature }}</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-cyan-600"></span>
                                <span>Service details and quality standards are confirmed before production.</span>
                            </li>
                        @endforelse
                    </ul>
                </article>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
                <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">How It Works</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">Simple, Structured Delivery Flow</h2>
                    <ol class="mt-6 space-y-4">
                        @forelse ($processSteps as $index => $step)
                            <li class="flex items-start gap-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">{{ $index + 1 }}</span>
                                <span class="pt-1 text-sm font-semibold leading-6 text-slate-700">{{ $step }}</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">1</span>
                                <span class="pt-1 text-sm font-semibold leading-6 text-slate-700">Complete the form and confirm your order details.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">2</span>
                                <span class="pt-1 text-sm font-semibold leading-6 text-slate-700">Proceed with secure payment and order validation.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-pink-100 text-xs font-black text-pink-700">3</span>
                                <span class="pt-1 text-sm font-semibold leading-6 text-slate-700">Production and delivery updates are shared as your job progresses.</span>
                            </li>
                        @endforelse
                    </ol>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-pink-700">Best For</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Use Cases</h2>
                    <ul class="mt-5 space-y-3 text-sm font-semibold text-slate-700">
                        @forelse ($useCases as $useCase)
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-slate-700"></span>
                                <span>{{ $useCase }}</span>
                            </li>
                        @empty
                            <li class="flex items-start gap-2">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-slate-700"></span>
                                <span>Ideal for businesses that need consistent output and clear communication.</span>
                            </li>
                        @endforelse
                    </ul>

                    <div class="mt-7 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-4">
                        <p class="text-xs font-black uppercase tracking-wide text-cyan-800">Trust Promise</p>
                        <ul class="mt-3 space-y-2 text-sm font-semibold text-cyan-900">
                            @forelse ($trustPoints as $trustPoint)
                                <li>{{ $trustPoint }}</li>
                            @empty
                                <li>Clear scope confirmation before production begins.</li>
                                <li>Professional support from order placement to delivery.</li>
                            @endforelse
                        </ul>
                    </div>

                    <a href="{{ $primaryCtaUrl }}" class="mt-7 inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700">{{ $primaryCtaLabel }}</a>
                </article>
            </div>

            <div id="service-order-form" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                @if ($usesCatalogRedirect)
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Order Via Products</p>
                    <h2 class="mt-2 text-4xl text-slate-950">Order {{ $service['name'] }} from product catalog</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $service['name'] }} orders are handled from the products page so you can select exact items and specifications.
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="{{ $productSectionRedirect }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700">Go to {{ $service['name'] }} Products</a>
                        <a href="{{ route('products.index') }}#catalog" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-200 px-6 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Browse Full Catalog</a>
                    </div>
                @elseif ($service['slug'] === 'direct-image-printing')
                    @auth
                        <livewire:services.direct-image-order-form :service="$service" />
                    @else
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Login Required</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Sign in to place a Direct Image order</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">For security and order tracking, Direct Image Printing orders can only be submitted by authenticated users.</p>
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700">Sign In</a>
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-200 px-6 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Create Account</a>
                        </div>
                    @endauth
                @elseif ($service['slug'] === 'dtf')
                    @auth
                        <livewire:services.dtf-order-form :service="$service" />
                    @else
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Login Required</p>
                        <h2 class="mt-2 text-4xl text-slate-950">Sign in to place a DTF order</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">For security and order tracking, DTF orders can only be submitted by authenticated users.</p>
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700">Sign In</a>
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-200 px-6 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Create Account</a>
                        </div>
                    @endauth
                @else
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Order Form</p>
                    <h2 class="mt-2 text-4xl text-slate-950">Order {{ $service['name'] }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Complete the form and you will be redirected to Paystack to pay securely.</p>

                    @if (session('status') || session('warning'))
                        <p class="mt-5 rounded-md border {{ session('status') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }} px-4 py-3 text-sm font-bold">
                            {{ session('status') ?? session('warning') }}
                        </p>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                            <p class="font-black">Please fix the highlighted fields.</p>
                        </div>
                    @endif

                    <form action="{{ route('services.orders.store', $service['slug']) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="text-sm font-black text-slate-800">Quantity
                                <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="text-sm font-black text-slate-800">Delivery Method
                                <select id="delivery_method" name="delivery_method" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    @foreach ($deliveryMethods as $method)
                                        <option value="{{ $method }}" @selected(old('delivery_method', 'Client Pickup') === $method)>{{ $method }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm font-black text-slate-800">Customer Name
                                <input type="text" name="customer_name" value="{{ old('customer_name', $customer?->displayName()) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="text-sm font-black text-slate-800">Customer Email
                                <input type="email" name="customer_email" value="{{ old('customer_email', $customer?->email) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="text-sm font-black text-slate-800">Customer Phone
                                <input type="text" name="customer_phone" value="{{ old('customer_phone', $customer?->phone) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="text-sm font-black text-slate-800">Delivery City
                                <input id="delivery_city" type="text" name="delivery_city" value="{{ old('delivery_city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="sm:col-span-2 text-sm font-black text-slate-800">Delivery Address
                                <input id="delivery_address" type="text" name="delivery_address" value="{{ old('delivery_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            </label>
                            <label class="sm:col-span-2 text-sm font-black text-slate-800">Order Notes / Artwork Instructions
                                <textarea name="artwork_notes" rows="4" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">{{ old('artwork_notes') }}</textarea>
                            </label>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Estimated Total</p>
                            <p id="estimated_total" class="mt-1 text-2xl font-black text-pink-700">NGN {{ number_format((float) $service['price'], 2) }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Final invoice may include delivery adjustments when applicable.</p>
                        </div>

                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700">Proceed to Paystack</button>
                    </form>
                @endif
            </div>
        </section>
    </main>

    @if (! in_array($service['slug'], ['direct-image-printing', 'dtf', 'uv-dtf', 'laser-engraving'], true))
        <script>
            (() => {
                const unitPrice = {{ json_encode((float) $service['price']) }};
                const quantityInput = document.getElementById('quantity');
                const totalNode = document.getElementById('estimated_total');
                const deliveryMethodInput = document.getElementById('delivery_method');
                const deliveryCityInput = document.getElementById('delivery_city');
                const deliveryAddressInput = document.getElementById('delivery_address');

                const renderTotal = () => {
                    const quantity = Math.max(1, parseInt(quantityInput?.value || '1', 10));
                    const total = quantity * unitPrice;
                    totalNode.textContent = 'NGN ' + new Intl.NumberFormat('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total);
                };

                const toggleDeliveryFields = () => {
                    const isPickup = (deliveryMethodInput?.value || '') === 'Client Pickup';
                    if (deliveryCityInput) {
                        deliveryCityInput.required = !isPickup;
                        if (isPickup) deliveryCityInput.value = '';
                    }
                    if (deliveryAddressInput) {
                        deliveryAddressInput.required = !isPickup;
                        if (isPickup) deliveryAddressInput.value = '';
                    }
                };

                quantityInput?.addEventListener('input', renderTotal);
                deliveryMethodInput?.addEventListener('change', toggleDeliveryFields);

                renderTotal();
                toggleDeliveryFields();
            })();
        </script>
    @endif
@endsection
