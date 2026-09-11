@extends('layouts.admin')

@section('title', 'Create Quotation | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
                <h1 class="pb-page-title">Create quotation</h1>
                <p class="pb-page-subtitle">Generate a professional quote for new or existing customers. Choose to save, download, or send after save.</p>
            </div>
        </div>

        <!-- Error Summary -->
        @if ($errors->any())
            <div class="pb-alert pb-alert-error items-start">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold">Please review the following issues:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <form action="{{ route('admin.invoices.quotations.store') }}" method="POST" class="fade-in-up section-delay-1 space-y-6">
            @csrf
            @php
                $lineItems = collect(old('line_items', [[
                    'source_type' => 'custom',
                    'catalog_item_key' => '',
                    'description' => '',
                    'size' => '',
                    'color' => '',
                    'finishing' => '',
                    'quantity' => old('quantity', 1),
                    'rate' => old('unit_price', 0),
                ]]))
                    ->filter(fn ($item) => is_array($item))
                    ->map(function (array $item): array {
                        $sourceType = strtolower((string) ($item['source_type'] ?? 'custom'));

                        if (! in_array($sourceType, ['custom', 'product', 'service'], true)) {
                            $sourceType = 'custom';
                        }

                        return [
                            'source_type' => $sourceType,
                            'catalog_item_key' => (string) ($item['catalog_item_key'] ?? ''),
                            'description' => (string) ($item['description'] ?? ''),
                            'size' => (string) ($item['size'] ?? ''),
                            'color' => (string) ($item['color'] ?? ''),
                            'finishing' => (string) ($item['finishing'] ?? ''),
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'rate' => (float) ($item['rate'] ?? 0),
                        ];
                    })
                    ->values();

                if ($lineItems->isEmpty()) {
                    $lineItems = collect([[
                        'source_type' => 'custom',
                        'catalog_item_key' => '',
                        'description' => '',
                        'size' => '',
                        'color' => '',
                        'finishing' => '',
                        'quantity' => 1,
                        'rate' => 0,
                    ]]);
                }
            @endphp
            
            <!-- Customer Section -->
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Customer Information</h2>
                    <p class="pb-section-subtitle">Select existing or create new customer</p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="pb-label">Existing Customer</label>
                        <select id="quotation-customer-select" name="customer_id" class="mt-2 pb-input">
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
                            id="quotation-toggle-new-customer"
                            class="pb-btn pb-btn-sm pb-btn-outline"
                            aria-expanded="false"
                            aria-controls="quotation-new-customer-form"
                        >
                            Add New Customer
                        </button>
                    </div>

                    <div id="quotation-new-customer-form" class="hidden rounded-xl border border-cyan-100 bg-cyan-50/30 p-4">
                        <livewire:admin.customer-quick-create />
                    </div>
                </div>
            </div>

            <!-- Customer Details Grid -->
            <div class="pb-card p-6 lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Customer Name *</label>
                        <input id="quotation-customer-name" name="customer_name" value="{{ old('customer_name') }}" required
                               class="pb-input"
                               placeholder="Full name">
                        @error('customer_name')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Customer Email</label>
                        <input id="quotation-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}"
                               class="pb-input"
                               placeholder="email@example.com">
                        <p class="text-xs font-semibold text-slate-500">Optional. Required only when using "Save & Send".</p>
                        @error('customer_email')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Customer Phone *</label>
                        <input id="quotation-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required
                               class="pb-input"
                               placeholder="+234 XXX XXX XXXX">
                        @error('customer_phone')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Product</label>
                        <select name="product_id" class="pb-input">
                            <option value="">— Custom job —</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Job Details Section -->
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Job Specifications</h2>
                    <p class="pb-section-subtitle">Define the job details and pricing</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Job Type *</label>
                        <select name="job_type" required class="pb-input">
                            <option value="">— Select job type —</option>
                            @foreach ($jobTypes as $jobType)
                                <option @selected(old('job_type') === $jobType)>{{ $jobType }}</option>
                            @endforeach
                        </select>
                        @error('job_type')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Size / Format</label>
                        <select name="size_format" class="pb-input">
                            <option value="">— Select size —</option>
                            @foreach ($sizes as $size)
                                <option @selected(old('size_format') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Tax (₦)</label>
                        <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}"
                               class="pb-input"
                               placeholder="0.00">
                        @error('tax_amount')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Discount (₦)</label>
                        <input type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', 0) }}"
                               class="pb-input"
                               placeholder="0.00">
                        @error('discount_amount')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <input id="quote-aggregate-quantity" type="hidden" name="quantity" value="{{ old('quantity', 1) }}">
                <input id="quote-aggregate-unit-price" type="hidden" name="unit_price" value="{{ old('unit_price', 0) }}">

                <datalist id="quote-size-suggestions">
                    @foreach ($sizes as $size)
                        <option value="{{ $size }}"></option>
                    @endforeach
                </datalist>
                <datalist id="quote-finishing-suggestions">
                    @foreach ($finishes as $finish)
                        <option value="{{ $finish }}"></option>
                    @endforeach
                </datalist>

                <div class="quotation-line-items-wrap mt-6 overflow-hidden rounded-xl border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="quotation-line-items-table w-full min-w-[980px]">
                            <thead class="bg-slate-900 text-left text-xs font-bold uppercase tracking-wide text-white">
                                <tr>
                                    <th class="px-4 py-3">Item</th>
                                    <th class="px-4 py-3">Quantity</th>
                                    <th class="px-4 py-3">Rate</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="quotation-line-items" class="divide-y divide-slate-200 bg-white">
                                @foreach ($lineItems as $index => $item)
                                    <tr data-line-item-row class="quotation-line-item-row">
                                        <td class="line-item-cell line-item-cell--item px-4 py-3">
                                            <p class="line-item-mobile-label">Item</p>
                                            <div class="space-y-2.5">
                                                <div class="grid gap-2 sm:grid-cols-2">
                                                    <div>
                                                        <p class="line-item-sub-label">Type</p>
                                                        <select data-line-item-source name="line_items[{{ $index }}][source_type]" class="pb-input">
                                                            <option value="custom" @selected(($item['source_type'] ?? 'custom') === 'custom')>Custom</option>
                                                            <option value="product" @selected(($item['source_type'] ?? '') === 'product')>Existing Product</option>
                                                            <option value="service" @selected(($item['source_type'] ?? '') === 'service')>Existing Service</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <p class="line-item-sub-label">Catalog Item</p>
                                                        <select data-line-item-catalog name="line_items[{{ $index }}][catalog_item_key]" class="pb-input">
                                                            <option value="">— Select catalog item —</option>
                                                            <optgroup label="Products">
                                                                @foreach ($products as $product)
                                                                    <option
                                                                        value="product:{{ $product->id }}"
                                                                        data-source-type="product"
                                                                        data-item-name="{{ $product->name }}"
                                                                        data-unit-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                                                        @selected(($item['catalog_item_key'] ?? '') === 'product:'.$product->id)
                                                                    >
                                                                        {{ $product->name }} · ₦{{ number_format((float) $product->price, 2) }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="Services">
                                                                @foreach ($services as $service)
                                                                    <option
                                                                        value="{{ $service['key'] }}"
                                                                        data-source-type="service"
                                                                        data-item-name="{{ $service['name'] }}"
                                                                        data-unit-price="{{ number_format((float) $service['price'], 2, '.', '') }}"
                                                                        @selected(($item['catalog_item_key'] ?? '') === $service['key'])
                                                                    >
                                                                        {{ $service['name'] }} · ₦{{ number_format((float) $service['price'], 2) }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input data-line-item-description name="line_items[{{ $index }}][description]" value="{{ (string) ($item['description'] ?? '') }}"
                                                       class="pb-input"
                                                       placeholder="Description of item/service...">
                                                <div class="grid gap-2 sm:grid-cols-3">
                                                    <input data-line-item-size list="quote-size-suggestions" name="line_items[{{ $index }}][size]" value="{{ (string) ($item['size'] ?? '') }}"
                                                           class="pb-input"
                                                           placeholder="Size / Format">
                                                    <input data-line-item-color name="line_items[{{ $index }}][color]" value="{{ (string) ($item['color'] ?? '') }}"
                                                           class="pb-input"
                                                           placeholder="Color">
                                                    <input data-line-item-finishing list="quote-finishing-suggestions" name="line_items[{{ $index }}][finishing]" value="{{ (string) ($item['finishing'] ?? '') }}"
                                                           class="pb-input"
                                                           placeholder="Finishing">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="line-item-cell line-item-cell--quantity px-4 py-3">
                                            <p class="line-item-mobile-label">Quantity</p>
                                            <input data-line-item-quantity type="number" min="1" step="1" name="line_items[{{ $index }}][quantity]" value="{{ (int) ($item['quantity'] ?? 1) }}"
                                                   class="pb-input w-24">
                                        </td>
                                        <td class="line-item-cell line-item-cell--rate px-4 py-3">
                                            <p class="line-item-mobile-label">Rate</p>
                                            <div class="flex w-32 items-center overflow-hidden rounded-lg border border-slate-200">
                                                <span class="px-3 text-sm font-bold text-slate-500">₦</span>
                                                <input data-line-item-rate type="number" min="0" step="0.01" name="line_items[{{ $index }}][rate]" value="{{ (float) ($item['rate'] ?? 0) }}"
                                                       class="w-full border-0 bg-transparent px-3 py-2.5 text-sm font-semibold text-slate-800 focus:ring-0">
                                            </div>
                                        </td>
                                        <td class="line-item-cell line-item-cell--amount px-4 py-3 text-sm font-bold text-slate-900">
                                            <p class="line-item-mobile-label">Amount</p>
                                            <span data-line-item-amount>₦0.00</span>
                                        </td>
                                        <td class="line-item-cell line-item-cell--action px-4 py-3 text-right">
                                            <button type="button" data-remove-line-item class="pb-btn pb-btn-sm pb-btn-outline">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @error('line_items')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.catalog_item_key')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.description')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.quantity')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror
                @error('line_items.*.rate')<p class="mt-2 text-xs font-bold text-pink-700">{{ $message }}</p>@enderror

                <button type="button" id="add-quotation-line-item" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 transition-colors hover:bg-emerald-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Line Item
                </button>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Subtotal</p>
                        <p id="quotation-subtotal-display" class="mt-2 text-lg font-bold text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Tax</p>
                        <p id="quotation-tax-display" class="mt-2 text-lg font-bold text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Discount</p>
                        <p id="quotation-discount-display" class="mt-2 text-lg font-bold text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-lg border border-pink-200 bg-pink-50 p-4">
                        <p class="pb-label text-pink-700">Grand Total</p>
                        <p id="quotation-total-display" class="mt-2 text-lg font-bold text-pink-700">₦0.00</p>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="pb-card p-6 lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Due Date
                        </label>
                        <input type="datetime-local" name="due_at" value="{{ old('due_at', now()->addDays(7)->format('Y-m-d\\TH:i')) }}" 
                               class="pb-input">
                        @error('due_at')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Quotation Status
                        </label>
                        <select name="invoice_status" class="pb-input">
                            <option value="draft" @selected(old('invoice_status', 'draft') === 'draft')>Draft</option>
                            <option value="unpaid" @selected(old('invoice_status') === 'unpaid')>Unpaid</option>
                            <option value="paid" @selected(old('invoice_status') === 'paid')>Paid</option>
                            <option value="disputed" @selected(old('invoice_status') === 'disputed')>Disputed</option>
                        </select>
                        <p class="text-xs font-semibold text-slate-500">Set to Paid to mark this quotation as settled immediately.</p>
                        @error('invoice_status')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Delivery City
                        </label>
                        <input name="delivery_city" value="{{ old('delivery_city') }}" 
                               class="pb-input"
                               placeholder="e.g., Lagos">
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Delivery Address
                        </label>
                        <input name="delivery_address" value="{{ old('delivery_address') }}" 
                               class="pb-input"
                               placeholder="Full delivery address">
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Artwork / Brief Notes
                        </label>
                        <textarea name="artwork_notes" rows="4" data-rich-editor
                                  class="pb-textarea"
                                  placeholder="Describe artwork requirements, special instructions, etc.">{{ old('artwork_notes') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Internal Notes
                        </label>
                        <textarea name="internal_notes" rows="4" data-rich-editor
                                  class="pb-textarea bg-slate-50"
                                  placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Save Options -->
            <div class="pb-card p-6 lg:p-8">
                <p class="text-sm font-bold text-slate-900">Save Options</p>
                <p class="mt-1 text-xs text-slate-500">Quotations without an email can only be saved or downloaded.</p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="submit" name="action" value="save" class="pb-btn pb-btn-md pb-btn-ink">
                        Save Quotation
                    </button>
                    <button type="submit" name="action" value="save_download" class="pb-btn pb-btn-md pb-btn-outline">
                        Save & Download
                    </button>
                    <button type="submit" name="action" value="save_send" id="quotation-save-send-button" class="pb-btn pb-btn-md pb-btn-primary">
                        Save & Send
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <template id="quotation-line-item-template">
        <tr data-line-item-row class="quotation-line-item-row">
            <td class="line-item-cell line-item-cell--item px-4 py-3">
                <p class="line-item-mobile-label">Item</p>
                <div class="space-y-2.5">
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div>
                            <p class="line-item-sub-label">Type</p>
                            <select data-line-item-source class="pb-input">
                                <option value="custom" selected>Custom</option>
                                <option value="product">Existing Product</option>
                                <option value="service">Existing Service</option>
                            </select>
                        </div>
                        <div>
                            <p class="line-item-sub-label">Catalog Item</p>
                            <select data-line-item-catalog class="pb-input">
                                <option value="">— Select catalog item —</option>
                                <optgroup label="Products">
                                    @foreach ($products as $product)
                                        <option
                                            value="product:{{ $product->id }}"
                                            data-source-type="product"
                                            data-item-name="{{ $product->name }}"
                                            data-unit-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                        >
                                            {{ $product->name }} · ₦{{ number_format((float) $product->price, 2) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Services">
                                    @foreach ($services as $service)
                                        <option
                                            value="{{ $service['key'] }}"
                                            data-source-type="service"
                                            data-item-name="{{ $service['name'] }}"
                                            data-unit-price="{{ number_format((float) $service['price'], 2, '.', '') }}"
                                        >
                                            {{ $service['name'] }} · ₦{{ number_format((float) $service['price'], 2) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <input data-line-item-description
                           class="pb-input"
                           placeholder="Description of item/service...">
                    <div class="grid gap-2 sm:grid-cols-3">
                        <input data-line-item-size list="quote-size-suggestions"
                               class="pb-input"
                               placeholder="Size / Format">
                        <input data-line-item-color
                               class="pb-input"
                               placeholder="Color">
                        <input data-line-item-finishing list="quote-finishing-suggestions"
                               class="pb-input"
                               placeholder="Finishing">
                    </div>
                </div>
            </td>
            <td class="line-item-cell line-item-cell--quantity px-4 py-3">
                <p class="line-item-mobile-label">Quantity</p>
                <input data-line-item-quantity type="number" min="1" step="1" value="1"
                       class="pb-input w-24">
            </td>
            <td class="line-item-cell line-item-cell--rate px-4 py-3">
                <p class="line-item-mobile-label">Rate</p>
                <div class="flex w-32 items-center overflow-hidden rounded-lg border border-slate-200">
                    <span class="px-3 text-sm font-bold text-slate-500">₦</span>
                    <input data-line-item-rate type="number" min="0" step="0.01" value="0"
                           class="w-full border-0 bg-transparent px-3 py-2.5 text-sm font-semibold text-slate-800 focus:ring-0">
                </div>
            </td>
            <td class="line-item-cell line-item-cell--amount px-4 py-3 text-sm font-bold text-slate-900">
                <p class="line-item-mobile-label">Amount</p>
                <span data-line-item-amount>₦0.00</span>
            </td>
            <td class="line-item-cell line-item-cell--action px-4 py-3 text-right">
                <button type="button" data-remove-line-item class="pb-btn pb-btn-sm pb-btn-outline">
                    Remove
                </button>
            </td>
        </tr>
    </template>

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

        .line-item-mobile-label {
            display: none;
            margin-bottom: 0.35rem;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #475569;
        }

        .line-item-sub-label {
            margin-bottom: 0.2rem;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }

        @media (max-width: 640px) {
            .quotation-line-items-wrap {
                overflow: visible;
                border: 0;
                border-radius: 0;
            }

            .quotation-line-items-table {
                min-width: 100%;
            }

            .quotation-line-items-table thead {
                display: none;
            }

            .quotation-line-items-table,
            .quotation-line-items-table tbody,
            .quotation-line-items-table tr,
            .quotation-line-items-table td {
                display: block;
                width: 100%;
            }

            .quotation-line-items-table tbody {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
                background: transparent;
            }

            .quotation-line-items-table .quotation-line-item-row {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-template-areas:
                    "item item"
                    "quantity rate"
                    "amount amount"
                    "action action";
                gap: 0.75rem;
                border: 1px solid #cbd5e1;
                border-radius: 0.9rem;
                background: #ffffff;
                padding: 0.85rem;
            }

            .quotation-line-items-table .line-item-cell {
                padding: 0;
                border: 0;
            }

            .quotation-line-items-table .line-item-cell--item { grid-area: item; }
            .quotation-line-items-table .line-item-cell--quantity { grid-area: quantity; }
            .quotation-line-items-table .line-item-cell--rate { grid-area: rate; }
            .quotation-line-items-table .line-item-cell--amount { grid-area: amount; }
            .quotation-line-items-table .line-item-cell--action { grid-area: action; text-align: left; }

            .quotation-line-items-table [data-line-item-quantity] {
                width: 100%;
            }

            .quotation-line-items-table .line-item-cell--rate > div {
                width: 100%;
            }

            .quotation-line-items-table [data-remove-line-item] {
                width: 100%;
                justify-content: center;
            }

            .line-item-mobile-label {
                display: block;
            }
        }
    </style>

    <script>
        (() => {
            const customerSelect = document.getElementById('quotation-customer-select');
            const nameInput = document.getElementById('quotation-customer-name');
            const emailInput = document.getElementById('quotation-customer-email');
            const phoneInput = document.getElementById('quotation-customer-phone');
            const toggleNewCustomerButton = document.getElementById('quotation-toggle-new-customer');
            const newCustomerForm = document.getElementById('quotation-new-customer-form');
            const lineItemsBody = document.getElementById('quotation-line-items');
            const lineItemTemplate = document.getElementById('quotation-line-item-template');
            const addLineItemButton = document.getElementById('add-quotation-line-item');
            const taxInput = document.querySelector('input[name="tax_amount"]');
            const discountInput = document.querySelector('input[name="discount_amount"]');
            const saveAndSendButton = document.getElementById('quotation-save-send-button');
            const subtotalDisplay = document.getElementById('quotation-subtotal-display');
            const taxDisplay = document.getElementById('quotation-tax-display');
            const discountDisplay = document.getElementById('quotation-discount-display');
            const totalDisplay = document.getElementById('quotation-total-display');
            const aggregateQuantityInput = document.getElementById('quote-aggregate-quantity');
            const aggregateUnitPriceInput = document.getElementById('quote-aggregate-unit-price');

            const formatter = new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const parseAmount = (value) => {
                const parsed = Number.parseFloat(value ?? '');
                return Number.isFinite(parsed) ? Math.max(0, parsed) : 0;
            };

            const formatCurrency = (value) => `₦${formatter.format(value)}`;
            const lineItemRows = () => Array.from(lineItemsBody?.querySelectorAll('[data-line-item-row]') ?? []);

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
                saveAndSendButton.title = hasEmail ? '' : 'Customer email is required to send quotation';
            };

            const setNewCustomerFormVisible = (visible) => {
                if (!newCustomerForm || !toggleNewCustomerButton) {
                    return;
                }

                newCustomerForm.classList.toggle('hidden', !visible);
                toggleNewCustomerButton.setAttribute('aria-expanded', visible ? 'true' : 'false');
                toggleNewCustomerButton.textContent = visible ? 'Hide New Customer Form' : 'Add New Customer';
            };

            const renumberLineItemInputs = () => {
                lineItemRows().forEach((row, index) => {
                    const sourceTypeInput = row.querySelector('[data-line-item-source]');
                    const catalogItemInput = row.querySelector('[data-line-item-catalog]');
                    const descriptionInput = row.querySelector('[data-line-item-description]');
                    const sizeInput = row.querySelector('[data-line-item-size]');
                    const colorInput = row.querySelector('[data-line-item-color]');
                    const finishingInput = row.querySelector('[data-line-item-finishing]');
                    const quantityInput = row.querySelector('[data-line-item-quantity]');
                    const rateInput = row.querySelector('[data-line-item-rate]');

                    if (sourceTypeInput) {
                        sourceTypeInput.name = `line_items[${index}][source_type]`;
                    }

                    if (catalogItemInput) {
                        catalogItemInput.name = `line_items[${index}][catalog_item_key]`;
                    }

                    if (descriptionInput) {
                        descriptionInput.name = `line_items[${index}][description]`;
                    }

                    if (sizeInput) {
                        sizeInput.name = `line_items[${index}][size]`;
                    }

                    if (colorInput) {
                        colorInput.name = `line_items[${index}][color]`;
                    }

                    if (finishingInput) {
                        finishingInput.name = `line_items[${index}][finishing]`;
                    }

                    if (quantityInput) {
                        quantityInput.name = `line_items[${index}][quantity]`;
                    }

                    if (rateInput) {
                        rateInput.name = `line_items[${index}][rate]`;
                    }
                });
            };

            const syncCatalogOptionsForRow = (row) => {
                if (!(row instanceof HTMLElement)) {
                    return;
                }

                const sourceTypeInput = row.querySelector('[data-line-item-source]');
                const catalogItemInput = row.querySelector('[data-line-item-catalog]');
                const descriptionInput = row.querySelector('[data-line-item-description]');
                const rateInput = row.querySelector('[data-line-item-rate]');

                if (!sourceTypeInput || !catalogItemInput) {
                    return;
                }

                const sourceType = ['product', 'service'].includes(sourceTypeInput.value)
                    ? sourceTypeInput.value
                    : 'custom';

                if (sourceType === 'custom') {
                    catalogItemInput.value = '';
                    catalogItemInput.disabled = true;
                    catalogItemInput.classList.add('bg-slate-100');
                    catalogItemInput.classList.add('text-slate-500');
                } else {
                    catalogItemInput.disabled = false;
                    catalogItemInput.classList.remove('bg-slate-100');
                    catalogItemInput.classList.remove('text-slate-500');
                }

                Array.from(catalogItemInput.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        option.disabled = false;
                        return;
                    }

                    const optionSourceType = option.getAttribute('data-source-type');
                    const matches = sourceType !== 'custom' && optionSourceType === sourceType;

                    option.hidden = !matches;
                    option.disabled = !matches;
                });

                if (sourceType === 'custom') {
                    return;
                }

                const selectedOption = catalogItemInput.selectedOptions?.[0];

                if (!selectedOption || selectedOption.disabled) {
                    const firstAvailable = Array.from(catalogItemInput.options).find((option) => option.value !== '' && !option.disabled);
                    catalogItemInput.value = firstAvailable?.value ?? '';
                }

                const activeOption = catalogItemInput.selectedOptions?.[0];
                const itemName = activeOption?.getAttribute('data-item-name') ?? '';
                const baseRate = parseAmount(activeOption?.getAttribute('data-unit-price'));

                if (descriptionInput && descriptionInput.value.trim() === '' && itemName !== '') {
                    descriptionInput.value = itemName;
                }

                if (rateInput && parseAmount(rateInput.value) <= 0 && baseRate > 0) {
                    rateInput.value = baseRate.toFixed(2);
                }
            };

            const updateTotals = () => {
                let subtotal = 0;
                let totalQuantity = 0;

                lineItemRows().forEach((row) => {
                    const quantityInput = row.querySelector('[data-line-item-quantity]');
                    const rateInput = row.querySelector('[data-line-item-rate]');
                    const amountDisplay = row.querySelector('[data-line-item-amount]');
                    const quantity = Math.max(1, parseInt(quantityInput?.value ?? '1', 10) || 1);
                    const rate = parseAmount(rateInput?.value);
                    const amount = quantity * rate;

                    totalQuantity += quantity;
                    subtotal += amount;

                    if (amountDisplay) {
                        amountDisplay.textContent = formatCurrency(amount);
                    }
                });

                const tax = parseAmount(taxInput?.value);
                const discount = parseAmount(discountInput?.value);
                const total = Math.max(0, subtotal + tax - discount);
                const aggregateQuantity = Math.max(1, totalQuantity);
                const aggregateUnitPrice = aggregateQuantity > 0 ? subtotal / aggregateQuantity : 0;

                if (subtotalDisplay) {
                    subtotalDisplay.textContent = formatCurrency(subtotal);
                }

                if (taxDisplay) {
                    taxDisplay.textContent = formatCurrency(tax);
                }

                if (discountDisplay) {
                    discountDisplay.textContent = formatCurrency(discount);
                }

                if (totalDisplay) {
                    totalDisplay.textContent = formatCurrency(total);
                }

                if (aggregateQuantityInput) {
                    aggregateQuantityInput.value = String(aggregateQuantity);
                }

                if (aggregateUnitPriceInput) {
                    aggregateUnitPriceInput.value = aggregateUnitPrice.toFixed(2);
                }
            };

            const appendLineItem = (item = {}) => {
                if (!lineItemsBody || !lineItemTemplate) {
                    return;
                }

                const row = lineItemTemplate.content.firstElementChild?.cloneNode(true);

                if (!(row instanceof HTMLElement)) {
                    return;
                }

                const sourceTypeInput = row.querySelector('[data-line-item-source]');
                const catalogItemInput = row.querySelector('[data-line-item-catalog]');
                const descriptionInput = row.querySelector('[data-line-item-description]');
                const sizeInput = row.querySelector('[data-line-item-size]');
                const colorInput = row.querySelector('[data-line-item-color]');
                const finishingInput = row.querySelector('[data-line-item-finishing]');
                const quantityInput = row.querySelector('[data-line-item-quantity]');
                const rateInput = row.querySelector('[data-line-item-rate]');

                if (sourceTypeInput) {
                    const sourceType = ['custom', 'product', 'service'].includes(String(item.source_type))
                        ? String(item.source_type)
                        : 'custom';
                    sourceTypeInput.value = sourceType;
                }

                if (catalogItemInput) {
                    catalogItemInput.value = String(item.catalog_item_key ?? '');
                }

                if (descriptionInput) {
                    descriptionInput.value = String(item.description ?? '');
                }

                if (sizeInput) {
                    sizeInput.value = String(item.size ?? '');
                }

                if (colorInput) {
                    colorInput.value = String(item.color ?? '');
                }

                if (finishingInput) {
                    finishingInput.value = String(item.finishing ?? '');
                }

                if (quantityInput) {
                    quantityInput.value = String(item.quantity ?? 1);
                }

                if (rateInput) {
                    rateInput.value = String(item.rate ?? 0);
                }

                lineItemsBody.appendChild(row);
                syncCatalogOptionsForRow(row);
                renumberLineItemInputs();
                updateTotals();
            };

            customerSelect?.addEventListener('change', (event) => {
                const option = event.target.selectedOptions?.[0];
                hydrateFromOption(option);
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

            addLineItemButton?.addEventListener('click', () => {
                appendLineItem();
            });

            lineItemsBody?.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-remove-line-item]');

                if (!removeButton) {
                    return;
                }

                const rows = lineItemRows();
                const row = removeButton.closest('[data-line-item-row]');

                if (!row) {
                    return;
                }

                if (rows.length === 1) {
                    const sourceTypeInput = row.querySelector('[data-line-item-source]');
                    const catalogItemInput = row.querySelector('[data-line-item-catalog]');
                    const descriptionInput = row.querySelector('[data-line-item-description]');
                    const sizeInput = row.querySelector('[data-line-item-size]');
                    const colorInput = row.querySelector('[data-line-item-color]');
                    const finishingInput = row.querySelector('[data-line-item-finishing]');
                    const quantityInput = row.querySelector('[data-line-item-quantity]');
                    const rateInput = row.querySelector('[data-line-item-rate]');

                    if (sourceTypeInput) {
                        sourceTypeInput.value = 'custom';
                    }

                    if (catalogItemInput) {
                        catalogItemInput.value = '';
                    }

                    if (descriptionInput) {
                        descriptionInput.value = '';
                    }

                    if (sizeInput) {
                        sizeInput.value = '';
                    }

                    if (colorInput) {
                        colorInput.value = '';
                    }

                    if (finishingInput) {
                        finishingInput.value = '';
                    }

                    if (quantityInput) {
                        quantityInput.value = '1';
                    }

                    if (rateInput) {
                        rateInput.value = '0';
                    }

                    syncCatalogOptionsForRow(row);
                } else {
                    row.remove();
                }

                renumberLineItemInputs();
                updateTotals();
            });

            lineItemsBody?.addEventListener('change', (event) => {
                const row = event.target.closest('[data-line-item-row]');

                if (!(row instanceof HTMLElement)) {
                    return;
                }

                if (
                    event.target.matches('[data-line-item-source]')
                    || event.target.matches('[data-line-item-catalog]')
                ) {
                    syncCatalogOptionsForRow(row);
                }

                updateTotals();
            });

            lineItemsBody?.addEventListener('input', (event) => {
                if (!event.target.closest('[data-line-item-row]')) {
                    return;
                }

                updateTotals();
            });

            taxInput?.addEventListener('input', updateTotals);
            discountInput?.addEventListener('input', updateTotals);

            if (lineItemRows().length === 0) {
                appendLineItem();
            } else {
                lineItemRows().forEach((row) => {
                    syncCatalogOptionsForRow(row);
                });
                renumberLineItemInputs();
                updateTotals();
            }

            setNewCustomerFormVisible(false);
            syncSendActionAvailability();
        })();
    </script>
@endsection
