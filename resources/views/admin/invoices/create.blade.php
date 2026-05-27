@extends('layouts.admin')

@section('title', 'Create Invoice | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="mb-4 flex items-center gap-2">
                <a href="{{ route('admin.invoices.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">Create invoice</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">
                        Create invoices using only Printbuka catalog products/services. Choose whether to save, download, or send after save.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <div class="rounded-xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 p-3">
                        <svg class="h-8 w-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="fade-in-up rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-black text-red-800">Please review the following issues:</p>
                        <ul class="mt-2 space-y-1 text-sm font-semibold text-red-700">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center gap-2">
                                    <span class="h-1 w-1 rounded-full bg-red-400"></span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.invoices.store') }}" method="POST" class="fade-in-up section-delay-1 space-y-6">
            @csrf
            @php
                $lineItems = collect(old('line_items', []))
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item): array => [
                        'description' => (string) ($item['description'] ?? ''),
                        'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                        'rate' => max(0, (float) ($item['rate'] ?? 0)),
                    ])
                    ->values();

                if ($lineItems->isEmpty()) {
                    $lineItems = collect([[
                        'description' => '',
                        'quantity' => 1,
                        'rate' => 0,
                    ]]);
                }
            @endphp

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="mb-6 flex items-center gap-3">
                    <div class="rounded-xl border border-pink-200 bg-gradient-to-br from-pink-100 to-pink-50 p-2">
                        <svg class="h-5 w-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Existing Customer
                        </label>
                        <select id="invoice-customer-select" name="customer_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
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

                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Need to add a new customer?</p>
                        <button
                            type="button"
                            id="invoice-toggle-new-customer"
                            class="rounded-lg border border-cyan-200 bg-white px-3 py-2 text-xs font-black uppercase tracking-wide text-cyan-700 transition-colors hover:bg-cyan-50"
                            aria-expanded="false"
                            aria-controls="invoice-new-customer-form"
                        >
                            Add New Customer
                        </button>
                    </div>

                    <div id="invoice-new-customer-form" class="hidden rounded-xl border border-cyan-100 bg-cyan-50/30 p-4">
                        <livewire:admin.customer-quick-create />
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-amber-50/70 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Need to create an order first?</p>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.orders.create') }}" class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs font-black uppercase tracking-wide text-amber-700 transition-colors hover:bg-amber-50">
                                Create Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Customer Name *</label>
                        <input id="invoice-customer-name" name="customer_name" value="{{ old('customer_name') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Full name">
                        @error('customer_name')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Customer Email</label>
                        <input id="invoice-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="email@example.com">
                        <p class="text-xs font-semibold text-slate-500">Optional. Required only when using "Save & Send".</p>
                        @error('customer_email')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Customer Phone *</label>
                        <input id="invoice-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="+234 XXX XXX XXXX">
                        @error('customer_phone')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Size / Format</label>
                        <select id="catalog-size-format" name="size_format" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Material / Substrate</label>
                        <select id="catalog-material-substrate" name="material_substrate" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Paper Density</label>
                        <select id="catalog-paper-density" name="paper_density" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Finish / Lamination</label>
                        <select id="catalog-finish-lamination" name="finish_lamination" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="space-y-1 sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery Method</label>
                        <select id="catalog-delivery-method" name="delivery_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="mb-6 flex items-center gap-3">
                    <div class="rounded-xl border border-cyan-200 bg-gradient-to-br from-cyan-100 to-cyan-50 p-2">
                        <svg class="h-5 w-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Catalog Item & Pricing</h2>
                        <p class="text-sm text-slate-500">Choose only from Printbuka products/services</p>
                    </div>
                </div>

                <div class="catalog-item-grid grid grid-cols-2 gap-4 sm:gap-5 sm:grid-cols-2">
                    <div class="catalog-item-field catalog-item-field--item col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Product / Service *</label>
                        <select id="catalog-item-select" name="catalog_item_key" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            <option value="">— Select product or service —</option>
                            <optgroup label="Products">
                                @foreach ($products as $product)
                                    <option
                                        value="product:{{ $product->id }}"
                                        data-unit-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                        data-item-name="{{ $product->name }}"
                                        @selected(old('catalog_item_key') === 'product:'.$product->id)
                                    >
                                        {{ $product->name }} · ₦{{ number_format((float) $product->price, 2) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Services">
                                @foreach ($services as $service)
                                    <option
                                        value="{{ $service['key'] }}"
                                        data-unit-price="{{ number_format((float) $service['price'], 2, '.', '') }}"
                                        data-item-name="{{ $service['name'] }}"
                                        @selected(old('catalog_item_key') === $service['key'])
                                    >
                                        {{ $service['name'] }} · ₦{{ number_format((float) $service['price'], 2) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('catalog_item_key')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field catalog-item-field--quantity space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Quantity *</label>
                        <input id="catalog-quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        @error('quantity')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field catalog-item-field--rate space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Unit Price (₦)</label>
                        <div class="flex items-center overflow-hidden rounded-xl border border-slate-300 bg-slate-50">
                            <span class="px-3 text-sm font-black text-slate-500">₦</span>
                            <input id="catalog-unit-price-display" type="text" readonly class="w-full border-0 bg-transparent px-3 py-3.5 text-sm font-black text-slate-800 focus:ring-0">
                        </div>
                        <input id="catalog-unit-price" type="hidden" value="0">
                    </div>

                    <div class="catalog-item-field catalog-item-field--amount col-span-2 space-y-1 sm:hidden">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Amount</label>
                        <p id="catalog-mobile-amount" class="rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-xl font-black text-slate-900">₦0.00</p>
                    </div>

                    <div class="catalog-item-field space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Tax (₦)</label>
                        <input id="catalog-tax" type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                        @error('tax_amount')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Discount (₦)</label>
                        <input id="catalog-discount" type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', 0) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="0.00">
                        @error('discount_amount')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Item</p>
                        <p id="catalog-item-name" class="mt-2 text-sm font-black text-slate-900">—</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Subtotal</p>
                        <p id="catalog-subtotal" class="mt-2 text-lg font-black text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Adjustments</p>
                        <p id="catalog-adjustments" class="mt-2 text-lg font-black text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-pink-700">Invoice Total</p>
                        <p id="catalog-total" class="mt-2 text-lg font-black text-pink-700">₦0.00</p>
                    </div>
                </div>

                <div class="mt-6 rounded-xl border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-black text-slate-900">Editable Invoice Items (Optional)</p>
                        <p class="text-xs text-slate-500">Add item rows to override catalog-generated invoice items.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-sm">
                            <thead class="bg-white text-xs font-black uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3">Rate</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-line-items" class="divide-y divide-slate-100">
                                @foreach ($lineItems as $index => $item)
                                    <tr data-line-item-row>
                                        <td class="px-4 py-3">
                                            <input name="line_items[{{ $index }}][description]" value="{{ $item['description'] }}" data-line-item-description class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800" placeholder="Item description">
                                            <input type="hidden" name="line_items[{{ $index }}][source_type]" value="custom" data-line-item-source>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="1" step="1" name="line_items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" data-line-item-quantity class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" step="0.01" name="line_items[{{ $index }}][rate]" value="{{ $item['rate'] }}" data-line-item-rate class="w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800">
                                        </td>
                                        <td class="px-4 py-3 font-black text-slate-900" data-line-item-amount>₦0.00</td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" data-remove-line-item class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black uppercase tracking-wide text-slate-500 hover:border-red-200 hover:text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-3">
                        <button type="button" id="add-invoice-line-item" class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-emerald-700 hover:bg-emerald-100">Add Item Row</button>
                    </div>
                </div>
                @error('line_items')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.description')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.quantity')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.rate')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Due Date</label>
                        <input type="datetime-local" name="due_at" value="{{ old('due_at', now()->addDays(7)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        @error('due_at')<p class="mt-1.5 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Invoice Status</label>
                        <select name="invoice_status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            @foreach ($invoiceStatuses as $value)
                                <option value="{{ $value }}" @selected(old('invoice_status', 'draft') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery City</label>
                        <input name="delivery_city" value="{{ old('delivery_city') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="e.g., Lagos">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Delivery Address</label>
                        <input name="delivery_address" value="{{ old('delivery_address') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Full delivery address">
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Artwork / Brief Notes</label>
                        <textarea name="artwork_notes" rows="4" class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Describe artwork requirements, special instructions, etc.">{{ old('artwork_notes') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Internal Notes</label>
                        <textarea name="internal_notes" rows="4" class="w-full resize-none rounded-xl border border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8">
                <p class="text-sm font-black text-slate-900">Save Options</p>
                <p class="mt-1 text-xs text-slate-500">Invoices without an email can only be saved or downloaded.</p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="submit" name="action" value="save" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white transition hover:bg-slate-800">
                        Save Invoice
                    </button>
                    <button type="submit" name="action" value="save_download" class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-900 transition hover:bg-slate-50">
                        Save & Download
                    </button>
                    <button type="submit" name="action" value="save_send" id="invoice-save-send-button" class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                        Save & Send
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="text-sm font-semibold text-slate-500 transition-colors hover:text-slate-700">Cancel</a>
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

        @media (max-width: 640px) {
            .catalog-item-grid {
                gap: 0.75rem;
            }
        }
    </style>

    <script>
        (() => {
            const customerSelect = document.getElementById('invoice-customer-select');
            const nameInput = document.getElementById('invoice-customer-name');
            const emailInput = document.getElementById('invoice-customer-email');
            const phoneInput = document.getElementById('invoice-customer-phone');
            const toggleNewCustomerButton = document.getElementById('invoice-toggle-new-customer');
            const newCustomerForm = document.getElementById('invoice-new-customer-form');
            const catalogSelect = document.getElementById('catalog-item-select');
            const quantityInput = document.getElementById('catalog-quantity');
            const taxInput = document.getElementById('catalog-tax');
            const discountInput = document.getElementById('catalog-discount');
            const unitPriceInput = document.getElementById('catalog-unit-price');
            const unitPriceDisplay = document.getElementById('catalog-unit-price-display');
            const subtotalDisplay = document.getElementById('catalog-subtotal');
            const adjustmentsDisplay = document.getElementById('catalog-adjustments');
            const totalDisplay = document.getElementById('catalog-total');
            const itemNameDisplay = document.getElementById('catalog-item-name');
            const mobileAmountDisplay = document.getElementById('catalog-mobile-amount');
            const sizeSelect = document.getElementById('catalog-size-format');
            const materialSelect = document.getElementById('catalog-material-substrate');
            const densitySelect = document.getElementById('catalog-paper-density');
            const finishSelect = document.getElementById('catalog-finish-lamination');
            const deliverySelect = document.getElementById('catalog-delivery-method');
            const saveAndSendButton = document.getElementById('invoice-save-send-button');
            const lineItemsBody = document.getElementById('invoice-line-items');
            const addLineItemButton = document.getElementById('add-invoice-line-item');
            const productOptionCatalog = @json($productOptionCatalog);
            const previousOptionSelections = {
                size_format: @json(old('size_format')),
                material_substrate: @json(old('material_substrate')),
                paper_density: @json(old('paper_density')),
                finish_lamination: @json(old('finish_lamination')),
                delivery_method: @json(old('delivery_method')),
            };

            const formatter = new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const parseAmount = (value) => {
                const parsed = Number.parseFloat(value ?? '');
                return Number.isFinite(parsed) ? Math.max(0, parsed) : 0;
            };

            const lineItemRows = () => Array.from(lineItemsBody?.querySelectorAll('[data-line-item-row]') ?? []);

            const renumberLineItems = () => {
                lineItemRows().forEach((row, index) => {
                    const descriptionInput = row.querySelector('[data-line-item-description]');
                    const sourceInput = row.querySelector('[data-line-item-source]');
                    const quantityInput = row.querySelector('[data-line-item-quantity]');
                    const rateInput = row.querySelector('[data-line-item-rate]');

                    if (descriptionInput) {
                        descriptionInput.name = `line_items[${index}][description]`;
                    }

                    if (sourceInput) {
                        sourceInput.name = `line_items[${index}][source_type]`;
                    }

                    if (quantityInput) {
                        quantityInput.name = `line_items[${index}][quantity]`;
                    }

                    if (rateInput) {
                        rateInput.name = `line_items[${index}][rate]`;
                    }
                });
            };

            const lineItemsState = () => {
                const items = lineItemRows().map((row) => {
                    const description = String(row.querySelector('[data-line-item-description]')?.value ?? '').trim();
                    const quantity = Math.max(1, parseInt(row.querySelector('[data-line-item-quantity]')?.value ?? '1', 10) || 1);
                    const rate = parseAmount(row.querySelector('[data-line-item-rate]')?.value);
                    const amount = quantity * rate;
                    const amountCell = row.querySelector('[data-line-item-amount]');

                    if (amountCell) {
                        amountCell.textContent = `₦${formatter.format(amount)}`;
                    }

                    return {
                        hasValue: description !== '',
                        quantity,
                        rate,
                        amount,
                    };
                });

                const explicitItems = items.filter((item) => item.hasValue);

                return {
                    hasExplicitItems: explicitItems.length > 0,
                    quantity: explicitItems.reduce((sum, item) => sum + item.quantity, 0),
                    subtotal: explicitItems.reduce((sum, item) => sum + item.amount, 0),
                };
            };

            const setNewCustomerFormVisible = (visible) => {
                if (!newCustomerForm || !toggleNewCustomerButton) {
                    return;
                }

                newCustomerForm.classList.toggle('hidden', !visible);
                toggleNewCustomerButton.setAttribute('aria-expanded', visible ? 'true' : 'false');
                toggleNewCustomerButton.textContent = visible ? 'Hide New Customer Form' : 'Add New Customer';
            };

            const selectedCatalogOption = () => catalogSelect?.selectedOptions?.[0] ?? null;

            const selectedProductId = () => {
                const selectedValue = selectedCatalogOption()?.value ?? '';
                if (!selectedValue.startsWith('product:')) {
                    return null;
                }

                return selectedValue.slice('product:'.length);
            };

            const activeProductOptionPayload = () => {
                const productId = selectedProductId();

                if (!productId) {
                    return null;
                }

                return productOptionCatalog?.[productId] ?? null;
            };

            const optionPriceFor = (options, label) => {
                if (!Array.isArray(options) || !label) {
                    return 0;
                }

                const match = options.find((option) => String(option?.label ?? '') === String(label));
                return parseAmount(match?.price);
            };

            const fillSelectOptions = (select, options, selectedValue) => {
                if (!select) {
                    return;
                }

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '— Auto / Default —';

                select.replaceChildren(placeholder);

                const normalizedOptions = Array.isArray(options) ? options : [];

                normalizedOptions.forEach((option) => {
                    const label = String(option?.label ?? '').trim();
                    if (!label) {
                        return;
                    }

                    const optionElement = document.createElement('option');
                    const price = parseAmount(option?.price);
                    optionElement.value = label;
                    optionElement.textContent = price > 0
                        ? `${label} (+ ₦${formatter.format(price)})`
                        : label;
                    select.appendChild(optionElement);
                });

                if (selectedValue && Array.from(select.options).some((option) => option.value === selectedValue)) {
                    select.value = selectedValue;
                } else {
                    select.value = '';
                }
            };

            const populateProductOptionSelectors = () => {
                const payload = activeProductOptionPayload();
                const isProduct = Boolean(payload);
                const selectors = [sizeSelect, materialSelect, densitySelect, finishSelect, deliverySelect];
                selectors.forEach((select) => {
                    if (!select) {
                        return;
                    }
                    select.disabled = !isProduct;
                });

                if (!isProduct) {
                    fillSelectOptions(sizeSelect, [], null);
                    fillSelectOptions(materialSelect, [], null);
                    fillSelectOptions(densitySelect, [], null);
                    fillSelectOptions(finishSelect, [], null);
                    fillSelectOptions(deliverySelect, [], null);
                    return;
                }

                fillSelectOptions(sizeSelect, payload.options?.size_format, previousOptionSelections.size_format ?? payload.defaults?.size_format ?? '');
                fillSelectOptions(materialSelect, payload.options?.material_substrate, previousOptionSelections.material_substrate ?? payload.defaults?.material_substrate ?? '');
                fillSelectOptions(densitySelect, payload.options?.paper_density, previousOptionSelections.paper_density ?? payload.defaults?.paper_density ?? '');
                fillSelectOptions(finishSelect, payload.options?.finish_lamination, previousOptionSelections.finish_lamination ?? payload.defaults?.finish_lamination ?? '');
                fillSelectOptions(deliverySelect, payload.options?.delivery_method, previousOptionSelections.delivery_method ?? payload.defaults?.delivery_method ?? '');

                previousOptionSelections.size_format = null;
                previousOptionSelections.material_substrate = null;
                previousOptionSelections.paper_density = null;
                previousOptionSelections.finish_lamination = null;
                previousOptionSelections.delivery_method = null;
            };

            const updateCatalogPrice = () => {
                const option = selectedCatalogOption();
                const baseUnitPrice = parseAmount(option?.getAttribute('data-unit-price'));
                const itemName = option?.getAttribute('data-item-name') || '—';
                const quantity = Math.max(1, parseInt(quantityInput?.value ?? '1', 10) || 1);
                const tax = parseAmount(taxInput?.value);
                const discount = parseAmount(discountInput?.value);
                const payload = activeProductOptionPayload();
                const sizePrice = optionPriceFor(payload?.options?.size_format, sizeSelect?.value || payload?.defaults?.size_format);
                const materialPrice = optionPriceFor(payload?.options?.material_substrate, materialSelect?.value || payload?.defaults?.material_substrate);
                const densityPrice = optionPriceFor(payload?.options?.paper_density, densitySelect?.value || payload?.defaults?.paper_density);
                const finishPrice = optionPriceFor(payload?.options?.finish_lamination, finishSelect?.value || payload?.defaults?.finish_lamination);
                const deliveryPrice = optionPriceFor(payload?.options?.delivery_method, deliverySelect?.value || payload?.defaults?.delivery_method);
                const effectiveUnitPrice = baseUnitPrice + sizePrice + materialPrice + densityPrice + finishPrice;
                const subtotal = (quantity * effectiveUnitPrice) + deliveryPrice;
                const lineItems = lineItemsState();
                const activeSubtotal = lineItems.hasExplicitItems ? lineItems.subtotal : subtotal;
                const total = Math.max(0, activeSubtotal + tax - discount);
                const adjustments = tax - discount;

                if (unitPriceInput) {
                    unitPriceInput.value = effectiveUnitPrice.toFixed(2);
                }

                if (unitPriceDisplay) {
                    unitPriceDisplay.value = formatter.format(effectiveUnitPrice);
                }

                if (itemNameDisplay) {
                    itemNameDisplay.textContent = deliveryPrice > 0
                        ? `${itemName} (+ Delivery ₦${formatter.format(deliveryPrice)})`
                        : itemName;
                }

                if (subtotalDisplay) {
                    subtotalDisplay.textContent = `₦${formatter.format(activeSubtotal)}`;
                }

                if (mobileAmountDisplay) {
                    mobileAmountDisplay.textContent = `₦${formatter.format(activeSubtotal)}`;
                }

                if (adjustmentsDisplay) {
                    adjustmentsDisplay.textContent = `₦${formatter.format(adjustments)}`;
                }

                if (totalDisplay) {
                    totalDisplay.textContent = `₦${formatter.format(total)}`;
                }
            };

            const hydrateFromOption = (option) => {
                if (!option) {
                    return;
                }

                nameInput.value = option.getAttribute('data-customer-name') ?? nameInput.value;
                emailInput.value = option.getAttribute('data-customer-email') ?? emailInput.value;
                phoneInput.value = option.getAttribute('data-customer-phone') ?? phoneInput.value;
                syncSendActionAvailability();
            };

            const syncSendActionAvailability = () => {
                if (!saveAndSendButton) {
                    return;
                }

                const hasEmail = (emailInput?.value ?? '').trim() !== '';
                saveAndSendButton.hidden = !hasEmail;
                saveAndSendButton.disabled = !hasEmail;
                saveAndSendButton.title = hasEmail ? '' : 'Customer email is required to send invoice';
            };

            customerSelect?.addEventListener('change', (event) => {
                hydrateFromOption(event.target.selectedOptions?.[0]);
            });

            toggleNewCustomerButton?.addEventListener('click', () => {
                setNewCustomerFormVisible(newCustomerForm?.classList.contains('hidden'));
            });

            emailInput?.addEventListener('input', syncSendActionAvailability);

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
                setNewCustomerFormVisible(false);
            });

            catalogSelect?.addEventListener('change', updateCatalogPrice);
            quantityInput?.addEventListener('input', updateCatalogPrice);
            taxInput?.addEventListener('input', updateCatalogPrice);
            discountInput?.addEventListener('input', updateCatalogPrice);
            sizeSelect?.addEventListener('change', updateCatalogPrice);
            materialSelect?.addEventListener('change', updateCatalogPrice);
            densitySelect?.addEventListener('change', updateCatalogPrice);
            finishSelect?.addEventListener('change', updateCatalogPrice);
            deliverySelect?.addEventListener('change', updateCatalogPrice);

            catalogSelect?.addEventListener('change', populateProductOptionSelectors);

            addLineItemButton?.addEventListener('click', () => {
                if (!lineItemsBody) {
                    return;
                }

                const row = document.createElement('tr');
                row.setAttribute('data-line-item-row', '');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <input data-line-item-description class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800" placeholder="Item description">
                        <input type="hidden" value="custom" data-line-item-source>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" min="1" step="1" value="1" data-line-item-quantity class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" min="0" step="0.01" value="0" data-line-item-rate class="w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800">
                    </td>
                    <td class="px-4 py-3 font-black text-slate-900" data-line-item-amount>₦0.00</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" data-remove-line-item class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black uppercase tracking-wide text-slate-500 hover:border-red-200 hover:text-red-600">Remove</button>
                    </td>
                `;
                lineItemsBody.appendChild(row);
                renumberLineItems();
                updateCatalogPrice();
            });

            lineItemsBody?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-line-item]');
                if (!button) {
                    return;
                }

                const row = button.closest('[data-line-item-row]');
                if (!row) {
                    return;
                }

                const rows = lineItemRows();
                if (rows.length === 1) {
                    const descriptionInput = row.querySelector('[data-line-item-description]');
                    const quantityInput = row.querySelector('[data-line-item-quantity]');
                    const rateInput = row.querySelector('[data-line-item-rate]');

                    if (descriptionInput) {
                        descriptionInput.value = '';
                    }
                    if (quantityInput) {
                        quantityInput.value = '1';
                    }
                    if (rateInput) {
                        rateInput.value = '0';
                    }
                } else {
                    row.remove();
                }

                renumberLineItems();
                updateCatalogPrice();
            });

            lineItemsBody?.addEventListener('input', () => {
                updateCatalogPrice();
            });

            setNewCustomerFormVisible(false);
            populateProductOptionSelectors();
            renumberLineItems();
            updateCatalogPrice();
            syncSendActionAvailability();
        })();
    </script>
@endsection
