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
        <h1 class="pb-page-title mt-2">Add custom price list item</h1>

        <form action="{{ route('admin.pricelist.custom.store') }}" method="POST" class="mt-6 space-y-6 pb-card p-6">
            @csrf

            <div class="pb-field">
                <label class="pb-label">Label *</label>
                <input type="text" name="label" value="{{ old('label') }}"
                    class="pb-input" required />
                @error('label') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="pb-field">
                <label class="pb-label">Service</label>
                <select name="service_slug"
                    class="pb-input">
                    <option value="">Not service-specific</option>
                    @foreach ($services as $slug => $service)
                        <option value="{{ $slug }}" @selected(old('service_slug') === $slug)>{{ $service['name'] ?? $slug }}</option>
                    @endforeach
                </select>
                @error('service_slug') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="pb-field">
                <label class="pb-label">Product</label>
                <select name="product_id"
                    class="pb-input">
                    <option value="">Not product-specific</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected((int) old('product_id', $selectedProductId) === $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="max-w-xs pb-field">
                <label class="pb-label">Price (₦) *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">₦</span>
                    <input type="number" min="0" step="0.01" name="price" value="{{ old('price') }}"
                        class="pb-input pl-10" required />
                </div>
                @error('price') <p class="pb-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.pricelist.custom.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Cancel</a>
                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Save item</button>
            </div>
        </form>
    </div>
@endsection
