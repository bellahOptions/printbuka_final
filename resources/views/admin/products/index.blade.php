@extends('layouts.admin')

@section('title', 'Product Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="pb-page-header">
            <div>
                <h1 class="pb-page-title">Products</h1>
                <p class="pb-page-subtitle">Product Management</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="pb-btn pb-btn-md pb-btn-primary self-start">Create Product</a>
        </div>

        @if (request()->user()?->role === 'super_admin')
            <div class="pb-card border-red-200 bg-red-50 p-5 mb-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-black uppercase tracking-wide text-red-700">Process & Technology Manager Cleanup</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-950">Remove seeded product catalog</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">
                            Removes {{ number_format($seededProductCount ?? 0) }} product(s) marked as seeded. For older production data created before seed tracking existed, tick the legacy option to clear all {{ number_format($productCount ?? 0) }} current product(s).
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.products.seeded.destroy') }}" class="w-full max-w-lg space-y-3">
                        @csrf
                        @method('DELETE')
                        <div class="pb-field">
                            <label class="pb-label" for="seeded-products-confirmation">Type DELETE SEEDED PRODUCTS</label>
                            <input
                                id="seeded-products-confirmation"
                                name="confirmation"
                                type="text"
                                autocomplete="off"
                                class="pb-input w-full border-red-200 focus:border-red-500 focus:ring-red-100"
                                placeholder="DELETE SEEDED PRODUCTS"
                            >
                            @error('confirmation')
                                <p class="pb-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="flex items-start gap-3 rounded-md border border-red-200 bg-white/80 p-3 text-sm text-slate-700">
                            <input type="checkbox" name="include_legacy_catalog" value="1" class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">
                            <span>Legacy production cleanup: also remove unmarked products currently in the catalog.</span>
                        </label>
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-destructive">Remove seeded products</button>
                    </form>
                </div>
            </div>
        @endif

        <livewire:admin.products-table />
    </div>
@endsection
