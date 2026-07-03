@extends('layouts.admin')

@section('title', 'Pricing — '.$product->name.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">{{ $product->name }}</span>
        </div>
        <h1 class="mt-2 text-3xl font-black text-slate-950">{{ $product->name }}</h1>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.pricelist.products.update', $product) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Base price</h2>
                <div class="mt-4 max-w-xs space-y-1">
                    <label class="text-sm font-black text-slate-700">Base Price (₦) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500">₦</span>
                        <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                            class="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-10 pr-4 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" required />
                    </div>
                    @error('price') <p class="text-xs text-pink-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @include('admin.pricelist._option-groups')

            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save pricing</button>
            </div>
        </form>
    </div>
@endsection
