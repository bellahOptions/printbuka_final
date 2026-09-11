@extends('layouts.admin')

@section('title', 'Custom Price List Items | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.pricelist.index') }}" class="hover:text-pink-600">Pricelist</a>
            <span>/</span>
            <span class="font-semibold text-slate-700">Custom Items</span>
        </div>

        <div class="pb-page-header mt-2">
            <div>
                <h1 class="pb-page-title">Custom price list items</h1>
                <p class="pb-page-subtitle max-w-2xl">Freeform pricing lines not tied to the standard product/service structure — optionally linked to a service and/or a product.</p>
            </div>
            <a href="{{ route('admin.pricelist.custom.create') }}" class="pb-btn pb-btn-md pb-btn-primary">
                + Add custom item
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="pb-table-wrapper">
            <table class="pb-table pb-table--cards">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Service</th>
                        <th>Product</th>
                        <th class="text-right">Price (₦)</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td data-label="Label" class="font-semibold text-slate-800">{{ $item->label }}</td>
                            <td data-label="Service">
                                {{ $item->service_slug ? config("printbuka_services.services.{$item->service_slug}.name", $item->service_slug) : '—' }}
                            </td>
                            <td data-label="Product">
                                @if ($item->product)
                                    <a href="{{ route('admin.pricelist.products.edit', $item->product) }}" class="text-pink-600 hover:underline">{{ $item->product->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td data-label="Price (₦)" class="text-right font-semibold text-slate-800">₦{{ number_format((float) $item->price, 2) }}</td>
                            <td class="text-right">
                                <form action="{{ route('admin.pricelist.custom.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this custom price list item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-destructive">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="pb-empty">
                                    <p class="pb-empty-title">No custom price list items yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
