@extends('layouts.admin')

@section('title', 'Pricing — '.$product->name.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">{{ $product->name }}</span>
        </div>
        <h1 class="pb-page-title mt-2">{{ $product->name }}</h1>

        @if (session('status'))
            <div class="mt-6 pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.products.update', $product) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="pb-card p-6">
                <h2 class="pb-section-title">Base price</h2>
                <div class="mt-4 max-w-xs pb-field">
                    <label class="pb-label">Base Price (₦) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">₦</span>
                        <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                            class="pb-input pl-10" required />
                    </div>
                    @error('price') <p class="pb-field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            @include('admin.pricelist._option-groups')

            <div class="flex justify-end">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Save pricing</button>
            </div>
        </form>

        <div class="mt-8 pb-card p-6">
            <div class="flex items-center justify-between">
                <h2 class="pb-section-title">Custom price list items</h2>
                <a href="{{ route('admin.pricelist.custom.create', ['product_id' => $product->id]) }}" class="text-sm font-semibold text-pink-600 hover:text-pink-800">
                    + Add custom item
                </a>
            </div>
            <p class="pb-section-subtitle">Freeform pricing lines linked to this product, outside the standard option pricing above.</p>

            <div class="mt-4 divide-y divide-slate-100">
                @forelse ($product->priceListItems as $item)
                    <div class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $item->label }}</p>
                            @if ($item->service_slug)
                                <p class="text-xs text-slate-500">{{ config("printbuka_services.services.{$item->service_slug}.name", $item->service_slug) }}</p>
                            @endif
                        </div>
                        <p class="font-semibold text-slate-800">₦{{ number_format((float) $item->price, 2) }}</p>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">No custom items linked to this product yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
