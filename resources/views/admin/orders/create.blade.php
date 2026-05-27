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
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">Log client brief, add order items, optionally create an invoice now.</p>
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
        @php
            $orderItems = old('order_items', [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'size_format' => '', 'material_substrate' => '', 'finish_lamination' => '', 'artwork_notes' => ''],
            ]);
        @endphp

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

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="fade-in-up section-delay-1 space-y-6">
            @csrf

            <!-- Hidden auto‑filled fields -->
            <input type="hidden" name="channel" value="Manual">
            <input type="hidden" name="job_type" value="Custom Order">

            <!-- Invoice Checkbox (first, controls rest of form) -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 border border-amber-200">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-black text-slate-950">Invoice & Pricing</h2>
                </div>
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="generate_invoice" value="0">
                    <input type="checkbox" name="generate_invoice" value="1" id="generate-invoice-checkbox"
                           class="mt-1 h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                           @checked(old('generate_invoice', true))>
                    <span>
                        <strong>Create invoice along with order</strong>
                        <span class="block mt-1 text-xs font-bold text-slate-500">If checked, an invoice will be generated based on the order items. Unit prices can be added later from the invoice.</span>
                    </span>
                </label>
            </div>

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

                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Need to add a new customer?</p>
                        <button
                            type="button"
                            id="job-toggle-new-customer"
                            class="rounded-lg border border-cyan-200 bg-white px-3 py-2 text-xs font-black uppercase tracking-wide text-cyan-700 transition-colors hover:bg-cyan-50"
                            aria-expanded="false"
                            aria-controls="job-new-customer-form"
                        >
                            Add New Customer
                        </button>
                    </div>

                    <div id="job-new-customer-form" class="hidden rounded-xl border border-cyan-100 bg-cyan-50/30 p-4">
                        <livewire:admin.customer-quick-create />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2 mt-4">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Client Name *</label>
                            <input id="job-customer-name" name="customer_name" value="{{ old('customer_name') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="Full name">
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Client Email *</label>
                            <input id="job-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="email@example.com">
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Client Phone *</label>
                            <input id="job-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="+234 XXX XXX XXXX">
                        </div>
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
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Priority *</label>
                        <select name="priority" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            @foreach ($priorities as $priority)<option @selected(old('priority', '🟡 Normal') === $priority)>{{ $priority }}</option>@endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Assigned Designer</label>
                        <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-600">Auto-assigned after job creation.</p>
                    </div>
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Brief Date</label>
                        <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-600">Set to job creation date/time.</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Job Image Assets</label>
                        <livewire:uploads.secure-image-upload
                            input-name="job_asset_image_paths"
                            :multiple="true"
                            directory="job-assets/images"
                            :max-size-kb="5120"
                            :max-files="20"
                            :initial-paths="old('job_asset_image_paths', [])"
                        />
                        <p class="mt-2 text-xs text-slate-500">Upload image assets securely via Livewire (JPG, PNG, WEBP up to 5MB each).</p>
                        @error('job_asset_image_paths')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                        @error('job_asset_image_paths.*')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Artwork Documents (PDF, SVG, ZIP)</label>
                        <input type="file" name="job_asset_files[]" multiple accept=".pdf,.svg,.zip" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        <p class="mt-2 text-xs text-slate-500">Non-image assets (PDF, SVG, ZIP up to 20MB each).</p>
                        @error('job_asset_files.*')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8" id="order-items-section">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-violet-100 to-violet-50 border border-violet-200">
                        <svg class="w-5 h-5 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Order Items</h2>
                        <p class="text-sm text-slate-500" id="order-items-hint">Add items with descriptions, quantities, and optional reference images. Unit price can be added when creating an invoice.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid gap-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-100 rounded-2xl border border-slate-200 p-4" id="order-items-header">
                        <div class="grid sm:grid-cols-[4fr_1fr_1fr_1fr_auto]">
                            <div>Description</div>
                            <div>Qty</div>
                            <div>Unit Price (₦)</div>
                            <div>Amount</div>
                            <div>Size / Format</div>
                            <div>Material / Substrate</div>
                            <div>Finish / Lamination</div>
                            <div>Artwork Notes</div>
                            <div>Image</div>
                            <div class="sr-only">Remove</div>
                        </div>
                    </div>

                    <div id="order-items-rows" class="space-y-3">
                        @foreach ($orderItems as $index => $item)
                            <div class="order-item-row grid gap-3 sm:grid-cols-[4fr_1fr_1fr_1fr_1fr_1fr_1fr_1fr_auto] items-end rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Description *</label>
                                    <input type="text" name="order_items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required
                                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Add item details">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Qty *</label>
                                    <input type="number" min="1" name="order_items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required
                                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                </div>
                                <div class="space-y-1" id="invoice-unit-price-field-{{ $index }}">
                                    <label class="text-xs font-black text-slate-700">Unit Price (₦)</label>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-slate-500">₦</span>
                                        <input type="number" min="0" step="0.01" name="order_items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}"
                                               class="order-item-unit-price w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Amount</label>
                                    <p class="order-item-amount rounded-xl border border-slate-200 bg-slate-100 px-3 py-3 text-sm font-bold text-slate-700">
                                        ₦{{ number_format(((float)($item['unit_price'] ?? 0)) * ((int)($item['quantity'] ?? 1)), 2) }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Size / Format</label>
                                    <select name="order_items[{{ $index }}][size_format]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                        <option value="">— Select —</option>
                                        @foreach ($sizes as $size)<option @selected(($item['size_format'] ?? '') === $size)>{{ $size }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Material / Substrate</label>
                                    <select name="order_items[{{ $index }}][material_substrate]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                        <option value="">— Select —</option>
                                        @foreach ($materials as $material)<option @selected(($item['material_substrate'] ?? '') === $material)>{{ $material }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Finish / Lamination</label>
                                    <select name="order_items[{{ $index }}][finish_lamination]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                        <option value="">— Select —</option>
                                        @foreach ($finishes as $finish)<option @selected(($item['finish_lamination'] ?? '') === $finish)>{{ $finish }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Artwork Notes</label>
                                    <input type="text" name="order_items[{{ $index }}][artwork_notes]" value="{{ $item['artwork_notes'] ?? '' }}"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Optional notes">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-black text-slate-700">Image</label>
                                    <input type="file" name="order_items[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="remove-order-item-row inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 transition-colors hover:border-pink-300 hover:text-pink-700" aria-label="Remove item row">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <button type="button" id="add-order-item-row" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black text-slate-900 transition-all duration-200 hover:border-pink-300 hover:bg-pink-50 hover:text-pink-700">
                            + Add another item
                        </button>
                    </div>
                </div>
                @error('order_items')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('order_items.*.description')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('order_items.*.quantity')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('order_items.*.image')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
            </div>

            <!-- Internal Notes -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="space-y-1">
                    <label class="flex items-center gap-2 text-sm font-black text-slate-700">Internal Notes</label>
                    <textarea name="internal_notes" rows="4" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Job
                </span>
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            </button>
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
            const toggleNewCustomerButton = document.getElementById('job-toggle-new-customer');
            const newCustomerForm = document.getElementById('job-new-customer-form');

            // Invoice toggle
            const generateInvoiceCheckbox = document.getElementById('generate-invoice-checkbox');
            const orderItemsHint = document.getElementById('order-items-hint');

            // Order items rows
            const itemsContainer = document.getElementById('order-items-rows');
            const addItemBtn = document.getElementById('add-order-item-row');

            // --- Customer helpers ---
            const hydrateFromOption = (option) => {
                if (!option) return;
                nameInput.value = option.getAttribute('data-customer-name') ?? nameInput.value;
                emailInput.value = option.getAttribute('data-customer-email') ?? emailInput.value;
                phoneInput.value = option.getAttribute('data-customer-phone') ?? phoneInput.value;
            };

            const setNewCustomerFormVisible = (visible) => {
                if (!newCustomerForm || !toggleNewCustomerButton) return;
                newCustomerForm.classList.toggle('hidden', !visible);
                toggleNewCustomerButton.setAttribute('aria-expanded', visible ? 'true' : 'false');
                toggleNewCustomerButton.textContent = visible ? 'Hide New Customer Form' : 'Add New Customer';
            };

            customerSelect?.addEventListener('change', (event) => {
                hydrateFromOption(event.target.selectedOptions?.[0]);
            });

            toggleNewCustomerButton?.addEventListener('click', () => {
                setNewCustomerFormVisible(newCustomerForm?.classList.contains('hidden'));
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
                setNewCustomerFormVisible(false);
            });

            // --- Invoice toggle logic ---
            function toggleUnitPriceFields() {
                const generateInvoice = generateInvoiceCheckbox.checked;
                const unitPriceFields = document.querySelectorAll('[id^="invoice-unit-price-field-"]');
                unitPriceFields.forEach(field => {
                    field.style.display = generateInvoice ? 'block' : 'none';
                });
                // Recalculate amounts visibility
                updateAmountFields();
                orderItemsHint.textContent = generateInvoice
                    ? 'Add items with descriptions, quantities, and optional reference images. Unit prices are now available for invoicing.'
                    : 'Add items with descriptions, quantities, and optional reference images. Invoice can be created for this order later.';
            }

            // Calculate line item amounts
            function updateAmountFields() {
                document.querySelectorAll('.order-item-row').forEach(row => {
                    const qtyInput = row.querySelector('input[name*="[quantity]"]');
                    const priceInput = row.querySelector('.order-item-unit-price');
                    const amountDisplay = row.querySelector('.order-item-amount');
                    if (qtyInput && priceInput && amountDisplay) {
                        const qty = parseInt(qtyInput.value) || 0;
                        const price = parseFloat(priceInput.value) || 0;
                        const amount = qty * price;
                        amountDisplay.textContent = '₦' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    }
                });
            }

            // Listen for changes in unit price and quantity
            document.addEventListener('input', function(e) {
                if (e.target.matches('.order-item-unit-price') || e.target.matches('input[name*="[quantity]"]')) {
                    updateAmountFields();
                }
            });

            generateInvoiceCheckbox.addEventListener('change', toggleUnitPriceFields);

            // Initialize on load
            setTimeout(toggleUnitPriceFields, 100);

            // --- Order items row management ---
            function renumberOrderItemRows() {
                const rows = itemsContainer.querySelectorAll('.order-item-row');
                rows.forEach((row, idx) => {
                    row.querySelectorAll('[name]').forEach(input => {
                        input.name = input.name.replace(/order_items\[\d+\]/, `order_items[${idx}]`);
                    });
                });
            }

            function createOrderItemRow() {
                const row = document.createElement('div');
                row.className = 'order-item-row grid gap-3 sm:grid-cols-[4fr_1fr_1fr_1fr_1fr_1fr_1fr_1fr_auto] items-end rounded-2xl border border-slate-200 bg-slate-50 p-4';
                row.innerHTML = `
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Description *</label>
                        <input type="text" name="order_items[0][description]" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Add item details">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Qty *</label>
                        <input type="number" min="1" name="order_items[0][quantity]" value="1" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    </div>
                    <div class="space-y-1" id="invoice-unit-price-field-0">
                        <label class="text-xs font-black text-slate-700">Unit Price (₦)</label>
                        <input type="number" min="0" step="0.01" name="order_items[0][unit_price]" value="0"
                               class="order-item-unit-price w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Amount</label>
                        <p class="order-item-amount rounded-xl border border-slate-200 bg-slate-100 px-3 py-3 text-sm font-bold text-slate-700">₦0.00</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Size / Format</label>
                        <select name="order_items[0][size_format]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select —</option>
                            @foreach ($sizes as $size)<option>{{ $size }}</option>@endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Material / Substrate</label>
                        <select name="order_items[0][material_substrate]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select —</option>
                            @foreach ($materials as $material)<option>{{ $material }}</option>@endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Finish / Lamination</label>
                        <select name="order_items[0][finish_lamination]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select —</option>
                            @foreach ($finishes as $finish)<option>{{ $finish }}</option>@endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Artwork Notes</label>
                        <input type="text" name="order_items[0][artwork_notes]" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-800 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Optional notes">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-700">Image</label>
                        <input type="file" name="order_items[0][image]" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" class="remove-order-item-row inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 transition-colors hover:border-pink-300 hover:text-pink-700" aria-label="Remove item row">&times;</button>
                    </div>
                `;
                return row;
            }

            addItemBtn?.addEventListener('click', () => {
                const newRow = createOrderItemRow();
                itemsContainer.appendChild(newRow);
                renumberOrderItemRows();
            });

            itemsContainer?.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-order-item-row');
                if (!removeBtn) return;
                const row = removeBtn.closest('.order-item-row');
                if (itemsContainer.querySelectorAll('.order-item-row').length <= 1) return;
                row.remove();
                renumberOrderItemRows();
            });

            // Init states
            setNewCustomerFormVisible(false);
            updateInvoiceMode();
        })();
    </script>
@endsection