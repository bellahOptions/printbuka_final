@extends('layouts.admin')

@section('title', 'Large Format Rates | Printbuka')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.pricelist.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Pricelist</a>

        <div class="mt-3">
            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Pricelist</p>
            <h1 class="mt-2 text-4xl text-slate-950">Large format rates.</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">
                Price per square foot for large-format materials (flex banners, SAV stickers, etc). Customer service uses these rates in the
                <a href="{{ route('admin.large-format.calculator') }}" class="font-black text-pink-700 underline">calculator</a>
                to quote clients — width × height (converted to sqft) × rate × quantity.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-8 rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3.5 text-left">Material</th>
                        <th class="px-5 py-3.5 text-left">Rate (₦ / sqft)</th>
                        <th class="px-5 py-3.5 text-left">Active</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rates as $rate)
                        <tr>
                            <td class="px-5 py-4 font-black text-slate-900">{{ $rate->material }}</td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.large-format.update', $rate) }}" class="flex items-center gap-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" step="0.01" min="0" name="rate_per_sqft" value="{{ $rate->rate_per_sqft }}"
                                        class="w-32 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                                        <input type="checkbox" name="is_active" value="1" @checked($rate->is_active)>
                                        Active
                                    </label>
                                    <button type="submit" class="rounded-md bg-pink-600 px-4 py-2 text-xs font-black text-white transition hover:bg-pink-700">Save</button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $rate->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.large-format.destroy', $rate) }}" onsubmit="return confirm('Remove {{ $rate->material }} from large-format rates?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-black text-slate-400 hover:text-pink-600">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No large-format materials yet — add one below.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Add a material</h2>
            <form method="POST" action="{{ route('admin.large-format.store') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                @csrf
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-black text-slate-700">Material name</label>
                    <input type="text" name="material" placeholder="e.g. Flex Banner, SAV Sticker" value="{{ old('material') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    @error('material')
                        <p class="text-xs font-bold text-pink-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-black text-slate-700">Rate (₦ / sqft)</label>
                    <input type="number" step="0.01" min="0" name="rate_per_sqft" value="{{ old('rate_per_sqft') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Add material</button>
                </div>
            </form>
        </div>
    </div>
@endsection
