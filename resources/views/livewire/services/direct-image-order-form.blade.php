<div class="pb-28 md:pb-0">
    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Direct Image Order Form</p>
    <h2 class="mt-2 text-4xl text-slate-950">Place Your Direct Image Order</h2>
    <p class="mt-3 text-sm leading-6 text-slate-600">Pricing updates instantly based on selected paper type, paper size, design support, and delivery choice.</p>

    @if (session('status') || session('warning'))
        <p class="mt-5 rounded-md border {{ session('status') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }} px-4 py-3 text-sm font-bold">
            {{ session('status') ?? session('warning') }}
        </p>
    @endif

    <form wire:submit="submit" class="mt-6 grid gap-6 lg:grid-cols-[1fr_300px]">
        <div class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-black text-slate-800">Quantity
                    <input type="number" min="1" wire:model.live="quantity" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                    @error('quantity') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="text-sm font-black text-slate-800">Paper Type
                    <select wire:model.live="paper_type" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                        @foreach ($paperTypeOptions as $option)
                            <option value="{{ $option['label'] }}">{{ $option['label'] }} (+ NGN {{ number_format((float) $option['price'], 2) }})</option>
                        @endforeach
                    </select>
                    @error('paper_type') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="text-sm font-black text-slate-800">Paper Size
                    <select wire:model.live="paper_size" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                        @foreach ($paperSizeOptions as $option)
                            <option value="{{ $option['label'] }}">{{ $option['label'] }} (+ NGN {{ number_format((float) $option['price'], 2) }})</option>
                        @endforeach
                    </select>
                    @error('paper_size') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-black text-slate-800">Do you have a Design/Artwork?</p>
                <div class="mt-3 flex flex-wrap gap-4 text-sm font-semibold">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" wire:model.live="has_design" value="yes" class="text-pink-600 focus:ring-pink-500">
                        Yes, I have one
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" wire:model.live="has_design" value="no" class="text-pink-600 focus:ring-pink-500">
                        No, create a design brief
                    </label>
                </div>

                @if ($has_design === 'yes')
                    <div class="mt-4">
                        <label class="text-sm font-black text-slate-800">Upload Design/Artwork</label>
                        <input type="file" wire:model="design_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm font-semibold">
                        <p class="mt-2 text-xs font-semibold text-slate-500">Only image uploads are allowed. For PDF, SVG, or ZIP files, share an external drive link below.</p>
                        @error('design_file') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                        <label class="mt-4 block text-sm font-black text-slate-800">External Drive Links (Optional)</label>
                        <textarea wire:model.live="asset_drive_links" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Paste one link per line (Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, Mega)."></textarea>
                        @error('asset_drive_links') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="mt-4">
                        <label class="text-sm font-black text-slate-800">Design Brief</label>
                        <textarea wire:model.live="design_brief" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Describe your concept, brand colors, text content, dimensions, and finishing expectations."></textarea>
                        @error('design_brief') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                        <p class="mt-2 text-xs font-semibold text-slate-500">Design support fee: NGN {{ number_format((float) $this->designPrice, 2) }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-black text-slate-800">Delivery Type</p>
                <div class="mt-3 flex flex-wrap gap-4 text-sm font-semibold">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" wire:model.live="delivery_type" value="pickup" class="text-pink-600 focus:ring-pink-500">
                        Pickup
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" wire:model.live="delivery_type" value="delivery" class="text-pink-600 focus:ring-pink-500">
                        Delivery Address
                    </label>
                </div>

                @if ($delivery_type === 'delivery')
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @if (count($savedDeliveryAddresses) > 0)
                            <label class="sm:col-span-2 text-sm font-black text-slate-800">Saved Address
                                <select wire:model.live="delivery_address_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    <option value="">Select saved address</option>
                                    @foreach ($savedDeliveryAddresses as $address)
                                        <option value="{{ $address['id'] }}">{{ $address['label'] }} - {{ $address['city'] }} ({{ $address['address'] }})</option>
                                    @endforeach
                                </select>
                            </label>
                        @endif
                        <label class="text-sm font-black text-slate-800">Delivery City
                            <input type="text" wire:model.live="delivery_city" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            @error('delivery_city') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="text-sm font-black text-slate-800">Delivery Address
                            <input type="text" wire:model.live="delivery_address" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                            @error('delivery_address') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <p class="sm:col-span-2 text-xs font-semibold text-slate-500">Delivery fee: NGN {{ number_format((float) $this->deliveryPrice, 2) }}</p>
                    </div>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <label class="text-sm font-black text-slate-800">Full Name
                    <input type="text" wire:model.live="customer_name" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                    @error('customer_name') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="text-sm font-black text-slate-800">Email
                    <input type="email" wire:model.live="customer_email" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                    @error('customer_email') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="text-sm font-black text-slate-800">Phone
                    <input type="text" wire:model.live="customer_phone" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                    @error('customer_phone') <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="hidden md:inline-flex min-h-12 items-center justify-center rounded-md bg-pink-600 px-6 text-sm font-black text-white transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-70">Proceed to Paystack</button>
        </div>

        <aside class="hidden rounded-md border border-slate-200 bg-white p-5 shadow-sm lg:block lg:sticky lg:top-24 lg:self-start">
            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Estimated Total</p>
            <p class="mt-2 text-3xl font-black text-pink-700">NGN {{ number_format((float) $this->estimatedTotal, 2) }}</p>
            <div class="mt-4 space-y-2 text-xs font-semibold text-slate-600">
                <div class="flex items-center justify-between"><span>Base service</span><span>NGN {{ number_format((float) $this->basePrice, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Paper type</span><span>NGN {{ number_format((float) $this->paperTypePrice, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Paper size</span><span>NGN {{ number_format((float) $this->paperSizePrice, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Unit price</span><span>NGN {{ number_format((float) $this->unitPrice, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Quantity</span><span>{{ $quantity }}</span></div>
                <div class="flex items-center justify-between"><span>Design fee</span><span>NGN {{ number_format((float) $this->designPrice, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Delivery fee</span><span>NGN {{ number_format((float) $this->deliveryPrice, 2) }}</span></div>
            </div>
        </aside>

        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 backdrop-blur md:hidden">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Estimated Total</p>
                    <p class="text-xl font-black text-pink-700">NGN {{ number_format((float) $this->estimatedTotal, 2) }}</p>
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-70">Pay on Paystack</button>
            </div>
        </div>
    </form>
</div>
