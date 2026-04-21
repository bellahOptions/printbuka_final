@extends('layouts.theme')

@section('title', 'Get Quote | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
            <aside class="h-fit rounded-md bg-slate-950 p-6 text-white lg:sticky lg:top-28">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Get Quote</p>
                <h1 class="mt-3 text-4xl leading-tight">Tell us what you need printed.</h1>
                <p class="mt-4 text-sm leading-7 text-slate-300">Share the job details, quantity, delivery location and any artwork files you already have.</p>

                <div class="mt-6 space-y-3 rounded-md bg-white p-5 text-sm font-bold text-slate-700">
                    <p>Attach artwork, logos or images so the team can can provide accurate pricing faster.</p>
                </div>
            </aside>

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Quote Request</p>
                <h2 class="mt-2 text-4xl text-slate-950">Send the brief.</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">We will review the request and contact you with pricing and next steps.</p>

                @if ($errors->any())
                    <p class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">Check the highlighted details and try again.</p>
                @endif

                <form action="{{ route('quotes.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black text-slate-800">Product
                            <select name="product_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                                <option value="">Custom job</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-black text-slate-800">Job Type
                            <select name="job_type" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                                <option value="">Select job type</option>
                                @foreach ($jobTypes as $jobType)
                                    <option @selected(old('job_type') === $jobType)>{{ $jobType }}</option>
                                @endforeach
                            </select>
                            @error('job_type')<span class="mt-2 block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                        </label>
                        <label class="text-sm font-black text-slate-800">Size / Format
                            <select name="size_format" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                                <option value="">Select size</option>
                                @foreach ($sizes as $size)
                                    <option @selected(old('size_format') === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-black text-slate-800">Quantity
                            <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                            @error('quantity')<span class="mt-2 block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                        </label>
                        <label class="text-sm font-black text-slate-800">Budget (Subject to negotiation & approval)
                            <div class="relative mt-2">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-black text-slate-500">₦</span>
                                <input id="quote-budget-input" type="number" min="0" step="0.01" name="quote_budget" value="{{ old('quote_budget') }}" data-naira-input data-naira-preview-id="quote-budget-preview" placeholder="Enter your planned budget" class="min-h-12 w-full rounded-md border border-slate-200 px-4 pl-10 text-sm font-semibold">
                            </div>
                            <span id="quote-budget-preview" class="mt-2 block text-xs font-bold text-slate-500">₦0.00</span>
                            <span class="mt-1 block text-xs font-bold text-slate-500">Final pricing is still subject to Printbuka review, negotiation, and approval.</span>
                            @error('quote_budget')<span class="mt-2 block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                        </label>
                        <label class="text-sm font-black text-slate-800">Material / Substrate
                            <select name="material_substrate" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                                <option value="">Select material</option>
                                @foreach ($materials as $material)
                                    <option @selected(old('material_substrate') === $material)>{{ $material }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-black text-slate-800">Finish / Lamination
                            <select name="finish_lamination" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                                <option value="">Select finish</option>
                                @foreach ($finishes as $finish)
                                    <option @selected(old('finish_lamination') === $finish)>{{ $finish }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black text-slate-800">Full Name<input name="customer_name" value="{{ old('customer_name', auth()->user()?->displayName() ?? '') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"></label>
                        <label class="text-sm font-black text-slate-800">Phone Number<input name="customer_phone" value="{{ old('customer_phone') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"></label>
                        <label class="text-sm font-black text-slate-800 sm:col-span-2">Email Address<input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"></label>
                        <label class="text-sm font-black text-slate-800">Delivery City<input name="delivery_city" value="{{ old('delivery_city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"></label>
                        <label class="text-sm font-black text-slate-800">Delivery Address<input name="delivery_address" value="{{ old('delivery_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"></label>
                    </div>

                    <label class="block text-sm font-black text-slate-800">Artwork / Brief Notes
                        <textarea name="artwork_notes" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold" placeholder="Mention colours, deadline, file status, product references or finishing instructions.">{{ old('artwork_notes') }}</textarea>
                    </label>

                    <div class="space-y-2">
                        <label class="block text-sm font-black text-slate-800">Artwork / Image Assets</label>
                        <livewire:uploads.secure-image-upload
                            input-name="job_asset_image_paths"
                            :multiple="true"
                            directory="job-assets/images"
                            :max-size-kb="5120"
                            :max-files="20"
                            :initial-paths="old('job_asset_image_paths', [])"
                        />
                        <span class="block text-xs font-bold text-slate-500">Upload images securely via Livewire (JPG, PNG, WEBP up to 5MB each).</span>
                        @error('job_asset_image_paths')<span class="block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                        @error('job_asset_image_paths.*')<span class="block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                    </div>

                    <label class="block text-sm font-black text-slate-800">External Drive Links (For PDF, SVG, ZIP Files)
                        <textarea name="asset_drive_links" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold" placeholder="Paste one link per line (Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, Mega).">{{ old('asset_drive_links') }}</textarea>
                        <span class="mt-2 block text-xs font-bold text-slate-500">Document and ZIP uploads are blocked for security. Share them as external links instead.</span>
                        @error('asset_drive_links')<span class="mt-2 block text-sm font-semibold text-pink-700">{{ $message }}</span>@enderror
                    </label>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Submit Quote Request</button>
                </form>
            </section>
        </section>
    </main>
    <script>
        (() => {
            const formatter = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
            });

            document.querySelectorAll('[data-naira-input]').forEach((input) => {
                const previewId = input.getAttribute('data-naira-preview-id');
                const preview = previewId ? document.getElementById(previewId) : null;

                const sync = () => {
                    const amount = Number(input.value);
                    const displayAmount = Number.isFinite(amount) && input.value !== '' ? amount : 0;

                    if (preview) {
                        preview.textContent = formatter.format(displayAmount);
                    }
                };

                input.addEventListener('input', sync);
                sync();
            });
        })();
    </script>
@endsection
