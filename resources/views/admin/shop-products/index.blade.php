@extends('layouts.admin')
@section('title', 'Shop Products')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">Shop Products</h1>
        </div>
        <a href="{{ route('admin.shop-products.create') }}"
           class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black gap-2 self-start">
            <x-heroicon-o-plus class="w-4 h-4" /> Add Product
        </a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success mb-5 font-bold">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('admin.shop-products.index') }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ !request()->hasAny(['status','stock']) ? 'ring-2 ring-pink-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 group-hover:bg-pink-50 transition">
            <x-heroicon-o-squares-2x2 class="w-5 h-5 text-slate-500 group-hover:text-pink-600 transition" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Total</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.shop-products.index', ['status' => 'active']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-eye class="w-5 h-5 text-emerald-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-emerald-600">Active</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['active']) }}</p>
            
        </div>
    </a>
    <a href="{{ route('admin.shop-products.index', ['status' => 'featured']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'featured' ? 'ring-2 ring-amber-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-star class="w-5 h-5 text-amber-500" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-amber-600">Featured</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['featured']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.shop-products.index', ['stock' => 'out']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('stock') === 'out' ? 'ring-2 ring-red-500' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['out_of_stock'] > 0 ? 'bg-red-50' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-archive-box-x-mark class="w-5 h-5 {{ $stats['out_of_stock'] > 0 ? 'text-red-500' : 'text-slate-400' }}" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase {{ $stats['out_of_stock'] > 0 ? 'text-red-600' : 'text-slate-400' }}">Out of Stock</p>
            <p class="text-2xl font-black {{ $stats['out_of_stock'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ number_format($stats['out_of_stock']) }}</p>
        </div>
    </a>
</div>

{{-- Filters / Search --}}
<div class="pb-card p-4 mb-5">
    <form method="GET" action="{{ route('admin.shop-products.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Search</label>
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name, SKU, description…"
                       class="input input-bordered border-slate-200 input-sm w-full pl-9" />
            </div>
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Status</label>
            <select name="status" class="select select-bordered border-slate-200 select-sm">
                <option value="">All Products</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>Featured</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Stock</label>
            <select name="stock" class="select select-bordered border-slate-200 select-sm">
                <option value="">All Stock</option>
                <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black">
            <x-heroicon-o-funnel class="w-4 h-4" /> Filter
        </button>
        @if(request()->hasAny(['search','status','stock']))
            <a href="{{ route('admin.shop-products.index') }}" class="btn btn-sm btn-ghost font-black text-slate-400">
                <x-heroicon-o-x-mark class="w-4 h-4" /> Clear
            </a>
        @endif
    </form>
</div>

{{-- Product List --}}
@if($products->isEmpty())
    <div class="pb-card py-20 text-center">
        <x-heroicon-o-shopping-bag class="w-14 h-14 text-slate-200 mx-auto mb-4" />
        <p class="font-black text-slate-700 text-lg">No products found</p>
        <p class="text-sm text-slate-400 mt-1 mb-6">
            {{ request()->hasAny(['search','status','stock']) ? 'Try adjusting your filters.' : 'Start by adding your first shop product.' }}
        </p>
        @if(!request()->hasAny(['search','status','stock']))
            <a href="{{ route('admin.shop-products.create') }}"
               class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">
                <x-heroicon-o-plus class="w-4 h-4" /> Add First Product
            </a>
        @endif
    </div>
