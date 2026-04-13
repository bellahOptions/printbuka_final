@extends('layouts.theme')

@section('title', 'Track Order | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-6xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Track Order</p>
                    <h1 class="mt-4 text-5xl leading-tight">Know where your print request stands.</h1>
                    <p class="mt-5 text-sm leading-7 text-slate-300">Use your order number and email address to view your product, invoice, status and delivery details.</p>
                </div>
                <div class="rounded-md bg-white p-5 text-slate-950">
                    <p class="text-sm font-black text-pink-700">Example</p>
                    <p class="mt-2 text-2xl font-black">#00012</p>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Order Lookup</p>
                <h2 class="mt-2 text-4xl text-slate-950">Enter your order details.</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Your order email keeps the tracking page private.</p>

                <form action="{{ route('orders.track.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="order_number" class="text-sm font-black text-slate-800">Order number</label>
                        <input id="order_number" name="order_number" type="text" value="{{ old('order_number') }}" placeholder="#00012" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                        @error('order_number')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="text-sm font-black text-slate-800">Email address</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                        @error('customer_email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Track Order</button>
                </form>
            </div>
        </section>
    </main>
@endsection
