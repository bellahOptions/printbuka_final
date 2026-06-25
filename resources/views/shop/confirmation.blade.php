@extends('layouts.new-app')
@section('title', 'Order Confirmed | Printbuka Shop')
@section('content')
<main class="bg-slate-50 min-h-screen py-16">
<div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

    @if($order->isPaid())
        <div class="text-center mb-10">
            <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
                <x-heroicon-o-check-circle class="w-10 h-10 text-emerald-600" />
            </div>
            <h1 class="text-3xl font-black text-slate-950">Order Confirmed!</h1>
            <p class="text-slate-500 mt-2">Thank you, <strong>{{ $order->customer_name }}</strong>. Your payment was received.</p>
            @if(session('status'))
                <div class="alert alert-success mt-4 font-bold">{{ session('status') }}</div>
            @endif
        </div>
    @else
        <div class="text-center mb-10">
            <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-5">
                <x-heroicon-o-clock class="w-10 h-10 text-amber-600" />
            </div>
            <h1 class="text-3xl font-black text-slate-950">Order Received</h1>
            <p class="text-slate-500 mt-2">Payment pending â€” we will update you at <strong>{{ $order->customer_email }}</strong>.</p>
        </div>
    @endif

    <div class="card bg-white border border-slate-200 shadow-sm mb-6">
        <div class="card-body p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Order Reference</p>
                    <p class="font-black text-xl text-slate-950 font-mono">{{ $order->reference }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="badge font-black {{ $order->isPaid() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} border-0">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                    <span class="badge font-black bg-slate-100 text-slate-700 border-0">
                        {{ ucfirst($order->fulfillment_status) }}
                    </span>
                </div>
            </div>

            {{-- Items --}}
            <h3 class="font-black text-slate-900 mb-4">Items Ordered</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between gap-4 py-3 border-b border-slate-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-black text-slate-950">{{ $item->product_name }}</p>
                            @foreach($item->selectedOptions as $opt)
                                <p class="text-xs text-slate-400 font-bold">{{ $opt->group_name }}: {{ $opt->option_name }}</p>
                            @endforeach
                            <p class="text-xs text-slate-500 mt-0.5">Ã— {{ $item->quantity }} @ NGN {{ number_format((float)$item->unit_price, 0) }}</p>
                        </div>
                        <p class="font-black text-slate-900 shrink-0">NGN {{ number_format((float)$item->line_total, 0) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="border-t border-slate-200 mt-4 pt-4 flex justify-between">
                <span class="font-black text-slate-900">Total Paid</span>
                <span class="text-xl font-black text-pink-600">NGN {{ number_format((float)$order->total, 0) }}</span>
            </div>
        </div>
    </div>

    {{-- Shipping address --}}
    <div class="card bg-white border border-slate-200 shadow-sm mb-8">
        <div class="card-body p-6">
            <h3 class="font-black text-slate-900 mb-3 flex items-center gap-2">
                <x-heroicon-o-truck class="w-4 h-4 text-pink-600" /> Delivery Address
            </h3>
            <p class="text-slate-700 font-bold">{{ $order->shipping_name }}</p>
            <p class="text-slate-600 text-sm mt-1">{{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
            @if($order->shipping_notes)
                <p class="text-slate-400 text-xs mt-1 italic">{{ $order->shipping_notes }}</p>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('shop.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Continue Shopping</a>
        <a href="{{ route('home') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">Back to Home</a>
    </div>

</div>
</main>
@endsection
