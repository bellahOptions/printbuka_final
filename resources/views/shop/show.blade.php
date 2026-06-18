@extends('layouts.theme')
@section('title', $product->name . ' | Printbuka Shop')
@section('meta_description', \Illuminate\Support\Str::limit($product->short_description ?? $product->name, 155))
@section('content')
<main class="bg-base-100 text-base-content min-h-screen">
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs font-bold text-slate-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-pink-600">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-pink-600">Shop</a>
        <span>/</span>
        <span class="text-slate-700">{{ $product->name }}</span>
    </nav>

    @if(session('status'))
        <div class="alert alert-success mb-6 font-bold">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error mb-6 font-bold">
            <ul class="list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-12 items-start">

        {{-- Image gallery --}}
        <div x-data="{ active: '{{ $product->featuredImageUrl() ?? asset('img/product-placeholder.svg') }}' }">
            <div class="rounded-2xl overflow-hidden bg-slate-100 h-[420px]">
                <img :src="active" alt="{{ $product->name }}"
                     class="w-full h-full object-cover"
                     onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
            </div>
            @php $gallery = $product->additionalImageUrls(); @endphp
            @if(count($gallery))
                <div class="flex gap-3 mt-3 overflow-x-auto pb-1">
                    @if($product->featuredImageUrl())
                        <button type="button"
                                @click="active = '{{ $product->featuredImageUrl() }}'"
                                class="w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-pink-400 transition shrink-0">
                            <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                        </button>
                    @endif
                    @foreach($gallery as $img)
                        <button type="button"
                                @click="active = '{{ $img }}'"
                                class="w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-pink-400 transition shrink-0">
                            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product info + add to cart --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                @if($product->isOnSale())
                    <span class="badge bg-pink-600 border-0 text-white font-black">Sale</span>
                @endif
                @if($product->manage_stock)
                    <span class="badge border-0 font-black {{ $product->isInStock() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $product->isInStock() ? 'In stock' : 'Out of stock' }}
                    </span>
                @endif
                @if($product->sku)
                    <span class="text-xs text-slate-400 font-bold">SKU: {{ $product->sku }}</span>
                @endif
            </div>

            <h1 class="text-3xl sm:text-4xl font-black text-slate-950 leading-tight mb-3">{{ $product->name }}</h1>

            <div class="mb-5">
                @if($product->isOnSale())
                    <span class="text-slate-400 line-through text-lg font-bold">NGN {{ number_format((float)$product->price, 0) }}</span>
                    <span class="text-3xl font-black text-pink-600 ml-2">NGN {{ number_format($product->currentPrice(), 0) }}</span>
                @else
                    <span class="text-3xl font-black text-pink-600">NGN {{ number_format($product->currentPrice(), 0) }}</span>
                @endif
            </div>

            @if($product->short_description)
                <p class="text-slate-600 leading-relaxed mb-6">{{ $product->short_description }}</p>
            @endif

            {{-- Add to cart form --}}
            @if($product->isInStock())
                <form action="{{ route('shop.cart.add', $product) }}" method="POST" x-data="productOptions()" class="space-y-5">
                    @csrf

                    {{-- Option groups --}}
                    @foreach($product->optionGroups as $group)
                        <div>
                            <label class="block text-sm font-black text-slate-900 mb-2">
                                {{ $group->name }}
                                @if($group->is_required)<span class="text-pink-600 ml-0.5">*</span>@endif
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($group->options->where('is_available', true) as $option)
                                    <label class="cursor-pointer">
                                        <input type="radio"
                                               name="options[{{ $group->id }}]"
                                               value="{{ $option->id }}"
                                               class="sr-only peer"
                                               @if(!$group->is_required) {{ '' }} @endif
                                               x-on:change="selectOption({{ $group->id }}, {{ $option->id }}, {{ (float)$option->price_modifier }})" />
                                        <span class="inline-flex flex-col items-center border-2 border-slate-200 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 hover:border-slate-300 transition">
                                            {{ $option->name }}
                                            @if((float)$option->price_modifier != 0)
                                                <span class="text-xs font-bold {{ (float)$option->price_modifier > 0 ? 'text-slate-500' : 'text-emerald-600' }}">
                                                    {{ (float)$option->price_modifier > 0 ? '+' : '' }}NGN {{ number_format(abs((float)$option->price_modifier), 0) }}
                                                </span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- Dynamic price --}}
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total price</p>
                        <p class="text-2xl font-black text-pink-600 mt-1" x-text="'NGN ' + total.toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 0})"></p>
                    </div>

                    {{-- Quantity --}}
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-black text-slate-700">Qty</label>
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-10 flex items-center justify-center hover:bg-slate-100 font-black text-slate-700">−</button>
                            <input type="number" name="quantity" x-model="qty" min="1" max="99"
                                   class="w-12 h-10 text-center font-black text-slate-900 border-0 focus:ring-0 bg-transparent" readonly />
                            <button type="button" @click="qty = Math.min(99, qty + 1)" class="w-10 h-10 flex items-center justify-center hover:bg-slate-100 font-black text-slate-700">+</button>
                        </div>
                    </div>

                    <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full btn-lg">
                        <x-heroicon-o-shopping-cart class="w-5 h-5" />
                        Add to Cart
                    </button>

                    <a href="{{ route('shop.cart') }}" class="btn btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700 w-full">View Cart</a>
                </form>

                <script>
                function productOptions() {
                    return {
                        qty: 1,
                        basePrice: {{ $product->currentPrice() }},
                        modifiers: {},
                        get total() {
                            const mod = Object.values(this.modifiers).reduce((s, v) => s + v, 0);
                            return (this.basePrice + mod) * this.qty;
                        },
                        selectOption(groupId, optionId, priceModifier) {
                            this.modifiers[groupId] = priceModifier;
                        }
                    }
                }
                </script>
            @else
                <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-center">
                    <p class="font-black text-red-700">This product is currently out of stock.</p>
                    <p class="text-sm text-red-500 mt-1">Check back soon or <a href="{{ route('quotes.create') }}" class="underline">request a quote</a>.</p>
                </div>
            @endif

            {{-- Description --}}
            @if($product->description)
                <div class="mt-8 prose prose-sm max-w-none text-slate-600">
                    <h3 class="font-black text-slate-950 text-lg mb-3">Product Details</h3>
                    {!! nl2br(e($product->description)) !!}
                </div>
            @endif
        </div>
    </div>

    <livewire:product.suggestions :excludeShopId="$product->id" />

    {{-- Related products --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-16">
            <h2 class="text-2xl font-black text-slate-950 mb-6">You might also like</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('shop.show', $related) }}" class="card bg-white border border-slate-200 hover:-translate-y-1 hover:shadow-lg transition group">
                        <figure class="h-40 overflow-hidden bg-slate-100">
                            @if($related->featuredImageUrl())
                                <img src="{{ $related->featuredImageUrl() }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <x-heroicon-o-shopping-bag class="w-10 h-10 text-slate-200" />
                                </div>
                            @endif
                        </figure>
                        <div class="card-body p-4">
                            <h3 class="font-black text-slate-950 text-sm leading-snug">{{ $related->name }}</h3>
                            <p class="text-base font-black text-pink-600 mt-1">NGN {{ number_format($related->currentPrice(), 0) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
</main>
@endsection
