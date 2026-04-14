@extends('layouts.theme')

@section('title', 'Order '.$product->name.' | Printbuka')

@section('content')
    @php
        $pricingPayload = [
            'basePrice' => (float) $product->price,
            'moq' => (int) $product->moq,
            'sizes' => $sizeOptions,
            'materials' => $materialOptions,
            'finishes' => $finishOptions,
            'deliveries' => $deliveryOptions,
        ];
        $authenticatedCustomer = auth()->user()?->role === 'customer' ? auth()->user() : null;
    @endphp
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.75fr_1.25fr] lg:px-8">
            <aside class="h-fit rounded-md bg-slate-950 p-6 text-white lg:sticky lg:top-28">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">{{ $serviceType === 'gift' ? 'Gift Order' : 'Print Order' }}</p>
                <h1 class="mt-3 text-4xl leading-tight">{{ $product->name }}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-300">{{ $product->short_description }}</p>

                <div class="mt-6 space-y-3 rounded-md bg-white p-5 text-slate-950">
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">MOQ</span>
                        <span class="font-black">{{ $product->moq }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">Unit price</span>
                        <span class="font-black">NGN {{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="font-bold text-slate-500">Finishing</span>
                        <span class="font-black">{{ $product->finishing }}</span>
                    </div>
                </div>
            </aside>

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Order Details</p>
                <h2 class="mt-2 text-4xl text-slate-950">Tell us what to prepare.</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">We will review your request, confirm artwork and delivery details, then guide you through payment and production.</p>

                <form action="{{ route('orders.store', $product) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label for="quantity" class="text-sm font-black text-slate-800">Quantity</label>
                        <input
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="{{ $product->moq }}"
                            value="{{ old('quantity', $product->moq) }}"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        <p class="mt-2 text-xs font-bold text-slate-500">Minimum order quantity is {{ $product->moq }}.</p>
                        @error('quantity')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-md border border-slate-200 p-5">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Product Options</p>
                        <p class="mt-2 text-xs font-bold text-slate-500">Option prices are calculated live. Delivery is added once; product options are calculated per MOQ batch.</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label for="size_format" class="text-sm font-black text-slate-800">Size / Format
                                <select id="size_format" name="size_format" data-price-group="sizes" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    @foreach ($sizeOptions as $option)
                                        <option value="{{ $option['label'] }}" @selected(old('size_format') === $option['label'])>{{ $option['label'] }}{{ (float) $option['price'] > 0 ? ' + NGN '.number_format((float) $option['price'], 2) : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label for="material_substrate" class="text-sm font-black text-slate-800">Material Type
                                <select id="material_substrate" name="material_substrate" data-price-group="materials" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    @foreach ($materialOptions as $option)
                                        <option value="{{ $option['label'] }}" @selected(old('material_substrate') === $option['label'])>{{ $option['label'] }}{{ (float) $option['price'] > 0 ? ' + NGN '.number_format((float) $option['price'], 2) : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label for="finish_lamination" class="text-sm font-black text-slate-800">Finish / Lamination
                                <select id="finish_lamination" name="finish_lamination" data-price-group="finishes" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    @foreach ($finishOptions as $option)
                                        <option value="{{ $option['label'] }}" @selected(old('finish_lamination') === $option['label'])>{{ $option['label'] }}{{ (float) $option['price'] > 0 ? ' + NGN '.number_format((float) $option['price'], 2) : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label for="delivery_method" class="text-sm font-black text-slate-800">Delivery
                                <select id="delivery_method" name="delivery_method" data-price-group="deliveries" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" required>
                                    @foreach ($deliveryOptions as $option)
                                        <option value="{{ $option['label'] }}" @selected(old('delivery_method') === $option['label'])>{{ $option['label'] }}{{ (float) $option['price'] > 0 ? ' + NGN '.number_format((float) $option['price'], 2) : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="mt-5 rounded-md bg-slate-950 p-5 text-white">
                            <p class="text-xs font-black uppercase tracking-wide text-cyan-300">Live Estimate</p>
                            <p class="mt-2 text-4xl font-black" id="live-order-total">NGN {{ number_format($product->price, 2) }}</p>
                            <div class="mt-4 grid gap-3 text-sm font-bold text-slate-200 sm:grid-cols-2">
                                <p>MOQ batches: <span id="live-order-batches">1</span></p>
                                <p>Production per batch: <span id="live-production-price">NGN {{ number_format($product->price, 2) }}</span></p>
                                <p>Delivery: <span id="live-delivery-price">NGN 0.00</span></p>
                                <p>Base MOQ price: NGN {{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="customer_name" class="text-sm font-black text-slate-800">First & Last Name</label>
                            <input id="customer_name" name="customer_name" type="text" value="{{ $authenticatedCustomer ? $authenticatedCustomer->displayName() : old('customer_name') }}" @readonly($authenticatedCustomer) class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 {{ $authenticatedCustomer ? 'bg-slate-100 text-slate-500' : '' }}" required />
                            @error('customer_name')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="customer_phone" class="text-sm font-black text-slate-800">Phone number</label>
                            <input id="customer_phone" name="customer_phone" type="text" value="{{ $authenticatedCustomer ? $authenticatedCustomer->phone : old('customer_phone') }}" @readonly($authenticatedCustomer) class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 {{ $authenticatedCustomer ? 'bg-slate-100 text-slate-500' : '' }}" required />
                            @error('customer_phone')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="customer_email" class="text-sm font-black text-slate-800">Email address</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ $authenticatedCustomer ? $authenticatedCustomer->email : old('customer_email') }}" @readonly($authenticatedCustomer) class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100 {{ $authenticatedCustomer ? 'bg-slate-100 text-slate-500' : '' }}" required />
                        @if ($authenticatedCustomer)
                            <p class="mt-2 text-xs font-bold text-slate-500">Using your verified account details. Update your profile if these details need to change.</p>
                        @endif
                        @error('customer_email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="delivery_city" class="text-sm font-black text-slate-800">Delivery city</label>
                            <input id="delivery_city" name="delivery_city" type="text" value="{{ old('delivery_city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            @error('delivery_city')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="delivery_address" class="text-sm font-black text-slate-800">Delivery address</label>
                            <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            @error('delivery_address')
                                <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="artwork_notes" class="text-sm font-black text-slate-800">{{ $serviceType === 'gift' ? 'Branding and gift notes' : 'Artwork and print notes' }}</label>
                        <textarea id="artwork_notes" name="artwork_notes" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Tell us about logo placement, colours, artwork files, delivery deadline or anything the production team should know.">{{ old('artwork_notes') }}</textarea>
                        @error('artwork_notes')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="job_asset_files" class="text-sm font-black text-slate-800">Artwork / Image Assets</label>
                        <input id="job_asset_files" name="job_asset_files[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                        <p class="mt-2 text-xs font-bold text-slate-500">Upload up to 5 images only: JPG, PNG or WebP. Maximum 5MB per image. PDFs, SVGs, archives and executable files are blocked for security.</p>
                        @error('job_asset_files')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                        @error('job_asset_files.*')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Submit Order Request</button>
                </form>
            </section>
        </section>
    </main>

    <script>
        (() => {
            const pricing = @json($pricingPayload);
            const currency = new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' });
            const quantityInput = document.getElementById('quantity');
            const totalOutput = document.getElementById('live-order-total');
            const batchOutput = document.getElementById('live-order-batches');
            const productionOutput = document.getElementById('live-production-price');
            const deliveryOutput = document.getElementById('live-delivery-price');

            const selectedPrice = (group) => {
                const select = document.querySelector(`[data-price-group="${group}"]`);
                const option = (pricing[group] || []).find((item) => item.label === select?.value);

                return Number(option?.price || 0);
            };

            const recalculate = () => {
                const quantity = Math.max(Number(quantityInput.value || pricing.moq), Number(pricing.moq || 1));
                const batches = Math.ceil(quantity / Math.max(Number(pricing.moq || 1), 1));
                const productionPrice = Number(pricing.basePrice || 0) + selectedPrice('sizes') + selectedPrice('materials') + selectedPrice('finishes');
                const deliveryPrice = selectedPrice('deliveries');
                const total = (batches * productionPrice) + deliveryPrice;

                batchOutput.textContent = batches;
                productionOutput.textContent = currency.format(productionPrice);
                deliveryOutput.textContent = currency.format(deliveryPrice);
                totalOutput.textContent = currency.format(total);
            };

            document.querySelectorAll('[data-price-group]').forEach((select) => select.addEventListener('change', recalculate));
            quantityInput.addEventListener('input', recalculate);
            recalculate();
        })();
    </script>
@endsection
