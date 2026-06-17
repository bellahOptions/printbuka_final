@extends('layouts.theme')
@section('title', 'Checkout | Printbuka Shop')
@section('content')
<main class="bg-slate-50 min-h-screen py-12">
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

    <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-400 mb-8">
        <a href="{{ route('shop.cart') }}" class="hover:text-pink-600">Cart</a>
        <span>/</span>
        <span class="text-slate-700">Checkout</span>
    </nav>

    @if(session('error'))
        <div class="alert alert-error mb-6 font-bold">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error mb-6">
            <ul class="list-disc ml-4 text-sm font-bold">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-[1fr_340px] gap-8 items-start">

        {{-- Checkout form --}}
        <form action="{{ route('shop.checkout.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Contact info --}}
            <div class="card bg-white border border-slate-200 shadow-sm">
                <div class="card-body p-6">
                    <h2 class="font-black text-slate-950 text-lg mb-5 flex items-center gap-2">
                        <x-heroicon-o-user class="w-5 h-5 text-pink-600" /> Contact Information
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <label class="form-control">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Full Name *</span>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $user?->name) }}"
                                   class="input input-bordered border-slate-200 @error('customer_name') input-error @enderror"
                                   placeholder="Your full name" required />
                        </label>
                        <label class="form-control">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Email *</span>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}"
                                   class="input input-bordered border-slate-200 @error('customer_email') input-error @enderror"
                                   placeholder="your@email.com" required />
                        </label>
                        <label class="form-control sm:col-span-2">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Phone Number</span>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}"
                                   class="input input-bordered border-slate-200"
                                   placeholder="+234 800 000 0000" />
                        </label>
                    </div>
                </div>
            </div>

            {{-- Shipping info --}}
            <div class="card bg-white border border-slate-200 shadow-sm">
                <div class="card-body p-6">
                    <h2 class="font-black text-slate-950 text-lg mb-5 flex items-center gap-2">
                        <x-heroicon-o-truck class="w-5 h-5 text-pink-600" /> Delivery Information
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <label class="form-control sm:col-span-2">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Recipient Name *</span>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', $user?->name) }}"
                                   class="input input-bordered border-slate-200 @error('shipping_name') input-error @enderror"
                                   placeholder="Name on delivery" required />
                        </label>
                        <label class="form-control sm:col-span-2">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Street Address *</span>
                            <input type="text" name="shipping_address" value="{{ old('shipping_address') }}"
                                   class="input input-bordered border-slate-200 @error('shipping_address') input-error @enderror"
                                   placeholder="House number, street name" required />
                        </label>
                        <label class="form-control">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">City *</span>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}"
                                   class="input input-bordered border-slate-200 @error('shipping_city') input-error @enderror"
                                   placeholder="Lagos" required />
                        </label>
                        <label class="form-control">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">State *</span>
                            <input type="text" name="shipping_state" value="{{ old('shipping_state') }}"
                                   class="input input-bordered border-slate-200 @error('shipping_state') input-error @enderror"
                                   placeholder="Lagos State" required />
                        </label>
                        <label class="form-control sm:col-span-2">
                            <span class="label-text font-bold text-xs uppercase text-slate-500">Delivery Notes</span>
                            <textarea name="shipping_notes" rows="2"
                                      class="textarea textarea-bordered border-slate-200"
                                      placeholder="Landmarks, access instructions, etc.">{{ old('shipping_notes') }}</textarea>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="card bg-white border border-slate-200 shadow-sm">
                <div class="card-body p-6">
                    <h2 class="font-black text-slate-950 text-lg mb-4 flex items-center gap-2">
                        <x-heroicon-o-credit-card class="w-5 h-5 text-pink-600" /> Payment
                    </h2>
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <x-heroicon-o-lock-closed class="w-5 h-5 text-emerald-600 shrink-0" />
                        <div>
                            <p class="font-black text-slate-900 text-sm">Secure payment via Paystack</p>
                            <p class="text-xs text-slate-500 mt-0.5">You will be redirected to Paystack to complete your payment. Cards, bank transfer, and USSD supported.</p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black btn-lg w-full">
                <x-heroicon-o-lock-closed class="w-5 h-5" />
                Pay NGN {{ number_format($subtotal, 0) }} with Paystack
            </button>
        </form>

        {{-- Order summary --}}
        <div class="card bg-white border border-slate-200 shadow-sm sticky top-4">
            <div class="card-body p-6">
                <h2 class="font-black text-slate-950 text-lg mb-5">Order Summary</h2>

                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex gap-3">
                            @if($item['product']->featuredImageUrl())
                                <img src="{{ $item['product']->featuredImageUrl() }}" alt="{{ $item['product']->name }}"
                                     class="w-14 h-14 rounded-xl object-cover border border-slate-100 shrink-0"
                                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                            @else
                                <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                    <x-heroicon-o-shopping-bag class="w-6 h-6 text-slate-300" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-slate-900 leading-snug">{{ $item['product']->name }}</p>
                                @foreach($item['selected_options'] as $opt)
                                    <p class="text-xs text-slate-400 font-bold">{{ $opt->group?->name }}: {{ $opt->name }}</p>
                                @endforeach
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-slate-400">× {{ $item['quantity'] }}</span>
                                    <span class="text-sm font-black text-slate-900">NGN {{ number_format($item['line_total'], 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-200 mt-5 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 font-bold">Subtotal</span>
                        <span class="font-black text-slate-900">NGN {{ number_format($subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 font-bold">Shipping</span>
                        <span class="font-black text-emerald-600">TBD</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-200">
                        <span class="font-black text-slate-900">Total</span>
                        <span class="text-xl font-black text-pink-600">NGN {{ number_format($subtotal, 0) }}</span>
                    </div>
                </div>

                <a href="{{ route('shop.cart') }}" class="btn btn-ghost font-black text-slate-500 w-full mt-3 btn-sm">← Edit Cart</a>
            </div>
        </div>

    </div>
</div>
</main>
@endsection
