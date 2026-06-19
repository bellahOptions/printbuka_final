@extends('layouts.theme')

@section('title', $product->name.' | Printbuka')
@section('meta_description', \Illuminate\Support\Str::limit($product->short_description ?: $product->description, 155))
@section('og_type', 'product')
@section('og_image', $product->featuredImageUrl() ?: asset('logo.png'))

@section('content')
    @php
        $galleryImages = $product->additionalImageUrls();
        $primaryImage = $product->featuredImageUrl() ?? ($galleryImages[0] ?? null);
        $galleryImages = collect($galleryImages)
            ->when($primaryImage !== null, fn ($images) => $images->filter(fn ($url) => $url !== $primaryImage))
            ->values()
            ->all();

        $image = $primaryImage ?? asset('img/product-placeholder.svg');
    @endphp

    <main class="bg-white text-slate-900">
        <section class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] items-start">
                    <div class="space-y-6">
                        <a href="{{ route('products.index') }}" class="text-sm font-black uppercase tracking-[0.24em] text-pink-700 transition hover:text-pink-800">Back to catalog</a>
                        <div class="inline-flex items-center gap-3 rounded-full bg-white px-4 py-2 text-sm font-black text-slate-900 shadow-sm ring-1 ring-slate-200">
                            <span>MOQ</span>
                            <span class="rounded-full bg-pink-100 px-3 py-1 text-pink-700">{{ $product->moq }}</span>
                        </div>
                        <div class="space-y-3 rounded-[2rem] bg-white p-8 shadow-xl ring-1 ring-slate-200">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-700">{{ $product->category?->name ?? 'Print product' }}</p>
                                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">{{ $product->name }}</h1>
                            </div>
                            <p class="max-w-3xl text-lg leading-8 text-slate-600">{{ $product->short_description ?: $product->description }}</p>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-3xl bg-slate-50 p-5">
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Price</p>
                                    <p class="mt-3 text-2xl font-black text-pink-700">{{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 2) : 'Contact us' }}</p>
                                </div>
                                <div class="rounded-3xl bg-slate-50 p-5">
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Finish</p>
                                    <p class="mt-3 text-lg font-bold text-slate-900">{{ $product->finishing ?: 'Standard' }}</p>
                                </div>
                                <div class="rounded-3xl bg-slate-50 p-5">
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Material</p>
                                    <p class="mt-3 text-lg font-bold text-slate-900">{{ $product->paper_type ?: 'N/A' }}</p>
                                </div>
                                <div class="rounded-3xl bg-slate-50 p-5">
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Size</p>
                                    <p class="mt-3 text-lg font-bold text-slate-900">{{ $product->paper_size ?: 'Custom' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[2rem] bg-white p-8 shadow-xl ring-1 ring-slate-200">
                            <h2 class="text-xl font-black text-slate-950">Why choose this product?</h2>
                            <ul class="mt-6 space-y-4 text-sm leading-6 text-slate-600">
                                <li class="flex gap-3"><span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-pink-100 text-pink-700">✓</span> Clear print quality for every run.</li>
                                <li class="flex gap-3"><span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">✓</span> Fast review and delivery coordination.</li>
                                <li class="flex gap-3"><span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">✓</span> Flexible quantity, material, and artwork support.</li>
                            </ul>
                        </div>
                    </div>

                    <aside class="space-y-6">
                        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-cyan-50 via-white to-slate-100 shadow-xl ring-1 ring-slate-200">
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-[420px] w-full object-cover" onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';this.style.objectFit='contain';this.style.padding='10%';this.style.background='#f1f5f9';" />
                        </div>

                        @if (! empty($galleryImages))
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($galleryImages as $galleryImage)
                                    <img src="{{ $galleryImage }}" alt="{{ $product->name }} gallery image" class="h-24 w-full rounded-3xl border border-slate-200 object-cover bg-white" onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                @endforeach
                            </div>
                        @endif

                        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Order</p>
                                        <p class="mt-2 text-3xl font-black text-pink-700">{{ $product->hasAvailablePrice() ? 'NGN '.number_format($product->price, 2) : 'Contact us' }}</p>
                                    </div>
                                    <span class="rounded-full bg-pink-50 px-4 py-2 text-sm font-black text-pink-700">MOQ {{ $product->moq }}</span>
                                </div>

                                <p class="text-sm leading-6 text-slate-600">{{ $product->hasAvailablePrice() ? 'Order now and our team will prepare your artwork and delivery plan.' : 'Contact us to discuss custom pricing and options.' }}</p>

                                @if ($product->hasAvailablePrice())
                                    <a href="{{ route('orders.create', $product) }}" class="inline-flex w-full items-center justify-center rounded-3xl bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Start order</a>
                                @else
                                    <a href="{{ route('services.index') }}" class="inline-flex w-full items-center justify-center rounded-3xl bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">View Services</a>
                                @endif

                                <div class="rounded-3xl bg-slate-50 p-4 text-sm text-slate-600">
                                    <p class="font-black text-slate-900">Need help?</p>
                                    <p class="mt-1">Our support team can help with file specs, quantity recommendations, and delivery planning.</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-pink-700">Step 1</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">Confirm quantity</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Choose your minimum quantity and scale for better pricing and speed.</p>
                    </article>
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-700">Step 2</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">Share artwork notes</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Provide artwork details, print preferences, and delivery requirements for a smooth order.</p>
                    </article>
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Step 3</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">Receive confirmation</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">We will confirm quantity, artwork review, and production timing before you approve.</p>
                    </article>
                </div>
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section class="bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-pink-700">More Products</p>
                    <h2 class="mt-2 text-4xl font-black text-slate-950">You may also need</h2>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($relatedProducts as $relatedProduct)
                            <a href="{{ route('products.show', $relatedProduct) }}" class="rounded-[1.75rem] border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                                <h3 class="font-black text-slate-950">{{ $relatedProduct->name }}</h3>
                                <p class="mt-2 min-h-[3rem] text-sm leading-6 text-slate-600">{{ $relatedProduct->short_description }}</p>
                                <p class="mt-4 text-lg font-black text-pink-700">{{ $relatedProduct->hasAvailablePrice() ? 'NGN '.number_format($relatedProduct->price, 2) : 'Contact us' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection
