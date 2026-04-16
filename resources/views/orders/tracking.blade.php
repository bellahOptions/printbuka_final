@extends('layouts.theme')

@section('title', 'Order '.($order->job_order_number ?? $order->displayNumber()).' | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Order Tracking</p>
                <h1 class="mt-2 text-5xl">{{ $order->job_order_number ?? $order->displayNumber() }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">{{ $order->product?->name ?? 'Custom order' }} for {{ $order->customer_name }}</p>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Status</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ ucfirst($order->status) }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Invoice</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->service_type === 'quote' ? 'Quote review' : ($order->invoice?->invoice_number ?? 'Pending') }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">{{ $order->service_type === 'quote' ? 'Pricing' : 'Total' }}</p>
                    <p class="mt-1 text-2xl font-black text-pink-700">{{ $order->service_type === 'quote' ? 'Pending' : 'NGN '.number_format($order->invoice?->total_amount ?? $order->total_price, 2) }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold text-slate-500">Ordered</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->created_at->format('M d') }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Order Details</p>
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="font-bold text-slate-500">Product</span>
                            <span class="font-black text-slate-950">{{ $order->product?->name ?? 'Custom order' }}</span>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="font-bold text-slate-500">Quantity</span>
                            <span class="font-black text-slate-950">{{ $order->quantity }}</span>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="font-bold text-slate-500">Service type</span>
                            <span class="font-black text-slate-950">{{ ucfirst($order->service_type) }}</span>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="font-bold text-slate-500">{{ $order->service_type === 'quote' ? 'Request status' : 'Invoice status' }}</span>
                            <span class="font-black text-slate-950">{{ $order->service_type === 'quote' ? $order->status : ucfirst($order->invoice?->status ?? 'pending') }}</span>
                        </div>
                    </div>
                </section>

                <aside class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Delivery</p>
                    <p class="mt-4 text-sm leading-6 text-slate-600">{{ $order->delivery_address ?: 'Delivery address not provided yet.' }}</p>
                    <p class="mt-2 text-sm font-black text-slate-950">{{ $order->delivery_city ?: 'City pending' }}</p>
                    @if ($order->artwork_notes)
                        <p class="mt-6 text-sm font-black text-slate-950">Artwork notes</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $order->artwork_notes }}</p>
                    @endif
                </aside>
            </div>
        </section>
    </main>
@endsection
