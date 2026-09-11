@extends('layouts.admin')

@section('title', 'Large Format Rates | Printbuka')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.pricelist.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Pricelist</a>

        <div class="pb-page-header mt-3">
            <div>
                <h1 class="pb-page-title">Large Format Rates</h1>
                <p class="pb-page-subtitle max-w-2xl">
                    Price per square foot for large-format materials (flex banners, SAV stickers, etc). Customer service uses these rates in the
                    <a href="{{ route('admin.large-format.calculator') }}" class="font-black text-pink-700 underline">calculator</a>
                    to quote clients — width × height (converted to sqft) × rate × quantity.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success mb-6">
                {{ session('status') }}
            </div>
        @endif

        <div class="pb-table-wrapper">
            <table class="pb-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Rate (₦ / sqft)</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rates as $rate)
                        <tr>
                            <td class="font-black text-slate-900">{{ $rate->material }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.large-format.update', $rate) }}" class="flex items-center gap-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" step="0.01" min="0" name="rate_per_sqft" value="{{ $rate->rate_per_sqft }}"
                                        class="pb-input w-32">
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                                        <input type="checkbox" name="is_active" value="1" @checked($rate->is_active)>
                                        Active
                                    </label>
                                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-primary">Save</button>
                                </form>
                            </td>
                            <td class="text-sm text-slate-600">{{ $rate->is_active ? 'Yes' : 'No' }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('admin.large-format.destroy', $rate) }}" onsubmit="return confirm('Remove {{ $rate->material }} from large-format rates?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-ghost text-red-500 hover:text-red-600 hover:bg-red-50">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="pb-empty">No large-format materials yet — add one below.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pb-card p-6 mt-8">
            <h2 class="pb-section-title">Add a material</h2>
            <form method="POST" action="{{ route('admin.large-format.store') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                @csrf
                <div class="pb-field sm:col-span-2">
                    <label class="pb-label">Material name</label>
                    <input type="text" name="material" placeholder="e.g. Flex Banner, SAV Sticker" value="{{ old('material') }}"
                        class="pb-input w-full">
                    @error('material')
                        <p class="pb-field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="pb-field">
                    <label class="pb-label">Rate (₦ / sqft)</label>
                    <input type="number" step="0.01" min="0" name="rate_per_sqft" value="{{ old('rate_per_sqft') }}"
                        class="pb-input w-full">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Add material</button>
                </div>
            </form>
        </div>
    </div>
@endsection
