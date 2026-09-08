@extends('layouts.admin')
@section('title', 'Shop Orders')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title">Shop Orders</h1>
            <p class="pb-page-subtitle">Manage and track all Paystack-paid product orders.</p>
        </div>
        <a href="{{ route('admin.shop-products.index') }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 self-start">
            <x-heroicon-o-squares-2x2 class="w-4 h-4" /> Manage Products
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <a href="{{ route('admin.shop-orders.index') }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ !request()->hasAny(['status','payment']) ? 'ring-2 ring-pink-500' : '' }}">
        <p class="text-xs font-bold uppercase text-slate-400 tracking-wide">All Orders</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
    </a>
    <a href="{{ route('admin.shop-orders.index', ['status' => 'order_received']) }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ request('status') === 'order_received' ? 'ring-2 ring-sky-500' : '' }}">
        <p class="text-xs font-bold uppercase text-sky-600 tracking-wide">Received</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['order_received']) }}</p>
    </a>
    <a href="{{ route('admin.shop-orders.index', ['status' => 'processing']) }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ request('status') === 'processing' ? 'ring-2 ring-amber-500' : '' }}">
        <p class="text-xs font-bold uppercase text-amber-600 tracking-wide">Processing</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['processing']) }}</p>
    </a>
    <a href="{{ route('admin.shop-orders.index', ['status' => 'dispatched']) }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ request('status') === 'dispatched' ? 'ring-2 ring-violet-500' : '' }}">
        <p class="text-xs font-bold uppercase text-violet-600 tracking-wide">Dispatched</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['dispatched']) }}</p>
    </a>
    <a href="{{ route('admin.shop-orders.index', ['status' => 'delivered']) }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ request('status') === 'delivered' ? 'ring-2 ring-emerald-500' : '' }}">
        <p class="text-xs font-bold uppercase text-emerald-600 tracking-wide">Delivered</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['delivered']) }}</p>
    </a>
    <a href="{{ route('admin.shop-orders.index', ['payment' => 'paid']) }}"
       class="pb-card p-4 flex flex-col gap-1 hover:border-slate-300 hover:shadow transition {{ request('payment') === 'paid' ? 'ring-2 ring-green-500' : '' }}">
        <p class="text-xs font-bold uppercase text-green-600 tracking-wide">Paid</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['paid']) }}</p>
    </a>
</div>

@if(session('status'))
    <div class="alert alert-success mb-5 font-bold">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

<livewire:admin.shop-orders-table :filters="$filters" />
@endsection
