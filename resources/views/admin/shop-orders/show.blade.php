@extends('layouts.admin')
@section('title', 'Order ' . $order->reference)
@section('content')

@php
    $fulfillSteps = [
        ['key' => 'order_received', 'label' => 'Order Received',  'icon' => 'shopping-bag',   'color' => 'sky'],
        ['key' => 'processing',     'label' => 'Processing',       'icon' => 'cog-6-tooth',    'color' => 'amber'],
        ['key' => 'dispatched',     'label' => 'Dispatched',       'icon' => 'truck',          'color' => 'violet'],
        ['key' => 'delivered',      'label' => 'Delivered',        'icon' => 'check-badge',    'color' => 'emerald'],
    ];
    $stepKeys = array_column($fulfillSteps, 'key');
    $currentStepIndex = array_search($order->fulfillment_status, $stepKeys, true);
    if ($currentStepIndex === false) $currentStepIndex = -1;
@endphp

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.shop-orders.index') }}" class="text-slate-400 hover:text-slate-700 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="pb-page-title font-mono text-lg sm:text-2xl">{{ $order->reference }}</h1>
                <p class="pb-page-subtitle">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @php
            $payConfig = match($order->payment_status) {
                'paid'   => ['class' => 'pb-badge-success', 'label' => 'Paid'],
                'failed' => ['class' => 'pb-badge-danger',  'label' => 'Payment Failed'],
                default  => ['class' => 'pb-badge-warning', 'label' => 'Payment Pending'],
            };
        @endphp
        <span class="{{ $payConfig['class'] }} text-sm font-black">{{ $payConfig['label'] }}</span>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success mb-5 font-bold">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error mb-5 font-bold">{{ session('error') }}</div>
@endif

