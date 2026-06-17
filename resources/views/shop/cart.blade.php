@extends('layouts.theme')
@section('title', 'Your Cart | Printbuka Shop')
@section('content')
<main class="bg-slate-50 min-h-screen py-12">
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

    <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-pink-600">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-pink-600">Shop</a>
        <span>/</span>
        <span class="text-slate-700">Cart</span>
    </nav>

    @foreach(['status','warning','error'] as $key)
        @if(session($key))
            <div class="alert {{ $key === 'status' ? 'alert-success' : ($key === 'warning' ? 'alert-warning' : 'alert-error') }} mb-6 font-bold">
                {{ session($key) }}
            </div>
        @endif
    @endforeach

    <h1 class="text-3xl font-black text-slate-950 mb-8">Your Cart</h1>

    @if(empty($cartItems))
        <div class="card bg-white border border-slate-200 p-16 text-center">
            <x-heroicon-o-shopping-cart class="w-16 h-16 text-slate-200 mx-auto mb-4" />
            <p class="text-xl font-black text-slate-700">Your cart is empty.</p>
            <p class="text-slate-400 mt-1">Add some products to get started.</p>
            <a href="{{ route('shop.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black mt-6">Back to Shop</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

            {{-- Cart items --}}
            <div class="space-y-4">
                <form action="{{ route('shop.cart.update') }}" method="POST" id="cart-form">
                    @csrf
                    @foreach($cartItems as $item)
                        <div class="card bg-white border border-slate-200 shadow-sm">
                            <div class="card-body p-5">
                                <div class="flex gap-4">
                                    {{-- Product image --}}
                                    <a href="{{ route('shop.show', $item['product']) }}" class="shrink-0">
                                        @if($item['product']->featuredImageUrl())
                                            <img src="{{ $item['product']->featuredImageUrl() }}" alt="{{ $item['product']->name }}"
                                                 class="w-20 h-20 rounded-xl object-cover border border-slate-100"
                                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                                        @else
                                            <div class="w-20 h-20 rounded-xl bg-slate-100 flex items-center justify-center">
                                                <x-heroicon-o-shopping-bag class="w-8 h-8 text-slate-300" />
                                            </div>
                                        @endif
                                    </a>

                                    {{-- Details --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-black text-slate-950 text-base leading-snug">
                                            <a href="{{ route('shop.show', $item['product']) }}" class="hover:text-pink-600">{{ $item['product']->name }}</a>
                                        </h3>

                                        @if($item['selected_options']->isNotEmpty())
                                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                                @foreach($item['selected_options'] as $opt)
                                                    <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">
                                                        {{ $opt->group?->name }}: {{ $opt->name }}
                                                        @if((float)$opt->price_modifier != 0)
                                                            <span class="ml-1 {{ (float)$opt->price_modifier > 0 ? '' : 'text-emerald-600' }}">
                                                                {{ (float)$opt->price_modifier > 0 ? '+' : '' }}NGN {{ number_format(abs((float)$opt->price_modifier), 0) }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between mt-3 gap-3">
                                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                                                <span class="px-3 py-1 text-sm font-bold text-slate-500">Qty</span>
                                                <input type="number" name="quantities[{{ $item['index'] }}]"
                                                       value="{{ $item['quantity'] }}" min="1" max="99"
                                                       class="w-14 h-9 text-center font-black text-slate-900 border-0 focus:ring-0 bg-transparent text-sm" />
                                            </div>

                                            <div class="text-right">
                                                <p class="text-xs text-slate-400 font-bold">NGN {{ number_format($item['unit_price'], 0) }} × {{ $item['quantity'] }}</p>
                                                <p class="text-lg font-black text-pink-600">NGN {{ number_format($item['line_total'], 0) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Remove --}}
                                    <div class="shrink-0">
                                        <form action="{{ route('shop.cart.remove', $item['index']) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-ghost text-slate-400 hover:text-red-500">
                                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-outline font-black border-slate-300 hover:border-slate-500">Update Cart</button>
                        <form action="{{ route('shop.cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-ghost text-slate-400 hover:text-red-500 font-black">Clear Cart</button>
                        </form>
                    </div>
                </form>
            </div>

            {{-- Order summary --}}
            <div class="card bg-white border border-slate-200 shadow-sm sticky top-4">
                <div class="card-body p-6">
                    <h2 class="font-black text-slate-950 text-lg mb-5">Order Summary</h2>

                    <div class="space-y-3 mb-5">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 font-bold">{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                                <span class="font-black text-slate-900">NGN {{ number_format($item['line_total'], 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex justify-between">
                            <span class="font-black text-slate-900">Total</span>
                            <span class="text-xl font-black text-pink-600">NGN {{ number_format($total, 0) }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Shipping calculated at checkout</p>
                    </div>

                    <a href="{{ route('shop.checkout') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full mt-5 btn-lg">
                        Proceed to Checkout
                        <x-heroicon-o-arrow-right class="w-5 h-5" />
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn btn-ghost font-black text-slate-500 w-full mt-2">Continue Shopping</a>
                </div>
            </div>

        </div>
    @endif
</div>
</main>
@endsection
