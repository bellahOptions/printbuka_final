@extends('layouts.theme')

@section('title', 'Order '.$product->name.' | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.75fr_1.25fr] lg:px-8">
            <aside class="h-fit rounded-md bg-slate-950 p-6 text-white lg:sticky lg:top-28">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">{{ $serviceType === 'gift' ? 'Gift Order' : 'Print Order' }}</p>
                <h1 class="mt-3 text-4xl leading-tight">{{ $product->name }}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-300">{{ $product->short_description }}</p>

                <div class="mt-6 space-y-3 rounded-md bg-white p-5 text-slate-950">
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">MOQ</span>
                        <span class="font-black">{{ $product->moq }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">Unit price</span>
                        <span class="font-black">NGN {{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">Finishing</span>
                        <span class="font-black">{{ $product->finishing }}</span>
                    </div>
                </div>
            </aside>

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Order Details</p>
                <h2 class="mt-2 text-4xl text-slate-950">Tell us what to prepare.</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">We will review your request, confirm artwork and delivery details, then guide you through payment and production.</p>

                <form action="{{ route('orders.store', $product) }}" method="POST" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label for="quantity" class="text-sm font-black text-slate-800">Quantity</label>
                        <input
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="{{ $product->moq }}"
                            value="{{ old('quantity', $product->moq) }}"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        <p class="mt-2 text-xs font-bold text-slate-500">Minimum order quantity is {{ $product->moq }}.</p>
                        @error('quantity')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="customer_name" class="text-sm font-black text-slate-800">Full name</label>
                            <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name', auth()->user()->name ?? '') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                            @error('customer_name')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="customer_phone" class="text-sm font-black text-slate-800">Phone number</label>
                            <input id="customer_phone" name="customer_phone" type="text" value="{{ old('customer_phone') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                            @error('customer_phone')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="customer_email" class="text-sm font-black text-slate-800">Email address</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required />
                        @error('customer_email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="delivery_city" class="text-sm font-black text-slate-800">Delivery city</label>
                            <input id="delivery_city" name="delivery_city" type="text" value="{{ old('delivery_city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            @error('delivery_city')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="delivery_address" class="text-sm font-black text-slate-800">Delivery address</label>
                            <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            @error('delivery_address')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="artwork_notes" class="text-sm font-black text-slate-800">{{ $serviceType === 'gift' ? 'Branding and gift notes' : 'Artwork and print notes' }}</label>
                        <textarea id="artwork_notes" name="artwork_notes" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Tell us about logo placement, colours, artwork files, delivery deadline or anything the production team should know.">{{ old('artwork_notes') }}</textarea>
                        @error('artwork_notes')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Submit Order Request</button>
                </form>
            </section>
        </section>
    </main>
@endsection
