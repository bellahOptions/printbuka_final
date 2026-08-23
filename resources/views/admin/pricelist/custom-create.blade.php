@extends('layouts.admin')

@section('title', 'Add Custom Price List Item | Printbuka')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <a href="{{ route('admin.pricelist.custom.index') }}" class="hover:text-pink-600">Custom Items</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">New</span>
        </div>
        <h1 class="mt-2 text-3xl font-black text-slate-950">Add custom price list item</h1>

        <form action="{{ route('admin.pricelist.custom.store') }}" method="POST" class="mt-6 space-y-6 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            @csrf

            <div class="space-y-1">
                <label class="text-sm font-black text-slate-700">Label *</label>
                <input type="text" name="label" value="{{ old('label') }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" required />
                @error('label') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-black text-slate-700">Service</label>
                <select name="service_slug"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    <option value="">Not service-specific</option>
                    @foreach ($services as $slug => $service)
                        <option value="{{ $slug }}" @selected(old('service_slug') === $slug)>{{ $service['name'] ?? $slug }}</option>
                    @endforeach
                </select>
                @error('service_slug') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-black text-slate-700">Product</label>
                <select name="product_id"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    <option value="">Not product-specific</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected((int) old('product_id', $selectedProductId) === $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            <div class="max-w-xs space-y-1">
                <label class="text-sm font-black text-slate-700">Price (₦) *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">₦</span>
                    <input type="number" min="0" step="0.01" name="price" value="{{ old('price') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-10 pr-4 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" required />
                </div>
                @error('price') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.pricelist.custom.index') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">Cancel</a>
                <button type="submit" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save item</button>
            </div>
        </form>
    </div>
@endsection
