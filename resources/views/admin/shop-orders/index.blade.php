@extends('layouts.admin')
@section('title', 'Shop Orders')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title">Shop Orders</h1>
            <p class="pb-page-subtitle">Paystack-paid physical product orders.</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="pb-card p-4 mb-6">
    <form method="GET" action="{{ route('admin.shop-orders.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[220px]">
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Reference, name, email…"
                   class="input input-bordered border-slate-200 input-sm w-full" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Payment</label>
            <select name="payment" class="select select-bordered border-slate-200 select-sm">
                <option value="">All</option>
                <option value="pending" {{ request('payment') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('payment') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Fulfillment</label>
            <select name="status" class="select select-bordered border-slate-200 select-sm">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black">Filter</button>
        @if(request()->hasAny(['search','payment','status']))
            <a href="{{ route('admin.shop-orders.index') }}" class="btn btn-sm btn-ghost font-black text-slate-400">Clear</a>
        @endif
    </form>
</div>

@if(session('status'))
    <div class="alert alert-success mb-6 font-bold">{{ session('status') }}</div>
@endif

<div class="pb-card overflow-hidden">
    @if($orders->isEmpty())
        <div class="py-16 text-center">
            <x-heroicon-o-shopping-bag class="w-12 h-12 text-slate-200 mx-auto mb-3" />
            <p class="font-black text-slate-700">No orders found.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="pb-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Fulfillment</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.shop-orders.show', $order) }}"
                                   class="font-mono font-black text-slate-900 hover:text-pink-600 text-sm">{{ $order->reference }}</a>
                            </td>
                            <td>
                                <p class="font-black text-slate-900 text-sm">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-400">{{ $order->customer_email }}</p>
                            </td>
                            <td>
                                <span class="pb-badge-neutral">{{ $order->items_count }} item{{ $order->items_count !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                <p class="font-black text-slate-900">NGN {{ number_format((float)$order->total, 0) }}</p>
                            </td>
                            <td>
                                @php
                                    $payClass = match($order->payment_status) {
                                        'paid'    => 'pb-badge-success',
                                        'failed'  => 'pb-badge-danger',
                                        default   => 'pb-badge-warning',
                                    };
                                @endphp
                                <span class="{{ $payClass }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td>
                                @php
                                    $fulfillClass = match($order->fulfillment_status) {
                                        'delivered'  => 'pb-badge-success',
                                        'shipped'    => 'pb-badge-info',
                                        'processing' => 'pb-badge-warning',
                                        'cancelled'  => 'pb-badge-danger',
                                        default      => 'pb-badge-neutral',
                                    };
                                @endphp
                                <span class="{{ $fulfillClass }}">{{ ucfirst($order->fulfillment_status) }}</span>
                            </td>
                            <td>
                                <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td>
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.shop-orders.show', $order) }}"
                                       class="btn btn-xs btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                                </div>
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
