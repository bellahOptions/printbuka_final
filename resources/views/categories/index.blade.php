@extends('layouts.theme')

@section('title', 'Product Categories | Printbuka')

@section('content')
    <main class="bg-white text-slate-900">
        <section class="bg-[#f4fbfb] py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:px-8">
                <div class="flex flex-col justify-center">
                    <p class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-black text-pink-700 shadow-sm">Product Categories</p>
                    <h1 class="mt-5 max-w-3xl text-5xl leading-tight text-slate-950 sm:text-6xl">Find the right print, packaging or gift path.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Browse Printbuka categories by job type, from everyday business stationery to branded gifts and event materials.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">View All Products</a>
                        <a href="{{ route('partners.create') }}" class="rounded-md border border-slate-200 bg-white px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Partner With Us</a>
                    </div>
                </div>

                <img
                    src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=1200&q=80"
                    alt="Printed brand materials arranged together"
                    class="h-[440px] w-full rounded-md object-cover shadow-2xl shadow-cyan-900/10"
                />
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <article class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" class="h-56 w-full object-cover" />
                            <div class="p-6">
                                <p class="text-xs font-black uppercase tracking-wide text-pink-700">{{ $category['tag'] }}</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950">{{ $category['name'] }}</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $category['description'] }}</p>

                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach ($category['products'] as $product)
                                        <span class="rounded-md bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600">{{ $product }}</span>
                                    @endforeach
                                </div>

                                <a href="{{ route('products.index') }}#catalog" class="mt-6 inline-flex w-full justify-center rounded-md bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Browse Products</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-slate-950 py-16 text-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Need help choosing?</p>
                    <h2 class="mt-2 text-4xl">Start from the outcome, not the product name.</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Launching</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Use flyers, stickers, packaging and branded gifts.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Hosting</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Use banners, programmes, name tags and giveaways.</p>
                    </div>
                    <div class="rounded-md bg-white p-5 text-slate-950">
                        <h3 class="font-black">Gifting</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Use mugs, shirts, gift sets and custom delivery packs.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
