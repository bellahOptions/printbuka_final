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

{{-- Filters --}}
<div class="pb-card p-4 mb-6">
    <form method="GET" action="{{ route('admin.shop-orders.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[220px]">
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Reference, name or email…"
                   class="input input-bordered border-slate-200 input-sm w-full" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Payment</label>
            <select name="payment" class="select select-bordered border-slate-200 select-sm">
                <option value="">All Payments</option>
                <option value="pending"  {{ request('payment') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="paid"     {{ request('payment') === 'paid'     ? 'selected' : '' }}>Paid</option>
                <option value="failed"   {{ request('payment') === 'failed'   ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Order Status</label>
            <select name="status" class="select select-bordered border-slate-200 select-sm">
                <option value="">All Statuses</option>
                <option value="order_received" {{ request('status') === 'order_received' ? 'selected' : '' }}>Order Received</option>
                <option value="processing"     {{ request('status') === 'processing'     ? 'selected' : '' }}>Processing</option>
                <option value="dispatched"     {{ request('status') === 'dispatched'     ? 'selected' : '' }}>Dispatched</option>
                <option value="delivered"      {{ request('status') === 'delivered'      ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black">
            <x-heroicon-o-funnel class="w-4 h-4" /> Filter
        </button>
        @if(request()->hasAny(['search','payment','status']))
            <a href="{{ route('admin.shop-orders.index') }}" class="btn btn-sm btn-ghost font-black text-slate-400">
                <x-heroicon-o-x-mark class="w-4 h-4" /> Clear
            </a>
        @endif
    </form>
</div>

@if(session('status'))
    <div class="alert alert-success mb-5 font-bold">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

<div class="pb-card overflow-hidden">
    @if($orders->isEmpty())
        <div class="py-16 text-center">
            <x-heroicon-o-shopping-bag class="w-12 h-12 text-slate-200 mx-auto mb-3" />
            <p class="font-black text-slate-700">No orders found.</p>
            <p class="text-sm text-slate-400 mt-1">Try adjusting your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="pb-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th class="hidden sm:table-cell">Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th class="hidden md:table-cell">Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    @php
                        $fulfillConfig = match($order->fulfillment_status) {
                            'delivered'      => ['class' => 'pb-badge-success',  'label' => 'Delivered',      'dot' => 'bg-emerald-500'],
                            'dispatched'     => ['class' => 'pb-badge-info',     'label' => 'Dispatched',     'dot' => 'bg-violet-500'],
                            'processing'     => ['class' => 'pb-badge-warning',  'label' => 'Processing',     'dot' => 'bg-amber-500'],
                            'order_received' => ['class' => 'pb-badge-neutral',  'label' => 'Order Received', 'dot' => 'bg-sky-400'],
                            default          => ['class' => 'pb-badge-neutral',  'label' => ucfirst($order->fulfillment_status), 'dot' => 'bg-slate-400'],
                        };
                        $payConfig = match($order->payment_status) {
                            'paid'   => ['class' => 'pb-badge-success', 'label' => 'Paid'],
                            'failed' => ['class' => 'pb-badge-danger',  'label' => 'Failed'],
                            default  => ['class' => 'pb-badge-warning', 'label' => 'Pending'],
                        };
                    @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td>
                                <a href="{{ route('admin.shop-orders.show', $order) }}"
                                   class="font-mono font-black text-slate-900 hover:text-pink-600 text-sm transition-colors">{{ $order->reference }}</a>
                            </td>
                            <td>
                                <p class="font-black text-slate-900 text-sm leading-tight">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $order->customer_email }}</p>
                            </td>
                            <td class="hidden sm:table-cell">
                                <span class="pb-badge-neutral text-xs">{{ $order->items_count }} item{{ $order->items_count !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                <p class="font-black text-slate-900 text-sm">₦{{ number_format((float)$order->total, 0) }}</p>
                            </td>
                            <td>
                                <span class="{{ $payConfig['class'] }} text-xs">{{ $payConfig['label'] }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $fulfillConfig['dot'] }}"></span>
                                    <span class="{{ $fulfillConfig['class'] }} text-xs">{{ $fulfillConfig['label'] }}</span>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.shop-orders.show', $order) }}"
                                   class="btn btn-xs btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">
                                    View →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
