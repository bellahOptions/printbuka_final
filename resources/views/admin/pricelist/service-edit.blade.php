@extends('layouts.admin')

@section('title', 'Pricing — '.($service['name'] ?? $slug).' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">{{ $service['name'] ?? $slug }}</span>
        </div>
        <h1 class="mt-2 text-3xl font-black text-slate-950">{{ $service['name'] ?? $slug }}</h1>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.services.update', $slug) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Pricing</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-black text-slate-700">Base Price (₦) *</label>
                        <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $service['price'] ?? 0) }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" required />
                        @error('price') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($hasFees)
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Design Fee (₦)</label>
                            <input type="number" min="0" step="0.01" name="design_fee" value="{{ old('design_fee', $designFee) }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Delivery Fee (₦)</label>
                            <input type="number" min="0" step="0.01" name="delivery_fee" value="{{ old('delivery_fee', $deliveryFee) }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" />
                        </div>
                    @endif
                </div>
            </div>

            @if ($hasSizeOptions)
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-black text-slate-950">Film size pricing</h2>
                    <div class="mt-2 rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800">
                        One per line: <strong>Label|Price</strong>. Allowed labels: A2, A3, A4, A5, A6.
                    </div>
                    <div class="mt-4 space-y-1">
                        <textarea name="size_options" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-mono text-sm text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="A2|0&#10;A3|0&#10;A4|0&#10;A5|0&#10;A6|0">{{ old('size_options', $sizeOptionLines) }}</textarea>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save pricing</button>
            </div>
        </form>
    </div>
@endsection
