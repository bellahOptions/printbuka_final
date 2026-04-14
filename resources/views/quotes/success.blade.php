@extends('layouts.theme')

@section('title', 'Quote Request Submitted | Printbuka')

@section('content')
    <main class="bg-slate-50 px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto max-w-4xl rounded-md bg-white p-6 text-center shadow-xl shadow-cyan-950/10 sm:p-10">
            <p class="mx-auto inline-flex rounded-md bg-pink-50 px-4 py-2 text-sm font-black uppercase tracking-wide text-pink-700">Quote Submitted</p>
            <h1 class="mt-5 text-5xl text-slate-950">We have your brief.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600">Your quote request for {{ $order->job_type ?? $order->product?->name ?? 'this job' }} has been saved for review.</p>

            @if (session('status'))
                <p class="mx-auto mt-5 max-w-2xl rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
            @endif

            <div class="mx-auto mt-8 grid max-w-2xl gap-4 text-left sm:grid-cols-2">
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Quote number</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->job_order_number ?? $order->displayNumber() }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Status</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->status }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Quantity</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $order->quantity }}</p>
                </div>
                <div class="rounded-md border border-slate-200 p-5">
                    <p class="text-sm font-bold text-slate-500">Assets</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ count($order->job_image_assets ?? []) }}</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('orders.track.show', $order) }}" class="rounded-md border border-slate-200 px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Track Request</a>
                <a href="{{ route('products.index') }}" class="rounded-md border border-slate-200 px-6 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Browse Products</a>
                <a href="{{ route('home') }}" class="rounded-md bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">Back Home</a>
            </div>
        </section>
    </main>
@endsection
