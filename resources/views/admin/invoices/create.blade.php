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
                        Create invoices using only Printbuka catalog products/services and send them directly to customers.
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
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">Customer Email *</label>
                        <input id="invoice-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="email@example.com">
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
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="send_email" value="1" @checked(old('send_email', false)) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                    <div>
                        <p class="text-sm font-black text-slate-900">Send invoice email immediately</p>
                        <p class="mt-1 text-xs text-slate-500">Leave unchecked to save as a draft and send it later from the invoice list.</p>
                    </div>
                </label>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="btn-primary group relative overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-8 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-pink-600/30">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Invoice
                        </span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover:translate-x-0"></div>
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
                const total = Math.max(0, subtotal + tax - discount);
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
                    subtotalDisplay.textContent = `₦${formatter.format(subtotal)}`;
                }

                if (mobileAmountDisplay) {
                    mobileAmountDisplay.textContent = `₦${formatter.format(subtotal)}`;
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

            setNewCustomerFormVisible(false);
            populateProductOptionSelectors();
            updateCatalogPrice();
        })();
    </script>
@endsection
