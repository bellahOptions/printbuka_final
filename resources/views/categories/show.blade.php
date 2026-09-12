@extends('layouts.new-app')

@section('title', $category->name . ' | Printbuka')
@section('meta_description', \Illuminate\Support\Str::limit($category->description ?: ('Browse available '.strtolower($category->name).' products from Printbuka.'), 155))
@section('og_image', $category->imageUrl() ?: asset('logo.png'))

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition">Products</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-slate-700">{{ $category->name }}</span>
        </div>

        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-xl">
            <div class="p-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="mb-3 inline-flex rounded-full bg-brand-500/20 px-3 py-1 text-xs font-bold text-brand-300">
                            {{ $category->tag ?? 'Category' }}
                        </div>
                        <h1 class="pb-display text-3xl lg:text-4xl text-white">{{ $category->name }}</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-300">{{ $category->description }}</p>
                        <p class="mt-4 text-sm text-slate-400">{{ $activeProductCount }} products available</p>
                    </div>
                    @if($category->imageUrl())
                        <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="h-32 w-32 rounded-2xl object-cover shadow-lg" />
                    @endif
                </div>
            </div>
        </div>

        <livewire:product.infinite-catalog :filters="['category' => $category->slug]" />
    </div>
</main>
@endsection
