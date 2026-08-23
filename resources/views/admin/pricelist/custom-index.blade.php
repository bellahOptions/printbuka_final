@extends('layouts.admin')

@section('title', 'Custom Price List Items | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">Custom Items</span>
        </div>

        <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-950">Custom price list items</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Freeform pricing lines not tied to the standard product/service structure — optionally linked to a service and/or a product.</p>
            </div>
            <a href="{{ route('admin.pricelist.custom.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                + Add custom item
            </a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm">
            <table class="pb-table w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left">Label</th>
                        <th class="px-4 py-3 text-left">Service</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-right">Price (₦)</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->label }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->service_slug ? config("printbuka_services.services.{$item->service_slug}.name", $item->service_slug) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if ($item->product)
                                    <a href="{{ route('admin.pricelist.products.edit', $item->product) }}" class="text-pink-600 hover:underline">{{ $item->product->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">₦{{ number_format((float) $item->price, 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('admin.pricelist.custom.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this custom price list item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-bold text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No custom price list items yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
