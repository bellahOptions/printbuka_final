@extends('layouts.theme')

@section('title', $product->name.' | Printbuka')
@section('meta_description', \Illuminate\Support\Str::limit($product->short_description ?: $product->description, 155))
@section('og_type', 'product')
@section('og_image', $product->featuredImageUrl() ?: asset('logo.png'))

@section('content')
    @php
        $name = strtolower($product->name);
        $galleryImages = $product->additionalImageUrls();
        $image = $product->featuredImageUrl() ?? match (true) {
            str_contains($name, 'business') => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=1200&q=80',
            str_contains($name, 'flyer') => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1200&q=80',
            str_contains($name, 'sticker') => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=1200&q=80',
            str_contains($name, 'brochure') => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=1200&q=80',
            str_contains($name, 'letterhead') => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
            str_contains($name, 'gift'), str_contains($name, 'mug'), str_contains($name, 'shirt'), str_contains($name, 'tote') => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=1200&q=80',
            default => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80',
        };
    @endphp

    <main class="bg-white text-slate-900">
        <section class="bg-[#f4fbfb] py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:px-8">
                <div>
                    <a href="{{ route('products.index') }}" class="text-sm font-black text-pink-700 hover:text-pink-800">Back to all products</a>
                    <p class="mt-6 inline-flex rounded-md bg-white px-4 py-2 text-sm font-black text-pink-700 shadow-sm">MOQ {{ $product->moq }}</p>
                    <h1 class="mt-5 max-w-3xl break-words text-4xl leading-tight text-slate-950 sm:text-5xl lg:text-6xl">{{ $product->name }}</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">{{ $product->description }}</p>

                    <div class="mt-8 grid gap-3 text-sm font-bold text-slate-700 sm:grid-cols-2">
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Paper: {{ $product->paper_type }}</div>
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Size: {{ $product->paper_size }}</div>
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Density: {{ $product->paper_density }}</div>
                        <div class="rounded-md bg-white px-4 py-3 shadow-sm">Finishing: {{ $product->finishing }}</div>
                    </div>
                </div>

                <div>
                    <img src="{{ $image }}" alt="{{ $product->name }}" class="h-[280px] w-full rounded-md object-cover shadow-2xl shadow-cyan-900/10 sm:h-[440px]" />
                    @if (! empty($galleryImages))
                        <div class="mt-4 grid grid-cols-3 gap-3">
                            @foreach ($galleryImages as $galleryImage)
                                <img src="{{ $galleryImage }}" alt="{{ $product->name }} gallery image" class="h-24 w-full rounded-md border border-slate-200 object-cover bg-white" />
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-5 rounded-md border border-slate-200 bg-white p-6 shadow-lg">
                        @if ($product->hasAvailablePrice())
                            <p class="text-sm font-bold text-slate-500">starting at</p>
                            <p class="mt-1 text-4xl font-black text-pink-700">NGN {{ number_format($product->price, 2) }}</p>
                            <p class="mt-2 text-sm text-slate-600">per MOQ of {{ $product->moq }}</p>
                            <a href="{{ route('orders.create', $product) }}" class="mt-6 inline-flex w-full justify-center rounded-md bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Start Order</a>
                        @else
                            <p class="text-sm font-bold text-slate-500">pricing</p>
                            <p class="mt-1 text-3xl font-black text-pink-700">Request quotation</p>
                            <p class="mt-2 text-sm text-slate-600">Our team will price this product after reviewing your quantity, material, and artwork details.</p>
                            <a href="{{ $product->quoteRequestUrl() }}" class="mt-6 inline-flex w-full justify-center rounded-md bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Request Quote</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div class="rounded-md border border-slate-200 p-6">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Step 1</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Confirm quantity</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Start from the minimum order quantity and scale up for bulk pricing.</p>
                </div>
                <div class="rounded-md border border-slate-200 p-6">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Step 2</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Share artwork notes</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Tell the team what you have ready, what needs checking and where it should be delivered.</p>
                </div>
                <div class="rounded-md border border-slate-200 p-6">
                    <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Step 3</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Get confirmation</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Your request is saved for follow-up, artwork review and production guidance.</p>
                </div>
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section class="bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">More Products</p>
                    <h2 class="mt-2 text-4xl text-slate-950">You may also need.</h2>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($relatedProducts as $relatedProduct)
                            <a href="{{ route('products.show', $relatedProduct) }}" class="rounded-md border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                                <h3 class="font-black text-slate-950">{{ $relatedProduct->name }}</h3>
                                <p class="mt-2 min-h-12 text-sm leading-6 text-slate-600">{{ $relatedProduct->short_description }}</p>
                                <p class="mt-4 text-lg font-black text-pink-700">{{ $relatedProduct->hasAvailablePrice() ? 'NGN '.number_format($relatedProduct->price, 2) : 'Request quote' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection
