@extends('layouts.admin')
@section('title', 'Shop Products')
@section('content')
<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title">Shop Products</h1>
            <p class="pb-page-subtitle">Physical products with fixed pricing and customer-selectable options.</p>
        </div>
        <a href="{{ route('admin.shop-products.create') }}" class="pb-btn-primary">
            <x-heroicon-o-plus class="w-4 h-4" /> Add Product
        </a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success mb-6 font-bold">{{ session('status') }}</div>
@endif

<div class="pb-card overflow-hidden">
    @if($products->isEmpty())
        <div class="py-16 text-center">
            <x-heroicon-o-shopping-bag class="w-12 h-12 text-slate-200 mx-auto mb-3" />
            <p class="font-black text-slate-700">No shop products yet.</p>
            <a href="{{ route('admin.shop-products.create') }}" class="pb-btn-primary mt-4 inline-flex">Add First Product</a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="pb-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Options</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($product->featuredImageUrl())
                                        <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                             class="w-10 h-10 rounded-lg object-cover border border-slate-100" />
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <x-heroicon-o-photo class="w-5 h-5 text-slate-300" />
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-black text-slate-900">{{ $product->name }}</p>
                                        @if($product->sku)<p class="text-xs text-slate-400">SKU: {{ $product->sku }}</p>@endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-black text-slate-900">NGN {{ number_format((float)$product->price, 0) }}</p>
                                @if($product->isOnSale())
                                    <p class="text-xs text-pink-600 font-bold">Sale: NGN {{ number_format((float)$product->sale_price, 0) }}</p>
                                @endif
                            </td>
                            <td>
                                @if($product->manage_stock)
                                    <span class="pb-badge-{{ $product->isInStock() ? 'success' : 'danger' }}">
                                        {{ $product->stock_quantity }} in stock
                                    </span>
                                @else
                                    <span class="pb-badge-neutral">Unlimited</span>
                                @endif
                            </td>
                            <td>
                                <span class="pb-badge-neutral">{{ $product->option_groups_count }} group{{ $product->option_groups_count !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="pb-badge-success">Active</span>
                                @else
                                    <span class="pb-badge-warning">Inactive</span>
                                @endif
                                @if($product->is_featured)
                                    <span class="pb-badge-info ml-1">Featured</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('shop.show', $product) }}" target="_blank" class="btn btn-xs btn-ghost text-slate-400 hover:text-slate-700" title="View on site">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.shop-products.edit', $product) }}" class="btn btn-xs btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">Edit</a>
                                    <form action="{{ route('admin.shop-products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-ghost text-red-400 hover:text-red-600 font-black">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
