@extends('layouts.admin')

@section('title', 'Edit ' . $invoice->documentTypeLabel() . ' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
                <h1 class="pb-page-title">Edit {{ strtolower($invoice->documentTypeLabel()) }}</h1>
                <p class="pb-page-subtitle">Modify the invoice, its items, or customer details.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="pb-alert pb-alert-error items-start">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold">Please review the following issues:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="h-1 w-1 rounded-full bg-red-400"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $order = $invoice->order;
            $breakdown = $order->pricing_breakdown ?? [];

            // Defaults from existing data (used when no old() input)
            $defaultCatalogItemKey = old('catalog_item_key', $breakdown['catalog_item_key'] ?? '');
            $defaultQuantity = old('quantity', $breakdown['quantity'] ?? $order->quantity ?? 1);
            $defaultTax = old('tax_amount', $invoice->tax_amount ?? 0);
            $defaultDiscount = old('discount_amount', $invoice->discount_amount ?? 0);
            $defaultDueAt = old('due_at', $invoice->due_at?->format('Y-m-d\TH:i') ?? now()->addDays(7)->format('Y-m-d\TH:i'));
            $defaultStatus = old('invoice_status', $invoice->status ?? 'unpaid');
            $defaultDeliveryCity = old('delivery_city', $order->delivery_city ?? '');
            $defaultDeliveryAddress = old('delivery_address', $order->delivery_address ?? '');
            $defaultArtworkNotes = old('artwork_notes', $order->artwork_notes ?? '');
            $defaultInternalNotes = old('internal_notes', $order->internal_notes ?? '');

            // Product option defaults from pricing_breakdown or order fields
            $defaultSize = old('size_format', $breakdown['selected_options']['size_format'] ?? $order->size_format ?? '');
            $defaultMaterial = old('material_substrate', $breakdown['selected_options']['material_substrate'] ?? $order->material_substrate ?? '');
            $defaultDensity = old('paper_density', $breakdown['selected_options']['paper_density'] ?? $order->paper_density ?? '');
            $defaultFinish = old('finish_lamination', $breakdown['selected_options']['finish_lamination'] ?? $order->finish_lamination ?? '');
            $defaultDeliveryMethod = old('delivery_method', $breakdown['selected_options']['delivery_method'] ?? $order->delivery_method ?? '');

            // Line items: use old() if present, otherwise from breakdown
            $lineItems = collect(old('line_items', $breakdown['line_items'] ?? []))
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

            // Pre-load product option selections so the JS can re-select them on load
            $previousOptionSelections = [
                'size_format' => $defaultSize,
                'material_substrate' => $defaultMaterial,
                'paper_density' => $defaultDensity,
                'finish_lamination' => $defaultFinish,
                'delivery_method' => $defaultDeliveryMethod,
            ];
        @endphp

        <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST" class="fade-in-up section-delay-1 space-y-6">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="order_id" value="{{ $invoice->order_id }}">
            {{-- Customer Section (same as create) --}}
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Customer Information</h2>
                    <p class="pb-section-subtitle">Select existing or create new customer</p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="pb-label">Existing Customer</label>
                        <select id="invoice-customer-select" name="customer_id" class="mt-2 pb-input">
                            <option value="">— Select a customer —</option>
                            @foreach ($customers as $customer)
                                <option
                                    value="{{ $customer->id }}"
                                    data-customer-name="{{ $customer->displayName() }}"
                                    data-customer-email="{{ $customer->email }}"
                                    data-customer-phone="{{ $customer->phone }}"
                                    @selected((int) old('customer_id', $order->user_id) === $customer->id)
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
                            class="pb-btn pb-btn-sm pb-btn-outline"
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
                            <a href="{{ route('admin.orders.create') }}" class="pb-btn pb-btn-sm pb-btn-outline">
                                Create Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer details inline (editable) --}}
            <div class="pb-card p-6 lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Customer Name *</label>
                        <input id="invoice-customer-name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required class="pb-input" placeholder="Full name">
                        @error('customer_name')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Customer Email</label>
                        <input id="invoice-customer-email" type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" class="pb-input" placeholder="email@example.com">
                        @error('customer_email')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Customer Phone *</label>
                        <input id="invoice-customer-phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required class="pb-input" placeholder="+234 XXX XXX XXXX">
                        @error('customer_phone')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Size / Format</label>
                        <select id="catalog-size-format" name="size_format" class="pb-input">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Material / Substrate</label>
                        <select id="catalog-material-substrate" name="material_substrate" class="pb-input">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Paper Density</label>
                        <select id="catalog-paper-density" name="paper_density" class="pb-input">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Finish / Lamination</label>
                        <select id="catalog-finish-lamination" name="finish_lamination" class="pb-input">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>

                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Delivery Method</label>
                        <select id="catalog-delivery-method" name="delivery_method" class="pb-input">
                            <option value="">— Auto / Default —</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Catalog Item & Pricing (same as create) --}}
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Catalog Item & Pricing</h2>
                    <p class="pb-section-subtitle">Choose only from Printbuka products/services</p>
                </div>

                <div class="catalog-item-grid grid grid-cols-2 gap-4 sm:gap-5 sm:grid-cols-2">
                    <div class="catalog-item-field catalog-item-field--item col-span-2 pb-field">
                        <label class="pb-label">Product / Service *</label>
                        <select id="catalog-item-select" name="catalog_item_key" required class="pb-input">
                            <option value="">— Select product or service —</option>
                            <optgroup label="Products">
                                @foreach ($products as $product)
                                    <option
                                        value="product:{{ $product->id }}"
                                        data-unit-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                        data-item-name="{{ $product->name }}"
                                        @selected($defaultCatalogItemKey === 'product:'.$product->id)
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
                                        @selected($defaultCatalogItemKey === $service['key'])
                                    >
                                        {{ $service['name'] }} · ₦{{ number_format((float) $service['price'], 2) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('catalog_item_key')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field catalog-item-field--quantity pb-field">
                        <label class="pb-label">Quantity *</label>
                        <input id="catalog-quantity" type="number" min="1" name="quantity" value="{{ $defaultQuantity }}" required class="pb-input">
                        @error('quantity')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field catalog-item-field--rate pb-field">
                        <label class="pb-label">Unit Price (₦)</label>
                        <div class="flex items-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                            <span class="px-3 text-sm font-bold text-slate-500">₦</span>
                            <input id="catalog-unit-price-display" type="text" readonly class="w-full border-0 bg-transparent px-3 py-3.5 text-sm font-bold text-slate-800 focus:ring-0">
                        </div>
                        <input id="catalog-unit-price" type="hidden" value="0">
                    </div>

                    <div class="catalog-item-field catalog-item-field--amount col-span-2 pb-field sm:hidden">
                        <label class="pb-label">Amount</label>
                        <p id="catalog-mobile-amount" class="rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-xl font-bold text-slate-900">₦0.00</p>
                    </div>

                    <div class="catalog-item-field pb-field">
                        <label class="pb-label">Tax (₦)</label>
                        <input id="catalog-tax" type="number" step="0.01" min="0" name="tax_amount" value="{{ $defaultTax }}" class="pb-input" placeholder="0.00">
                        @error('tax_amount')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="catalog-item-field pb-field">
                        <label class="pb-label">Discount (₦)</label>
                        <input id="catalog-discount" type="number" step="0.01" min="0" name="discount_amount" value="{{ $defaultDiscount }}" class="pb-input" placeholder="0.00">
                        @error('discount_amount')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Item</p>
                        <p id="catalog-item-name" class="mt-2 text-sm font-bold text-slate-900">—</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Subtotal</p>
                        <p id="catalog-subtotal" class="mt-2 text-lg font-bold text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="pb-label">Adjustments</p>
                        <p id="catalog-adjustments" class="mt-2 text-lg font-bold text-slate-900">₦0.00</p>
                    </div>
                    <div class="rounded-lg border border-pink-200 bg-pink-50 p-4">
                        <p class="pb-label text-pink-700">Invoice Total</p>
                        <p id="catalog-total" class="mt-2 text-lg font-bold text-pink-700">₦0.00</p>
                    </div>
                </div>

                {{-- Line Items Table (editable) --}}
                <div class="mt-6 rounded-xl border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-bold text-slate-900">Editable Invoice Items</p>
                        <p class="text-xs text-slate-500">Add, edit or remove item rows.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="pb-table min-w-[640px]">
                            <thead class="">
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
                                            <input name="line_items[{{ $index }}][description]" value="{{ $item['description'] }}" data-line-item-description class="pb-input" placeholder="Item description">
                                            <input type="hidden" name="line_items[{{ $index }}][source_type]" value="custom" data-line-item-source>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="1" step="1" name="line_items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" data-line-item-quantity class="pb-input w-24">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" step="0.01" name="line_items[{{ $index }}][rate]" value="{{ $item['rate'] }}" data-line-item-rate class="pb-input w-32">
                                        </td>
                                        <td class="px-4 py-3 font-bold text-slate-900" data-line-item-amount>₦0.00</td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" data-remove-line-item class="pb-btn pb-btn-sm pb-btn-outline">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-3">
                        <button type="button" id="add-invoice-line-item" class="pb-btn pb-btn-sm pb-btn-secondary">Add Item Row</button>
                    </div>
                </div>
                @error('line_items')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('line_items.*.description')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('line_items.*.quantity')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('line_items.*.rate')<p class="pb-field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Additional Info --}}
            <div class="pb-card p-6 lg:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Due Date</label>
                        <input type="datetime-local" name="due_at" value="{{ $defaultDueAt }}" class="pb-input">
                        @error('due_at')<p class="pb-field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Invoice Status</label>
                        <select name="invoice_status" class="pb-input">
                            @foreach ($invoiceStatuses as $value)
                                <option value="{{ $value }}" @selected($defaultStatus === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">Delivery City</label>
                        <input name="delivery_city" value="{{ $defaultDeliveryCity }}" class="pb-input" placeholder="e.g., Lagos">
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">Delivery Address</label>
                        <input name="delivery_address" value="{{ $defaultDeliveryAddress }}" class="pb-input" placeholder="Full delivery address">
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">Artwork / Brief Notes</label>
                        <textarea name="artwork_notes" rows="4" data-rich-editor class="pb-textarea" placeholder="Describe artwork requirements, special instructions, etc.">{{ $defaultArtworkNotes }}</textarea>
                    </div>

                    <div class="sm:col-span-2 pb-field">
                        <label class="pb-label">Internal Notes</label>
                        <textarea name="internal_notes" rows="4" data-rich-editor class="pb-textarea bg-slate-50" placeholder="Private notes for staff only">{{ $defaultInternalNotes }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pb-card p-6 lg:p-8">
                <div class="flex items-center gap-4">
                    <button type="submit" class="pb-btn pb-btn-lg pb-btn-primary">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update {{ $invoice->documentTypeLabel() }}
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
        .section-delay-1 { animation-delay: 0.05s; }
        @media (max-width: 640px) {
            .catalog-item-grid { gap: 0.75rem; }
        }
    </style>

    {{-- Use the same JS as create, with the previous selections already set --}}
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
        const lineItemsBody = document.getElementById('invoice-line-items');
        const addLineItemButton = document.getElementById('add-invoice-line-item');
        const productOptionCatalog = @json($productOptionCatalog);
        const previousOptionSelections = @json($previousOptionSelections);

        const formatter = new Intl.NumberFormat('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const parseAmount = v => { const p = Number.parseFloat(v ?? ''); return Number.isFinite(p) ? Math.max(0, p) : 0; };

        const lineItemRows = () => Array.from(lineItemsBody?.querySelectorAll('[data-line-item-row]') ?? []);

        const renumberLineItems = () => {
            lineItemRows().forEach((row, index) => {
                row.querySelector('[data-line-item-description]').name = `line_items[${index}][description]`;
                row.querySelector('[data-line-item-source]').name = `line_items[${index}][source_type]`;
                row.querySelector('[data-line-item-quantity]').name = `line_items[${index}][quantity]`;
                row.querySelector('[data-line-item-rate]').name = `line_items[${index}][rate]`;
            });
        };

        const lineItemsState = () => {
            const items = lineItemRows().map(row => {
                const desc = String(row.querySelector('[data-line-item-description]')?.value ?? '').trim();
                const qty = Math.max(1, parseInt(row.querySelector('[data-line-item-quantity]')?.value ?? '1', 10) || 1);
                const rate = parseAmount(row.querySelector('[data-line-item-rate]')?.value);
                const amount = qty * rate;
                row.querySelector('[data-line-item-amount]').textContent = `₦${formatter.format(amount)}`;
                return { hasValue: desc !== '', quantity: qty, rate, amount, description: desc };
            });
            const explicit = items.filter(i => i.hasValue);
            return {
                hasExplicitItems: explicit.length > 0,
                explicitCount: explicit.length,
                quantity: explicit.reduce((s, i) => s + i.quantity, 0),
                subtotal: explicit.reduce((s, i) => s + i.amount, 0),
            };
        };

        const setNewCustomerFormVisible = (visible) => {
            newCustomerForm?.classList.toggle('hidden', !visible);
            toggleNewCustomerButton?.setAttribute('aria-expanded', visible ? 'true' : 'false');
            if (toggleNewCustomerButton) toggleNewCustomerButton.textContent = visible ? 'Hide New Customer Form' : 'Add New Customer';
        };

        const selectedCatalogOption = () => catalogSelect?.selectedOptions?.[0] ?? null;
        const selectedProductId = () => {
            const val = selectedCatalogOption()?.value ?? '';
            return val.startsWith('product:') ? val.slice('product:'.length) : null;
        };
        const activeProductOptionPayload = () => {
            const pid = selectedProductId();
            return pid ? productOptionCatalog?.[pid] ?? null : null;
        };
        const optionPriceFor = (options, label) => {
            if (!Array.isArray(options) || !label) return 0;
            const match = options.find(o => String(o?.label ?? '') === String(label));
            return parseAmount(match?.price);
        };

        const fillSelectOptions = (select, options, selectedValue) => {
            if (!select) return;
            select.innerHTML = '<option value="">— Auto / Default —</option>';
            (Array.isArray(options) ? options : []).forEach(o => {
                if (!o?.label) return;
                const el = document.createElement('option');
                el.value = o.label;
                el.textContent = o.price > 0 ? `${o.label} (+ ₦${formatter.format(o.price)})` : o.label;
                select.appendChild(el);
            });
            if (selectedValue && Array.from(select.options).some(o => o.value === selectedValue)) select.value = selectedValue;
            else select.value = '';
        };

        const populateProductOptionSelectors = () => {
            const payload = activeProductOptionPayload();
            const isProduct = Boolean(payload);
            [sizeSelect, materialSelect, densitySelect, finishSelect, deliverySelect].forEach(s => { if (s) s.disabled = !isProduct; });
            if (!isProduct) {
                [sizeSelect, materialSelect, densitySelect, finishSelect, deliverySelect].forEach(s => fillSelectOptions(s, [], null));
                return;
            }
            fillSelectOptions(sizeSelect, payload.options?.size_format, previousOptionSelections.size_format ?? payload.defaults?.size_format ?? '');
            fillSelectOptions(materialSelect, payload.options?.material_substrate, previousOptionSelections.material_substrate ?? payload.defaults?.material_substrate ?? '');
            fillSelectOptions(densitySelect, payload.options?.paper_density, previousOptionSelections.paper_density ?? payload.defaults?.paper_density ?? '');
            fillSelectOptions(finishSelect, payload.options?.finish_lamination, previousOptionSelections.finish_lamination ?? payload.defaults?.finish_lamination ?? '');
            fillSelectOptions(deliverySelect, payload.options?.delivery_method, previousOptionSelections.delivery_method ?? payload.defaults?.delivery_method ?? '');
            // Clear after first load
            Object.keys(previousOptionSelections).forEach(k => previousOptionSelections[k] = null);
        };

        const updateCatalogPrice = () => {
            const option = selectedCatalogOption();
            const basePrice = parseAmount(option?.getAttribute('data-unit-price'));
            const catalogItemName = option?.getAttribute('data-item-name') || '—';
            const quantity = Math.max(1, parseInt(quantityInput?.value ?? '1', 10) || 1);
            const tax = parseAmount(taxInput?.value);
            const discount = parseAmount(discountInput?.value);
            const payload = activeProductOptionPayload();
            const sizePrice = optionPriceFor(payload?.options?.size_format, sizeSelect?.value || payload?.defaults?.size_format);
            const materialPrice = optionPriceFor(payload?.options?.material_substrate, materialSelect?.value || payload?.defaults?.material_substrate);
            const densityPrice = optionPriceFor(payload?.options?.paper_density, densitySelect?.value || payload?.defaults?.paper_density);
            const finishPrice = optionPriceFor(payload?.options?.finish_lamination, finishSelect?.value || payload?.defaults?.finish_lamination);
            const deliveryPrice = optionPriceFor(payload?.options?.delivery_method, deliverySelect?.value || payload?.defaults?.delivery_method);
            const effectiveUnitPrice = basePrice + sizePrice + materialPrice + densityPrice + finishPrice;
            const catalogSubtotal = (quantity * effectiveUnitPrice) + deliveryPrice;
            const lineState = lineItemsState();
            const activeSubtotal = lineState.hasExplicitItems ? lineState.subtotal : catalogSubtotal;
            const total = Math.max(0, activeSubtotal + tax - discount);
            const adjustments = tax - discount;

            unitPriceInput.value = effectiveUnitPrice.toFixed(2);
            unitPriceDisplay.value = formatter.format(effectiveUnitPrice);

            // ***** UPDATED ITEM NAME LOGIC *****
            if (itemNameDisplay) {
                if (lineState.hasExplicitItems) {
                    itemNameDisplay.textContent = `Custom items (${lineState.explicitCount})`;
                } else if (option) {
                    itemNameDisplay.textContent = deliveryPrice > 0
                        ? `${catalogItemName} (+ Delivery ₦${formatter.format(deliveryPrice)})`
                        : catalogItemName;
                } else {
                    itemNameDisplay.textContent = '—';
                }
            }

            subtotalDisplay.textContent = `₦${formatter.format(activeSubtotal)}`;
            if (mobileAmountDisplay) mobileAmountDisplay.textContent = `₦${formatter.format(activeSubtotal)}`;
            adjustmentsDisplay.textContent = `₦${formatter.format(adjustments)}`;
            totalDisplay.textContent = `₦${formatter.format(total)}`;
        };
        

            const hydrateFromOption = (option) => {
                if (!option) return;
                nameInput.value = option.getAttribute('data-customer-name') ?? nameInput.value;
                emailInput.value = option.getAttribute('data-customer-email') ?? emailInput.value;
                phoneInput.value = option.getAttribute('data-customer-phone') ?? phoneInput.value;
            };

            customerSelect?.addEventListener('change', e => hydrateFromOption(e.target.selectedOptions?.[0]));
            toggleNewCustomerButton?.addEventListener('click', () => setNewCustomerFormVisible(newCustomerForm?.classList.contains('hidden')));
            window.addEventListener('admin-customer-created', (event) => {
                const detail = event.detail ?? {};
                const customer = (Array.isArray(detail) ? detail[0] : detail)?.customer ?? {};
                if (!customerSelect || !customer.id) return;
                let option = Array.from(customerSelect.options).find(o => o.value === String(customer.id));
                if (!option) {
                    option = document.createElement('option');
                    option.value = String(customer.id);
                    customerSelect.appendChild(option);
                }
                option.textContent = `${customer.name} · ${customer.email}`;
                option.setAttribute('data-customer-name', customer.name ?? '');
                option.setAttribute('data-customer-email', customer.email ?? '');
                option.setAttribute('data-customer-phone', customer.phone ?? '');
                customerSelect.value = String(customer.id);
                hydrateFromOption(option);
                setNewCustomerFormVisible(false);
            });

            catalogSelect?.addEventListener('change', () => { populateProductOptionSelectors(); updateCatalogPrice(); });
            [quantityInput, taxInput, discountInput, sizeSelect, materialSelect, densitySelect, finishSelect, deliverySelect].forEach(el => el?.addEventListener('input', updateCatalogPrice));

            addLineItemButton?.addEventListener('click', () => {
                if (!lineItemsBody) return;
                const row = document.createElement('tr');
                row.setAttribute('data-line-item-row', '');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <input data-line-item-description class="pb-input" placeholder="Item description">
                        <input type="hidden" value="custom" data-line-item-source>
                    </td>
                    <td class="px-4 py-3"><input type="number" min="1" step="1" value="1" data-line-item-quantity class="pb-input w-24"></td>
                    <td class="px-4 py-3"><input type="number" min="0" step="0.01" value="0" data-line-item-rate class="pb-input w-32"></td>
                    <td class="px-4 py-3 font-bold text-slate-900" data-line-item-amount>₦0.00</td>
                    <td class="px-4 py-3 text-right"><button type="button" data-remove-line-item class="pb-btn pb-btn-sm pb-btn-outline">Remove</button></td>
                `;
                lineItemsBody.appendChild(row);
                renumberLineItems();
                updateCatalogPrice();
            });

            lineItemsBody?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-line-item]');
                if (!button) return;
                const row = button.closest('[data-line-item-row]');
                const rows = lineItemRows();
                if (rows.length === 1) {
                    row.querySelector('[data-line-item-description]').value = '';
                    row.querySelector('[data-line-item-quantity]').value = '1';
                    row.querySelector('[data-line-item-rate]').value = '0';
                } else {
                    row.remove();
                }
                renumberLineItems();
                updateCatalogPrice();
            });

            lineItemsBody?.addEventListener('input', () => updateCatalogPrice());

            // Initialize
            setNewCustomerFormVisible(false);
            populateProductOptionSelectors();
            renumberLineItems();
            updateCatalogPrice();
        })();
    </script>
@endsection