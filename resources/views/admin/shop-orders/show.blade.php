@extends('layouts.admin')
@section('title', 'Order ' . $order->reference)
@section('content')

<div class="pb-page-header">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.shop-orders.index') }}" class="text-slate-400 hover:text-slate-700 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <h1 class="pb-page-title font-mono">{{ $order->reference }}</h1>
    </div>
    <p class="pb-page-subtitle">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
</div>

@if(session('status'))
    <div class="alert alert-success mb-6 font-bold">{{ session('status') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error mb-6 font-bold">{{ session('error') }}</div>
@endif

<div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

    {{-- Left: items + payment info --}}
    <div class="space-y-6">

        {{-- Order items --}}
        <div class="pb-card p-6">
            <h2 class="font-black text-slate-950 text-base mb-5">Items Ordered</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex gap-4 pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                        @php $shopProd = $item->product; @endphp
                        @if($shopProd?->featuredImageUrl())
                            <img src="{{ $shopProd->featuredImageUrl() }}" alt="{{ $item->product_name }}"
                                 class="w-16 h-16 rounded-xl object-cover border border-slate-100 shrink-0"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        @else
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-shopping-bag class="w-7 h-7 text-slate-300" />
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-slate-950">{{ $item->product_name }}</p>
                            @foreach($item->selectedOptions as $opt)
                                <p class="text-xs text-slate-400 font-bold">{{ $opt->group_name }}: {{ $opt->option_name }}
                                    @if((float)$opt->price_modifier != 0)
                                        <span class="ml-1">{{ (float)$opt->price_modifier > 0 ? '+' : '' }}NGN {{ number_format(abs((float)$opt->price_modifier), 0) }}</span>
                                    @endif
                                </p>
                            @endforeach
                            <p class="text-xs text-slate-500 mt-1">× {{ $item->quantity }} @ NGN {{ number_format((float)$item->unit_price, 0) }}</p>
                        </div>
                        <p class="font-black text-slate-900 shrink-0">NGN {{ number_format((float)$item->line_total, 0) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-slate-200 mt-4 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 font-bold">Subtotal</span>
                    <span class="font-black text-slate-900">NGN {{ number_format((float)$order->subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-black text-slate-900">Total</span>
                    <span class="text-xl font-black text-pink-600">NGN {{ number_format((float)$order->total, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment details --}}
        <div class="pb-card p-6">
            <h2 class="font-black text-slate-950 text-base mb-5">Payment Details</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Payment Status</p>
                    @php
                        $payClass = match($order->payment_status) {
                            'paid'   => 'pb-badge-success',
                            'failed' => 'pb-badge-danger',
                            default  => 'pb-badge-warning',
                        };
                    @endphp
                    <span class="{{ $payClass }} mt-1 inline-block">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if($order->paystack_reference)
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Paystack Reference</p>
                        <p class="font-mono text-sm font-black text-slate-700 mt-1">{{ $order->paystack_reference }}</p>
                    </div>
                @endif
                @if($order->paystack_data && isset($order->paystack_data['paid_at']))
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Paid At</p>
                        <p class="font-bold text-slate-700 text-sm mt-1">{{ \Carbon\Carbon::parse($order->paystack_data['paid_at'])->format('d M Y, H:i') }}</p>
                    </div>
                @endif
                @if($order->paystack_data && isset($order->paystack_data['channel']))
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Channel</p>
                        <p class="font-bold text-slate-700 text-sm mt-1 capitalize">{{ $order->paystack_data['channel'] }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right sidebar --}}
    <div class="space-y-5">

        {{-- Fulfillment status --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4">Fulfillment Status</h2>
            @php
                $fulfillClass = match($order->fulfillment_status) {
                    'delivered'  => 'pb-badge-success',
                    'shipped'    => 'pb-badge-info',
                    'processing' => 'pb-badge-warning',
                    'cancelled'  => 'pb-badge-danger',
                    default      => 'pb-badge-neutral',
                };
            @endphp
            <div class="mb-4">
                <span class="{{ $fulfillClass }}">{{ ucfirst($order->fulfillment_status) }}</span>
            </div>
            <form action="{{ route('admin.shop-orders.update-status', $order) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Update Status</label>
                    <select name="fulfillment_status" class="select select-bordered border-slate-200 select-sm w-full">
                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $st)
                            <option value="{{ $st }}" {{ $order->fulfillment_status === $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black w-full">
                    Update Status
                </button>
            </form>
        </div>

        {{-- Customer info --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4">Customer</h2>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Name</p>
                    <p class="font-black text-slate-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                    <p class="font-bold text-slate-700">{{ $order->customer_email }}</p>
                </div>
                @if($order->customer_phone)
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                        <p class="font-bold text-slate-700">{{ $order->customer_phone }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Shipping info --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4 flex items-center gap-2">
                <x-heroicon-o-truck class="w-4 h-4 text-pink-600" /> Delivery Address
            </h2>
            <div class="text-sm space-y-1">
                <p class="font-black text-slate-900">{{ $order->shipping_name }}</p>
                <p class="text-slate-600">{{ $order->shipping_address }}</p>
                <p class="text-slate-600">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                @if($order->shipping_notes)
                    <p class="text-slate-400 italic mt-2 text-xs">{{ $order->shipping_notes }}</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
