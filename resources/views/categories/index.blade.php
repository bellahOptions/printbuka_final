@extends('layouts.theme')

@section('title', 'Product Categories | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-4xl font-black text-slate-950">Product Categories</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Only categories with active products are displayed.</p>
        </div>

        @if($categories->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($categories as $category)
                    @php
                        $image = $category->image ?: 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('products.category', $category) }}" class="block">
                            <img src="{{ $image }}" alt="{{ $category->name }}" class="h-48 w-full object-cover" />
                        </a>
                        <div class="p-5">
                            <h2 class="text-xl font-black text-slate-950">
                                <a href="{{ route('products.category', $category) }}" class="hover:text-pink-600">{{ $category->name }}</a>
                            </h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $category->description ?: 'Explore available products in this category.' }}</p>
                            <p class="mt-3 text-xs font-bold uppercase tracking-wide text-pink-600">
                                {{ $category->active_products_count }} {{ \Illuminate\Support\Str::plural('active product', $category->active_products_count) }}
                            </p>

                            @if($category->children->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('products.category', $child) }}" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 hover:bg-pink-100 hover:text-pink-700">
                                            {{ $child->name }} ({{ $child->active_products_count }})
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <p class="text-lg font-black text-slate-800">No active categories available right now.</p>
                <p class="mt-2 text-sm text-slate-500">Check back shortly as products are being updated.</p>
            </div>
        @endif
    </div>
</main>
@endsection
