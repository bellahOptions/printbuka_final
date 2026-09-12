@extends('layouts.new-app')

@section('title', 'Track Order | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-6xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="pb-eyebrow text-cyan-300">Track Order</p>
                    <h1 class="pb-display mt-4 text-5xl leading-tight text-white">Know where your print request stands.</h1>
                    <p class="mt-5 text-sm leading-7 text-slate-300">Use your order number and email address to view your product, invoice, status and delivery details.</p>
                </div>
                <div class="rounded-md bg-white p-5 text-slate-950">
                    <p class="text-sm font-black text-brand-700">Example</p>
                    <p class="mt-2 text-2xl font-black">#00012</p>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                <p class="pb-eyebrow">Order Lookup</p>
                <h2 class="pb-display mt-2 text-4xl">Enter your order details.</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Your order email keeps the tracking page private.</p>

                <form action="{{ route('orders.track.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="order_number" class="text-sm font-black text-slate-800">Order number</label>
                        <input id="order_number" name="order_number" type="text" value="{{ old('order_number') }}" placeholder="#00012" class="pb-input mt-2 h-12" required />
                        @error('order_number')
                            <p class="mt-2 text-sm font-semibold text-brand-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="text-sm font-black text-slate-800">Email address</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}" class="pb-input mt-2 h-12" required />
                        @error('customer_email')
                            <p class="mt-2 text-sm font-semibold text-brand-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="pb-cta-primary w-full h-12 text-base">Track Order</button>
                </form>
            </div>
        </section>
    </main>
@endsection
