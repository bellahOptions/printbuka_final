@extends('layouts.admin')
@section('title', 'Shop Products')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">Shop Products</h1>
        </div>
        <a href="{{ route('admin.shop-products.create') }}"
           class="pb-btn pb-btn-md pb-btn-primary self-start">
            <x-heroicon-o-plus class="w-4 h-4" /> Add Product
        </a>
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
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

<livewire:admin.shop-products-table :filters="$filters" />

@endsection
