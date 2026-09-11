@extends('layouts.admin')

@section('title', 'Pricing — '.($service['name'] ?? $slug).' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">{{ $service['name'] ?? $slug }}</span>
        </div>
        <h1 class="pb-page-title mt-2">{{ $service['name'] ?? $slug }}</h1>

        @if (session('status'))
            <div class="mt-6 pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.services.update', $slug) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="pb-card p-6">
                <h2 class="pb-section-title">Pricing</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Base Price (₦) *</label>
                        <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $service['price'] ?? 0) }}"
                            class="pb-input" required />
                        @error('price') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>

                    @if ($hasFees)
                        <div class="pb-field">
                            <label class="pb-label">Design Fee (₦)</label>
                            <input type="number" min="0" step="0.01" name="design_fee" value="{{ old('design_fee', $designFee) }}"
                                class="pb-input" />
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Delivery Fee (₦)</label>
                            <input type="number" min="0" step="0.01" name="delivery_fee" value="{{ old('delivery_fee', $deliveryFee) }}"
                                class="pb-input" />
                        </div>
                    @endif
                </div>
            </div>

            @if ($hasSizeOptions)
                <div class="pb-card p-6">
                    <h2 class="pb-section-title">Film size pricing</h2>
                    <div class="mt-2 pb-alert pb-alert-info">
                        One per line: <strong>Label|Price</strong>. Allowed labels: A2, A3, A4, A5, A6.
                    </div>
                    <div class="mt-4 pb-field">
                        <textarea name="size_options" rows="6" class="pb-textarea font-mono" placeholder="A2|0&#10;A3|0&#10;A4|0&#10;A5|0&#10;A6|0">{{ old('size_options', $sizeOptionLines) }}</textarea>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Save pricing</button>
            </div>
        </form>
    </div>
@endsection
