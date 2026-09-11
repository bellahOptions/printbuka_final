@extends('layouts.admin')

@section('title', 'Create Admin Job | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        @php
            $orderItems = old('order_items', [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'size_format' => '', 'material_substrate' => '', 'finish_lamination' => '', 'artwork_notes' => ''],
            ]);
        @endphp

        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Job Tracker
                </a>
                <h1 class="pb-page-title">Create a new job</h1>
                <p class="pb-page-subtitle">Log client brief, add order items, optionally create an invoice now.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="pb-alert pb-alert-error items-start">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold">Please review the highlighted details:</p>
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

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="fade-in-up section-delay-1 space-y-6">
            @csrf

            <!-- Hidden auto‑filled fields -->
            <input type="hidden" name="channel" value="Manual">
            <input type="hidden" name="job_type" value="Custom Order">

            <!-- Invoice Checkbox (first, controls rest of form) -->
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-4">
                    <h2 class="pb-section-title">Invoice & Pricing</h2>
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
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Client Information</h2>
                    <p class="pb-section-subtitle">Select existing or create new customer</p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="pb-label">Existing Customer</label>
                        <select id="job-customer-select" name="customer_id" class="pb-input">
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
                            class="pb-btn pb-btn-sm pb-btn-outline"
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
                        <div class="pb-field">
                            <label class="pb-label">Client Name *</label>
                            <input id="job-customer-name" name="customer_name" value="{{ old('customer_name') }}" required 
                                   class="pb-input"
                                   placeholder="Full name">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Client Email *</label>
                            <input id="job-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required 
                                   class="pb-input"
                                   placeholder="email@example.com">
                        </div>
                        <div class="pb-field sm:col-span-2">
                            <label class="pb-label">Client Phone *</label>
                            <input id="job-customer-phone" name="customer_phone" value="{{ old('customer_phone') }}" required 
                                   class="pb-input"
                                   placeholder="+234 XXX XXX XXXX">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Preference Section -->
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Delivery Preference</h2>
                    <p class="pb-section-subtitle">Choose how the client will receive the job</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 mb-4">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 text-sm font-bold transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                        <input id="delivery-preference-pickup" type="radio" name="delivery_preference" value="pickup" @checked(old('delivery_preference') === 'pickup') class="h-5 w-5 border-slate-300 text-pink-600 focus:ring-pink-500">
                        <div>
                            <p class="font-bold text-slate-900">Client Pickup</p>
                            <p class="text-xs text-slate-500 mt-0.5">Client will collect from office</p>
                        </div>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 text-sm font-bold transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                        <input id="delivery-preference-delivery" type="radio" name="delivery_preference" value="delivery" @checked(old('delivery_preference', 'delivery') === 'delivery') class="h-5 w-5 border-slate-300 text-pink-600 focus:ring-pink-500">
                        <div>
                            <p class="font-bold text-slate-900">Delivery</p>
                            <p class="text-xs text-slate-500 mt-0.5">Deliver to client address</p>
                        </div>
                    </label>
                </div>
                @error('delivery_preference')
                    <p class="pb-field-error mb-4">{{ $message }}</p>
                @enderror

                <div id="delivery-fields" class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Delivery Method</label>
                        <select id="delivery-method" name="delivery_method" class="pb-input">
                            <option value="">— Select delivery method —</option>
                            @foreach ($deliveryMethods as $method)@continue($method === 'Client Pickup')<option value="{{ $method }}" @selected(old('delivery_method') === $method)>{{ $method }}</option>@endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Delivery City</label>
                        <input id="delivery-city" name="delivery_city" value="{{ old('delivery_city') }}" class="pb-input" placeholder="e.g., Lagos">
                        @error('delivery_city')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Delivery Address</label>
                        <input id="delivery-address" name="delivery_address" value="{{ old('delivery_address') }}" class="pb-input" placeholder="Full delivery address">
                        @error('delivery_address')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const pickupRadio = document.getElementById('delivery-preference-pickup');
                    const deliveryRadio = document.getElementById('delivery-preference-delivery');
                    const deliveryFields = document.getElementById('delivery-fields');
                    const deliveryMethod = document.getElementById('delivery-method');
                    const deliveryCity = document.getElementById('delivery-city');
                    const deliveryAddress = document.getElementById('delivery-address');

                    function syncDeliveryState() {
                        const isDelivery = deliveryRadio?.checked;
                        if (!deliveryFields) return;
                        deliveryFields.style.display = isDelivery ? 'grid' : 'none';
                        if (deliveryMethod) deliveryMethod.required = false;
                        if (deliveryCity) deliveryCity.required = Boolean(isDelivery);
                        if (deliveryAddress) deliveryAddress.required = Boolean(isDelivery);
                        if (!isDelivery) {
                            if (deliveryMethod) deliveryMethod.value = '';
                            if (deliveryCity) deliveryCity.value = '';
                            if (deliveryAddress) deliveryAddress.value = '';
                        }
                    }

                    pickupRadio?.addEventListener('change', syncDeliveryState);
                    deliveryRadio?.addEventListener('change', syncDeliveryState);
                    syncDeliveryState();
                });
            </script>

            <!-- Job Brief Section -->
            <div class="pb-card p-6 lg:p-8">
                <div class="mb-6">
                    <h2 class="pb-section-title">Job Brief</h2>
                    <p class="pb-section-subtitle">Define job specifications and requirements</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Priority *</label>
                        <select name="priority" required class="pb-input">
                            @foreach ($priorities as $priority)<option @selected(old('priority', '🟡 Normal') === $priority)>{{ $priority }}</option>@endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Assigned Designer</label>
                        <p class="pb-input bg-slate-50 text-slate-600 flex items-center">Auto-assigned after job creation.</p>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Brief Date</label>
                        <p class="pb-input bg-slate-50 text-slate-600 flex items-center">Set to job creation date/time.</p>
                    </div>
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Job Image Assets</label>
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
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                        @error('job_asset_image_paths.*')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Artwork Documents (PDF, SVG, ZIP)</label>
                        <input type="file" name="job_asset_files[]" multiple accept=".pdf,.svg,.zip" class="pb-input">
                        <p class="mt-2 text-xs text-slate-500">Non-image assets (PDF, SVG, ZIP up to 20MB each).</p>
                        @error('job_asset_files.*')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="pb-card p-6 lg:p-8" id="order-items-section">
                <div class="mb-6">
                    <h2 class="pb-section-title">Order Items</h2>
                    <p class="pb-section-subtitle" id="order-items-hint">Add items with descriptions, quantities, and optional reference images. Unit price can be added when creating an invoice.</p>
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
                                <div class="pb-field">
                                    <label class="pb-label">Description *</label>
                                    <input type="text" name="order_items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required
                                           class="pb-input" placeholder="Add item details">
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Qty *</label>
                                    <input type="number" min="1" name="order_items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required
                                           class="pb-input">
                                </div>
                                <div class="pb-field" id="invoice-unit-price-field-{{ $index }}">
                                    <label class="pb-label">Unit Price (₦)</label>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-slate-500">₦</span>
                                        <input type="number" min="0" step="0.01" name="order_items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}"
                                               class="order-item-unit-price pb-input" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Amount</label>
                                    <p class="order-item-amount pb-input bg-slate-100 font-bold flex items-center">
                                        ₦{{ number_format(((float)($item['unit_price'] ?? 0)) * ((int)($item['quantity'] ?? 1)), 2) }}
                                    </p>
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Size / Format</label>
                                    <select name="order_items[{{ $index }}][size_format]" class="pb-input">
                                        <option value="">— Select —</option>
                                        @foreach ($sizes as $size)<option @selected(($item['size_format'] ?? '') === $size)>{{ $size }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Material / Substrate</label>
                                    <select name="order_items[{{ $index }}][material_substrate]" class="pb-input">
                                        <option value="">— Select —</option>
                                        @foreach ($materials as $material)<option @selected(($item['material_substrate'] ?? '') === $material)>{{ $material }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Finish / Lamination</label>
                                    <select name="order_items[{{ $index }}][finish_lamination]" class="pb-input">
                                        <option value="">— Select —</option>
                                        @foreach ($finishes as $finish)<option @selected(($item['finish_lamination'] ?? '') === $finish)>{{ $finish }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Artwork Notes</label>
                                    <input type="text" name="order_items[{{ $index }}][artwork_notes]" value="{{ $item['artwork_notes'] ?? '' }}"
                                           class="pb-input" placeholder="Optional notes">
                                </div>
                                <div class="pb-field">
                                    <label class="pb-label">Image</label>
                                    <input type="file" name="order_items[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp"
                                           class="pb-input">
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="button" class="remove-order-item-row pb-btn pb-btn-icon pb-btn-outline" aria-label="Remove item row">&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <button type="button" id="add-order-item-row" class="pb-btn pb-btn-md pb-btn-outline">
                            + Add another item
                        </button>
                    </div>
                </div>
                @error('order_items')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('order_items.*.description')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('order_items.*.quantity')<p class="pb-field-error">{{ $message }}</p>@enderror
                @error('order_items.*.image')<p class="pb-field-error">{{ $message }}</p>@enderror
            </div>

            <!-- Internal Notes -->
            <div class="pb-card p-6 lg:p-8">
                <div class="pb-field">
                    <label class="pb-label">Internal Notes</label>
                    <textarea name="internal_notes" rows="4" data-rich-editor class="pb-textarea bg-slate-50" placeholder="Private notes for staff only">{{ old('internal_notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="pb-btn pb-btn-lg pb-btn-primary w-full">
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
                    <div class="pb-field">
                        <label class="pb-label">Description *</label>
                        <input type="text" name="order_items[0][description]" required class="pb-input" placeholder="Add item details">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Qty *</label>
                        <input type="number" min="1" name="order_items[0][quantity]" value="1" required class="pb-input">
                    </div>
                    <div class="pb-field" id="invoice-unit-price-field-0">
                        <label class="pb-label">Unit Price (₦)</label>
                        <input type="number" min="0" step="0.01" name="order_items[0][unit_price]" value="0"
                               class="order-item-unit-price pb-input" placeholder="0.00">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Amount</label>
                        <p class="order-item-amount pb-input bg-slate-100 font-bold flex items-center">₦0.00</p>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Size / Format</label>
                        <select name="order_items[0][size_format]" class="pb-input">
                            <option value="">— Select —</option>
                            @foreach ($sizes as $size)<option>{{ $size }}</option>@endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Material / Substrate</label>
                        <select name="order_items[0][material_substrate]" class="pb-input">
                            <option value="">— Select —</option>
                            @foreach ($materials as $material)<option>{{ $material }}</option>@endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Finish / Lamination</label>
                        <select name="order_items[0][finish_lamination]" class="pb-input">
                            <option value="">— Select —</option>
                            @foreach ($finishes as $finish)<option>{{ $finish }}</option>@endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Artwork Notes</label>
                        <input type="text" name="order_items[0][artwork_notes]" class="pb-input" placeholder="Optional notes">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Image</label>
                        <input type="file" name="order_items[0][image]" accept="image/jpeg,image/png,image/webp" class="pb-input">
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" class="remove-order-item-row pb-btn pb-btn-icon pb-btn-outline" aria-label="Remove item row">&times;</button>
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