{{-- Order Status Timeline --}}
<div class="pb-card p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-black text-slate-950 text-base">Order Progress</h2>
        <span class="text-xs text-slate-400">Customer is notified at each stage via email</span>
    </div>
    <div class="relative">
        {{-- Connector line --}}
        <div class="absolute top-5 left-[calc(12.5%)] right-[calc(12.5%)] h-0.5 bg-slate-200 z-0"></div>
        @php
            $progressWidth = match($currentStepIndex) {
                0 => '0%', 1 => '33.3%', 2 => '66.6%', 3 => '100%', default => '0%'
            };
        @endphp
        <div class="absolute top-5 left-[calc(12.5%)] h-0.5 bg-gradient-to-r from-sky-400 to-emerald-500 z-0 transition-all duration-500"
             style="width: calc({{ $progressWidth }} * 0.75)"></div>

        <div class="relative z-10 grid grid-cols-4 gap-2">
            @foreach($fulfillSteps as $i => $step)
            @php
                $isDone = $i < $currentStepIndex;
                $isCurrent = $i === $currentStepIndex;
                $isPending = $i > $currentStepIndex;
                $colorMap = [
                    'sky'     => ['ring' => 'ring-sky-500',     'bg' => 'bg-sky-500',     'text' => 'text-sky-700',     'light' => 'bg-sky-50'],
                    'amber'   => ['ring' => 'ring-amber-500',   'bg' => 'bg-amber-500',   'text' => 'text-amber-700',   'light' => 'bg-amber-50'],
                    'violet'  => ['ring' => 'ring-violet-500',  'bg' => 'bg-violet-500',  'text' => 'text-violet-700',  'light' => 'bg-violet-50'],
                    'emerald' => ['ring' => 'ring-emerald-500', 'bg' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'light' => 'bg-emerald-50'],
                ];
                $c = $colorMap[$step['color']];
            @endphp
            <div class="flex flex-col items-center text-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all duration-300
                    {{ $isDone    ? $c['bg'] . ' text-white shadow-sm' : '' }}
                    {{ $isCurrent ? $c['bg'] . ' text-white ring-4 ' . $c['ring'] . ' ring-offset-2 shadow-md' : '' }}
                    {{ $isPending ? 'bg-slate-100 text-slate-300' : '' }}">
                    @if($isDone)
                        <x-heroicon-s-check class="w-5 h-5" />
                    @elseif($isCurrent)
                        @if($step['icon'] === 'shopping-bag') <x-heroicon-s-shopping-bag class="w-5 h-5" />
                        @elseif($step['icon'] === 'cog-6-tooth') <x-heroicon-s-cog-6-tooth class="w-5 h-5" />
                        @elseif($step['icon'] === 'truck') <x-heroicon-s-truck class="w-5 h-5" />
                        @elseif($step['icon'] === 'check-badge') <x-heroicon-s-check-badge class="w-5 h-5" />
                        @endif
                    @else
                        @if($step['icon'] === 'shopping-bag') <x-heroicon-o-shopping-bag class="w-5 h-5" />
                        @elseif($step['icon'] === 'cog-6-tooth') <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        @elseif($step['icon'] === 'truck') <x-heroicon-o-truck class="w-5 h-5" />
                        @elseif($step['icon'] === 'check-badge') <x-heroicon-o-check-badge class="w-5 h-5" />
                        @endif
                    @endif
                </div>
                <p class="text-xs font-black leading-tight {{ $isCurrent ? $c['text'] : ($isDone ? 'text-slate-700' : 'text-slate-400') }}">
                    {{ $step['label'] }}
                </p>
                @if($isCurrent)
                    <span class="text-[10px] font-bold {{ $c['text'] }} {{ $c['light'] }} px-2 py-0.5 rounded-full">Current</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Update Status Form --}}
    <div class="mt-6 border-t border-slate-100 pt-5">
        <form action="{{ route('admin.shop-orders.update-status', $order) }}" method="POST"
              class="flex flex-wrap items-end gap-3">
            @csrf
            @method('PATCH')
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Advance to Status</label>
                <select name="fulfillment_status" class="select select-bordered border-slate-200 select-sm w-full">
                    @foreach($fulfillSteps as $step)
                        <option value="{{ $step['key'] }}" {{ $order->fulfillment_status === $step['key'] ? 'selected' : '' }}>
                            {{ $step['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm bg-slate-900 border-0 text-white hover:bg-slate-700 font-black">
                <x-heroicon-o-arrow-path class="w-4 h-4" /> Update & Notify Customer
            </button>
        </form>
    </div>
</div>

<div class="grid lg:grid-cols-[1fr_300px] gap-6 items-start">

    {{-- Left: items + payment info --}}
    <div class="space-y-5">

        {{-- Order items --}}
        <div class="pb-card">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-black text-slate-950 text-base">Items Ordered</h2>
                <span class="pb-badge-neutral text-xs">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <div class="p-5 flex gap-4">
                        @php $shopProd = $item->product; @endphp
                        @if($shopProd?->featuredImageUrl())
                            <img src="{{ $shopProd->featuredImageUrl() }}" alt="{{ $item->product_name }}"
                                 class="w-16 h-16 rounded-xl object-cover border border-slate-100 shrink-0"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        @else
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                <x-heroicon-o-shopping-bag class="w-7 h-7 text-slate-300" />
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-slate-950 text-sm">{{ $item->product_name }}</p>
                            <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                @foreach($item->selectedOptions as $opt)
                                    <span class="text-xs text-slate-500">
                                        <span class="font-bold text-slate-600">{{ $opt->group_name }}:</span>
                                        {{ $opt->option_name }}
                                        @if((float)$opt->price_modifier != 0)
                                            <span class="text-pink-600 font-bold">
                                                {{ (float)$opt->price_modifier > 0 ? '+' : '' }}₦{{ number_format(abs((float)$opt->price_modifier), 0) }}
                                            </span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-xs text-slate-400 mt-1.5">
                                × {{ $item->quantity }} @ ₦{{ number_format((float)$item->unit_price, 0) }} each
                            </p>
                        </div>
                        <p class="font-black text-slate-900 text-sm shrink-0">₦{{ number_format((float)$item->line_total, 0) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-slate-100 bg-slate-50/70 p-5 space-y-2 rounded-b-xl">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 font-bold">Subtotal</span>
                    <span class="font-black text-slate-900">₦{{ number_format((float)$order->subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-black text-slate-900">Order Total</span>
                    <span class="text-xl font-black text-pink-600">₦{{ number_format((float)$order->total, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment details --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4 flex items-center gap-2">
                <x-heroicon-o-credit-card class="w-4 h-4 text-green-500" /> Payment Details
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                    <span class="{{ $payConfig['class'] }} mt-1 inline-block text-xs">{{ $payConfig['label'] }}</span>
                </div>
                @if($order->paystack_reference)
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Paystack Ref</p>
                        <p class="font-mono text-xs font-black text-slate-700 mt-1 break-all">{{ $order->paystack_reference }}</p>
                    </div>
                @endif
                @if($order->paystack_data && isset($order->paystack_data['paid_at']))
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Paid At</p>
                        <p class="font-bold text-slate-700 text-sm mt-1">{{ \Carbon\Carbon::parse($order->paystack_data['paid_at'])->format('d M Y, H:i') }}</p>
                    </div>
                @endif
                @if($order->paystack_data && isset($order->paystack_data['channel']))
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Channel</p>
                        <p class="font-bold text-slate-700 text-sm mt-1 capitalize">{{ $order->paystack_data['channel'] }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right sidebar --}}
    <div class="space-y-4">

        {{-- Customer info --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4 flex items-center gap-2">
                <x-heroicon-o-user-circle class="w-4 h-4 text-pink-500" /> Customer
            </h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Name</p>
                    <p class="font-black text-slate-900 mt-0.5">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                    <a href="mailto:{{ $order->customer_email }}"
                       class="font-bold text-slate-700 hover:text-pink-600 transition-colors mt-0.5 block break-all">{{ $order->customer_email }}</a>
                </div>
                @if($order->customer_phone)
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                        <a href="tel:{{ $order->customer_phone }}"
                           class="font-bold text-slate-700 hover:text-pink-600 transition-colors mt-0.5 block">{{ $order->customer_phone }}</a>
                    </div>
                @endif
                @if($order->user_id)
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Account</p>
                        <span class="pb-badge-success text-xs mt-0.5">Registered Customer</span>
                    </div>
                @else
                    <div>
                        <span class="pb-badge-neutral text-xs">Guest Order</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Shipping info --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4 flex items-center gap-2">
                <x-heroicon-o-map-pin class="w-4 h-4 text-pink-500" /> Delivery Address
            </h2>
            <div class="text-sm space-y-1.5">
                <p class="font-black text-slate-900">{{ $order->shipping_name }}</p>
                <p class="text-slate-600 leading-relaxed">{{ $order->shipping_address }}</p>
                <p class="text-slate-600">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                @if($order->shipping_notes)
                    <div class="mt-3 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                        <p class="text-xs font-bold text-amber-700 mb-1">Delivery Note</p>
                        <p class="text-xs text-amber-600">{{ $order->shipping_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Order meta --}}
        <div class="pb-card p-5">
            <h2 class="font-black text-slate-950 text-sm mb-4 flex items-center gap-2">
                <x-heroicon-o-clock class="w-4 h-4 text-slate-400" /> Order Info
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Order placed</span>
                    <span class="font-black text-slate-700">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Last updated</span>
                    <span class="font-black text-slate-700">{{ $order->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="text-slate-500 font-bold">Order ID</span>
                    <span class="font-mono font-black text-slate-700">{{ $order->id }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
