@extends('layouts.admin')

@section('title', 'Create Admin Job | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.orders.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Job Tracker
                </a>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">Create a new job</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">Log the client brief, create the job order, and send the invoice in one streamlined step.</p>
                </div>
                <div class="hidden sm:block">
                    <div class="rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 p-3 border border-cyan-500/20">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Summary -->
        @if ($errors->any())
            <div class="fade-in-up rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-black text-red-800">Please review the highlighted details:</p>
                        <ul class="mt-2 space-y-1 text-sm font-semibold text-red-700">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center gap-2">
                                    <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="fade-in-up section-delay-1 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            @csrf

            <!-- Main Form Section -->
            <section class="space-y-6">
                <!-- Client Section -->
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                            <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Client Information</h2>
                            <p class="text-sm text-slate-500">Select existing or create new customer</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Existing Customer
                        </label>
                        <select id="job-customer-select" name="customer_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select existing customer —</option>
                            @foreach ($customers as $customer)
                                <option
                                    value="{{ $customer->id }}"
                                    data-customer-name="{{ $customer->displayName() }}"
                                    data-customer-email="{{ $customer->email }}"
                                    data-customer-phone="{{ $customer->phone }}"
                                    @selected((int) old('customer_id') === $customer->id)
                                >
                                    {{ $customer->displayName() }} · {{ $customer->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative my-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-3 text-xs font-black uppercase tracking-wider text-slate-400">Or</span>
                        </div>
                    </div>

                    <livewire:admin.customer-quick-create />

                    <div class="grid gap-5 sm:grid-cols-2 mt-4">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Client Name *
                            </label>
                            <input id="job-customer-name" name="customer_name" value="{{ old('customer_name') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="Full name">
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Client Email *
                            </label>
                            <input id="job-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="email@example.com">
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Client Phone *
                            </label>
                            <input id="job-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="+234 XXX XXX XXXX">
                        </div>
                    </div>
                </div>

                <!-- Job Brief Section -->
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                            <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Job Brief</h2>
                            <p class="text-sm text-slate-500">Define job specifications and requirements</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Channel *</label>
                            <select name="channel" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                @foreach ($channels as $channel)<option @selected(old('channel', 'Manual') === $channel)>{{ $channel }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Product</label>
                            <select name="product_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Custom job —</option>
                                @foreach ($products as $product)<option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Job Type *</label>
                            <select name="job_type" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select job type —</option>
                                @foreach ($jobTypes as $jobType)<option @selected(old('job_type') === $jobType)>{{ $jobType }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Size / Format</label>
                            <select name="size_format" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select size —</option>
                                @foreach ($sizes as $size)<option @selected(old('size_format') === $size)>{{ $size }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Priority *</label>
                            <select name="priority" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                @foreach ($priorities as $priority)<option @selected(old('priority', '🟡 Normal') === $priority)>{{ $priority }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Assigned Designer</label>
                            <select name="assigned_designer_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select designer —</option>
                                @foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('assigned_designer_id') === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Brief Date</label>
                            <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-600">Automatically set to job creation date/time.</p>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Material / Substrate</label>
                            <select name="material_substrate" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select material —</option>
                                @foreach ($materials as $material)<option @selected(old('material_substrate') === $material)>{{ $material }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Finish / Lamination</label>
                            <select name="finish_lamination" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select finish —</option>
                                @foreach ($finishes as $finish)<option @selected(old('finish_lamination') === $finish)>{{ $finish }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Artwork Notes</label>
                            <textarea name="artwork_notes" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Describe artwork requirements...">{{ old('artwork_notes') }}</textarea>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Job Image / Artwork Assets</label>
                            <input type="file" name="job_asset_files[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.svg,.zip" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <p class="mt-2 text-xs text-slate-500">Upload client artwork, images, PDFs, SVG files or ZIP archives up to 20MB each.</p>
                        </div>
                    </div>
                </div>

                <!-- Delivery Section -->
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Delivery Preference</h2>
                            <p class="text-sm text-slate-500">Choose how the client will receive the job</p>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 mb-4">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 text-sm font-black transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                            <input id="delivery-preference-pickup" type="radio" name="delivery_preference" value="pickup" @checked(old('delivery_preference') === 'pickup') class="h-5 w-5 border-slate-300 text-pink-600 focus:ring-pink-500">
                            <div>
                                <p class="font-black text-slate-900">Client Pickup</p>
                                <p class="text-xs text-slate-500 mt-0.5">Client will collect from office</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 text-sm font-black transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                            <input id="delivery-preference-delivery" type="radio" name="delivery_preference" value="delivery" @checked(old('delivery_preference', 'delivery') === 'delivery') class="h-5 w-5 border-slate-300 text-pink-600 focus:ring-pink-500">
                            <div>
                                <p class="font-black text-slate-900">Delivery</p>
                                <p class="text-xs text-slate-500 mt-0.5">Deliver to client address</p>
                            </div>
                        </label>
                    </div>

                    <div id="delivery-fields" class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery Method</label>
                            <select id="delivery-method" name="delivery_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                <option value="">— Select delivery method —</option>
                                @foreach ($deliveryMethods as $method)@continue($method === 'Client Pickup')<option value="{{ $method }}" @selected(old('delivery_method') === $method)>{{ $method }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery City</label>
                            <input id="delivery-city" name="delivery_city" value="{{ old('delivery_city') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="e.g., Lagos">
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery Address</label>
                            <input id="delivery-address" name="delivery_address" value="{{ old('delivery_address') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Full delivery address">
                        </div>
                    </div>
                </div>

                <!-- Invoice Section -->
                <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Invoice Details</h2>
                            <p class="text-sm text-slate-500">Set pricing and payment information</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-2 sm:col-span-2">
                            <p class="text-sm font-black text-slate-700">Fulfilment Speed</p>
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">
                                <input id="order-is-express" type="checkbox" name="is_express" value="1" @checked(old('is_express')) class="mt-1 h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                                <span>
                                    Express order (+₦{{ number_format((float) ($expressSurcharge ?? 0), 2) }})
                                    <span class="mt-1 block text-xs font-bold text-slate-500">Estimated delivery is 48 hours from confirmed payment.</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">
                                <input id="order-is-sample" type="checkbox" name="is_sample" value="1" @checked(old('is_sample')) class="mt-1 h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                                <span>
                                    Sample order (+₦{{ number_format((float) ($sampleSurcharge ?? 5000), 2) }})
                                    <span class="mt-1 block text-xs font-bold text-slate-500">Sample orders are auto-express and limited to 2 units.</span>
                                </span>
                            </label>
                            @error('is_express')<p class="text-sm font-semibold text-pink-700">{{ $message }}</p>@enderror
                            @error('is_sample')<p class="text-sm font-semibold text-pink-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Quantity *</label>
                            <input id="job-quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <p id="job-quantity-hint" class="text-xs font-bold text-slate-500">{{ old('is_sample') ? 'Sample quantity must be 1 or 2.' : 'Set quantity based on the requested production volume.' }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Unit Price (₦) *</label>
                            <input type="number" min="0" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Amount Paid (₦)</label>
                            <input type="number" min="0" step="0.01" name="amount_paid" value="{{ old('amount_paid', 0) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Payment Status *</label>
                            <select name="payment_status" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                @foreach ($paymentStatuses as $status)<option @selected(old('payment_status', 'Invoice Issued') === $status)>{{ $status }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Internal Notes</label>
                            <textarea name="internal_notes" rows="4" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Job & Send Invoice
                    </span>
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </button>
            </section>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <div class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-pink-50/50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-pink-700">Access</p>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-600">Only Super Admin, Management, and Customer Service can create jobs. The job starts at Analyzing Job Brief and is assigned a unique reference code after saving.</p>
                </div>

                <div class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-cyan-50/50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Created By</p>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-600">This job will be recorded under <strong class="text-slate-900">{{ auth()->user()->displayName() }}</strong>.</p>
                </div>

                <div class="rounded-2xl border border-slate-200/60 bg-gradient-to-br from-cyan-50/50 to-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Invoice Email</p>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-600">An invoice is generated immediately and sent to the client email address on this form.</p>
                </div>
            </aside>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
        .section-delay-1 { animation-delay: 0.05s; }
    </style>

    <script>
        (() => {
            const customerSelect = document.getElementById('job-customer-select');
            const nameInput = document.getElementById('job-customer-name');
            const emailInput = document.getElementById('job-customer-email');
            const phoneInput = document.getElementById('job-customer-phone');
            const pickupRadio = document.getElementById('delivery-preference-pickup');
            const deliveryRadio = document.getElementById('delivery-preference-delivery');
            const deliveryFields = document.getElementById('delivery-fields');
            const deliveryMethod = document.getElementById('delivery-method');
            const deliveryCity = document.getElementById('delivery-city');
            const deliveryAddress = document.getElementById('delivery-address');
            const quantityInput = document.getElementById('job-quantity');
            const quantityHint = document.getElementById('job-quantity-hint');
            const expressCheckbox = document.getElementById('order-is-express');
            const sampleCheckbox = document.getElementById('order-is-sample');

            const hydrateFromOption = (option) => {
                if (!option) return;
                nameInput.value = option.getAttribute('data-customer-name') ?? nameInput.value;
                emailInput.value = option.getAttribute('data-customer-email') ?? emailInput.value;
                phoneInput.value = option.getAttribute('data-customer-phone') ?? phoneInput.value;
            };

            const syncDeliveryState = () => {
                const isDelivery = deliveryRadio?.checked;
                if (!deliveryFields) return;
                deliveryFields.style.display = isDelivery ? 'grid' : 'none';
                deliveryMethod.required = Boolean(isDelivery);
                deliveryCity.required = Boolean(isDelivery);
                deliveryAddress.required = Boolean(isDelivery);
                if (!isDelivery) {
                    deliveryMethod.value = '';
                    deliveryCity.value = '';
                    deliveryAddress.value = '';
                }
            };

            const syncSampleRules = () => {
                if (!quantityInput) return;

                const isSample = Boolean(sampleCheckbox?.checked);

                if (isSample && expressCheckbox) {
                    expressCheckbox.checked = true;
                    expressCheckbox.disabled = true;
                } else if (expressCheckbox) {
                    expressCheckbox.disabled = false;
                }

                quantityInput.min = '1';

                if (isSample) {
                    quantityInput.max = '2';
                    if (Number(quantityInput.value || 0) > 2) {
                        quantityInput.value = '2';
                    }
                } else {
                    quantityInput.removeAttribute('max');
                }

                if (quantityHint) {
                    quantityHint.textContent = isSample
                        ? 'Sample quantity must be 1 or 2.'
                        : 'Set quantity based on the requested production volume.';
                }
            };

            customerSelect?.addEventListener('change', (event) => {
                hydrateFromOption(event.target.selectedOptions?.[0]);
            });

            window.addEventListener('admin-customer-created', (event) => {
                const rawDetail = event.detail ?? {};
                const detail = Array.isArray(rawDetail) ? (rawDetail[0] ?? {}) : rawDetail;
                const customer = detail.customer ?? {};
                if (!customerSelect || !customer.id) return;

                const customerId = String(customer.id);
                let option = Array.from(customerSelect.options).find((item) => item.value === customerId);
                if (!option) {
                    option = document.createElement('option');
                    option.value = customerId;
                    customerSelect.appendChild(option);
                }
                option.textContent = `${customer.name} · ${customer.email}`;
                option.setAttribute('data-customer-name', customer.name ?? '');
                option.setAttribute('data-customer-email', customer.email ?? '');
                option.setAttribute('data-customer-phone', customer.phone ?? '');
                customerSelect.value = customerId;
                hydrateFromOption(option);
            });

            pickupRadio?.addEventListener('change', syncDeliveryState);
            deliveryRadio?.addEventListener('change', syncDeliveryState);
            expressCheckbox?.addEventListener('change', syncSampleRules);
            sampleCheckbox?.addEventListener('change', syncSampleRules);
            syncDeliveryState();
            syncSampleRules();
        })();
    </script>
@endsection
