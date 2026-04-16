@extends('layouts.theme')

@section('title', 'Service Order Submitted | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto max-w-4xl rounded-md bg-white p-6 text-center shadow-xl shadow-cyan-950/10 sm:p-10">
            <p class="mx-auto inline-flex rounded-md bg-pink-50 px-4 py-2 text-sm font-black uppercase tracking-wide text-pink-700">Service Order Submitted</p>
            <h1 class="mt-5 text-5xl text-slate-950">We have your {{ $service['name'] }} request.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600">Your service order has been saved successfully. Our team will continue processing while your payment status updates.</p>

            @if (session('status') || session('warning'))
                <p class="mx-auto mt-5 max-w-2xl rounded-md border {{ session('status') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }} px-4 py-3 text-sm font-bold">
                    {{ session('status') ?? session('warning') }}
                </p>
            @endif

            <div class="mx-auto mt-8 grid max-w-2xl gap-4 text-left sm:grid-cols-2">
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Order number</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->job_order_number ?? ('#'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT)) }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Invoice</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->invoice?->invoice_number ?? 'Pending' }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Service</p>
                    <p class="mt-1 text-xl font-black text-slate-950">{{ $service['name'] }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Estimated total</p>
                    <p class="mt-1 text-2xl font-black text-pink-700">NGN {{ number_format((float) ($order->invoice?->total_amount ?? $order->total_price), 2) }}</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('services.show', $service['slug']) }}" class="rounded-md border border-slate-200 px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Back to Service</a>
                <a href="{{ route('orders.track.show', $order) }}" class="rounded-md border border-slate-200 px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Track This Order</a>
                <a href="{{ route('home') }}" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Back Home</a>
            </div>
        </section>
    </main>
@endsection