@else
    <div class="space-y-3">
        @foreach($products as $product)
        <div class="pb-card hover:shadow-md transition-shadow duration-200 overflow-hidden">
            <div class="flex items-start gap-4 p-4">

                {{-- Product Image --}}
                <div class="shrink-0">
                    @if($product->featuredImageUrl())
                        <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                             class="w-20 h-20 rounded-xl object-cover border border-slate-100"
                             onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                    @else
                        <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center border border-slate-100">
                            <x-heroicon-o-photo class="w-7 h-7 text-slate-300" />
                        </div>
                    @endif
                </div>

                {{-- Main content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5">
                        <div class="min-w-0">
                            <h3 class="font-black text-slate-900 text-base leading-tight truncate">{{ $product->name }}</h3>
                            @if($product->sku)
                                <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $product->sku }}</p>
                            @endif
                        </div>
                        {{-- Status badges --}}
                        <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                            @if($product->is_active)
                                <span class="pb-badge-success text-xs">Active</span>
                            @else
                                <span class="pb-badge-neutral text-xs">Inactive</span>
                            @endif
                            @if($product->is_featured)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                                    <x-heroicon-s-star class="w-3 h-3" /> Featured
                                </span>
                            @endif
                            @if($product->isOnSale())
                                <span class="pb-badge-info text-xs">Sale</span>
                            @endif
                        </div>
                    </div>

                    @if($product->short_description)
                        <p class="text-xs text-slate-500 leading-relaxed mb-2 line-clamp-2">{{ $product->short_description }}</p>
                    @endif

                    {{-- Meta row --}}
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 mt-2">
                        {{-- Pricing --}}
                        <div class="flex items-baseline gap-2">
                            @if($product->isOnSale())
                                <span class="text-lg font-black text-pink-600">₦{{ number_format((float)$product->sale_price, 0) }}</span>
                                <span class="text-sm text-slate-400 line-through">₦{{ number_format((float)$product->price, 0) }}</span>
                            @else
                                <span class="text-lg font-black text-slate-900">₦{{ number_format((float)$product->price, 0) }}</span>
                            @endif
                        </div>

                        {{-- Stock --}}
                        <div class="flex items-center gap-1.5 text-xs">
                            @if($product->manage_stock)
                                @if($product->isInStock())
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="font-bold text-emerald-700">{{ $product->stock_quantity }} in stock</span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span class="font-bold text-red-600">Out of stock</span>
                                @endif
                            @else
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                <span class="text-slate-400 font-bold">Unlimited stock</span>
                            @endif
                        </div>

                        {{-- Options --}}
                        @if($product->option_groups_count > 0)
                            <div class="flex items-center gap-1.5 text-xs">
                                <x-heroicon-o-adjustments-horizontal class="w-3.5 h-3.5 text-violet-500" />
                                <span class="text-slate-500 font-bold">
                                    {{ $product->option_groups_count }} option group{{ $product->option_groups_count !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        @endif

                        {{-- Views --}}
                        <div class="flex items-center gap-1.5 text-xs">
                            <x-heroicon-o-eye class="w-3.5 h-3.5 text-slate-400" />
                            <span class="text-slate-400 font-bold">{{ number_format((int)$product->view_count) }} views</span>
                        </div>
                    </div>
                </div>

                {{-- Actions column --}}
                <div class="flex flex-col items-end gap-2 shrink-0 self-center">
                    <a href="{{ route('admin.shop-products.edit', $product) }}"
                       class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black gap-1.5 w-full justify-center">
                        <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit
                    </a>
                    <div class="flex items-center gap-1 w-full">
                        <a href="{{ route('shop.show', $product) }}" target="_blank"
                           class="btn btn-xs btn-ghost text-slate-400 hover:text-slate-700 flex-1 justify-center"
                           title="View in shop">
                            <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                        </a>
                        <form action="{{ route('admin.shop-products.destroy', $product) }}" method="POST"
                              class="flex-1"
                              onsubmit="return confirm('Delete \'{{ addslashes($product->name) }}\'?\nThis cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-xs btn-ghost text-red-400 hover:text-red-600 hover:bg-red-50 font-black w-full">
                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Bottom accent for inactive products --}}
            @if(!$product->is_active)
                <div class="h-0.5 bg-slate-200"></div>
            @elseif($product->is_featured)
                <div class="h-0.5 bg-gradient-to-r from-amber-400 to-orange-300"></div>
            @else
                <div class="h-0.5 bg-gradient-to-r from-pink-500/20 to-transparent"></div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="mt-5 pb-card p-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
@endif

@endsection
