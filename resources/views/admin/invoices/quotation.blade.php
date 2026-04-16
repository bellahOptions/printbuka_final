@extends('layouts.admin')

@section('title', 'Create Quotation | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.invoices.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">Create quotation</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">Generate a professional quote for new or existing customers. Send it directly via email or save as draft.</p>
                </div>
                <div class="hidden sm:block">
                    <div class="rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 p-3 border border-cyan-500/20">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
                        <p class="text-sm font-black text-red-800">Please review the following issues:</p>
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

        <!-- Main Form -->
        <form action="{{ route('admin.invoices.quotations.store') }}" method="POST" class="fade-in-up section-delay-1 space-y-6">
            @csrf
            
            <!-- Customer Section -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                        <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Customer Information</h2>
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
                        <select id="quotation-customer-select" name="customer_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select a customer —</option>
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

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-3 text-xs font-black uppercase tracking-wider text-slate-400">Or</span>
                        </div>
                    </div>

                    <div>
                        <livewire:admin.customer-quick-create />
                    </div>
                </div>
            </div>

            <!-- Customer Details Grid -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Customer Name *
                        </label>
                        <input id="quotation-customer-name" name="customer_name" value="{{ old('customer_name') }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="Full name">
                        @error('customer_name')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Customer Email *
                        </label>
                        <input id="quotation-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="email@example.com">
                        @error('customer_email')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Customer Phone *
                        </label>
                        <input id="quotation-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="+234 XXX XXX XXXX">
                        @error('customer_phone')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Product
                        </label>
                        <select name="product_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Custom job —</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Job Details Section -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                        <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Job Specifications</h2>
                        <p class="text-sm text-slate-500">Define the job details and pricing</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Job Type *
                        </label>
                        <select name="job_type" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select job type —</option>
                            @foreach ($jobTypes as $jobType)
                                <option @selected(old('job_type') === $jobType)>{{ $jobType }}</option>
                            @endforeach
                        </select>
                        @error('job_type')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            Size / Format
                        </label>
                        <select name="size_format" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select size —</option>
                            @foreach ($sizes as $size)
                                <option @selected(old('size_format') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Quantity *
                        </label>
                        <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        @error('quantity')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Unit Price (₦) *
                        </label>
                        <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price') }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="0.00">
                        @error('unit_price')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                            Tax (₦)
                        </label>
                        <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="0.00">
                        @error('tax_amount')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Discount (₦)
                        </label>
                        <input type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', 0) }}" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="0.00">
                        @error('discount_amount')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Due Date
                        </label>
                        <input type="datetime-local" name="due_at" value="{{ old('due_at', now()->addDays(7)->format('Y-m-d\\TH:i')) }}" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        @error('due_at')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Delivery City
                        </label>
                        <input name="delivery_city" value="{{ old('delivery_city') }}" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="e.g., Lagos">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Delivery Address
                        </label>
                        <input name="delivery_address" value="{{ old('delivery_address') }}" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                               placeholder="Full delivery address">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Artwork / Brief Notes
                        </label>
                        <textarea name="artwork_notes" rows="4" 
                                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none"
                                  placeholder="Describe artwork requirements, special instructions, etc.">{{ old('artwork_notes') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Internal Notes
                        </label>
                        <textarea name="internal_notes" rows="4" 
                                  class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none"
                                  placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Email Option & Submit -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="send_email" value="1" @checked(old('send_email', true)) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                    <div>
                        <p class="text-sm font-black text-slate-900">Send quotation email immediately</p>
                        <p class="text-xs text-slate-500 mt-1">The quotation will be sent to the customer's email address</p>
                    </div>
                </label>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="btn-primary group relative overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-8 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Create Quotation
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .section-delay-1 { animation-delay: 0.05s; }
    </style>

    <script>
        (() => {
            const customerSelect = document.getElementById('quotation-customer-select');
            const nameInput = document.getElementById('quotation-customer-name');
            const emailInput = document.getElementById('quotation-customer-email');
            const phoneInput = document.getElementById('quotation-customer-phone');

            const hydrateFromOption = (option) => {
                if (!option) {
                    return;
                }

                nameInput.value = option.getAttribute('data-customer-name') ?? nameInput.value;
                emailInput.value = option.getAttribute('data-customer-email') ?? emailInput.value;
                phoneInput.value = option.getAttribute('data-customer-phone') ?? phoneInput.value;
            };

            customerSelect?.addEventListener('change', (event) => {
                const option = event.target.selectedOptions?.[0];
                hydrateFromOption(option);
            });

            window.addEventListener('admin-customer-created', (event) => {
                const rawDetail = event.detail ?? {};
                const detail = Array.isArray(rawDetail) ? (rawDetail[0] ?? {}) : rawDetail;
                const customer = detail.customer ?? {};

                if (!customerSelect || !customer.id) {
                    return;
                }

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
        })();
    </script>
@endsection