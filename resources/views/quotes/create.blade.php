@extends('layouts.theme')

@section('title', 'Get Quote | Printbuka')

@section('content')
    @php
        $quoteCategories = collect($categories ?? []);
        $selectedQuoteProduct = $selectedProduct ?? null;
        $selectedJobType = old('job_type', $selectedQuoteProduct?->name);
        $jobTypeOptions = collect($jobTypes);
    @endphp
    <main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                
                {{-- Left Sidebar --}}
                <aside class="h-fit rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white lg:sticky lg:top-28 shadow-xl">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="h-10 w-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                            <svg class="h-5 w-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Get Quote</p>
                    </div>
                    <h1 class="mt-2 text-3xl font-bold leading-tight lg:text-4xl">Tell us what you need printed.</h1>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Share the job details, quantity, delivery location and any artwork files you already have.</p>

                    {{-- Categories Quick Links --}}
                    @if($quoteCategories->isNotEmpty())
                        <div class="mt-6">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Popular Categories</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($quoteCategories->take(6) as $category)
                                    <a href="{{ route('products.category', $category->slug) }}" 
                                       class="text-xs px-3 py-1.5 rounded-full bg-white/10 hover:bg-pink-500/30 transition text-slate-300 hover:text-white">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 rounded-2xl bg-white/10 p-5 backdrop-blur-sm">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-cyan-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-white">Attach artwork, logos or images</p>
                                <p class="text-xs text-slate-300 mt-1">The team can provide accurate pricing faster with visual references.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <p class="text-xs text-slate-400">Need help? Call us</p>
                        <p class="text-sm font-bold text-white">📞 {{ $siteSettings['contact_phone'] ?? '08035245784' }}</p>
                        <p class="text-xs text-slate-400 mt-2">✉️ {{ $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng' }}</p>
                    </div>
                </aside>

                {{-- Right Side - Quote Form --}}
                <section class="card bg-white rounded-2xl shadow-xl border border-slate-100">
                    <div class="card-body p-6 sm:p-8">
                        <div class="mb-6">
                            <div class="badge bg-pink-100 text-pink-700 border-0 mb-2">Quote Request</div>
                            <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">Send the brief.</h2>
                            <p class="mt-2 text-sm text-slate-500">We will review the request and contact you with pricing and next steps.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-error shadow-lg mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Please check the highlighted details and try again.</span>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert bg-cyan-50 border-cyan-200 text-cyan-900 shadow-sm mb-6">
                                <span class="text-sm font-bold">{{ session('status') }}</span>
                            </div>
                        @endif

                        @if ($selectedQuoteProduct)
                            <div class="mb-6 rounded-xl border border-pink-100 bg-pink-50 p-4">
                                <p class="text-xs font-black uppercase tracking-wide text-pink-700">Selected product</p>
                                <p class="mt-1 text-lg font-black text-slate-950">{{ $selectedQuoteProduct->name }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-600">MOQ {{ $selectedQuoteProduct->moq }}{{ $selectedQuoteProduct->paper_size ? ' · '.$selectedQuoteProduct->paper_size : '' }}{{ $selectedQuoteProduct->paper_density ? ' · '.$selectedQuoteProduct->paper_density : '' }}</p>
                            </div>
                        @endif

                        <form action="{{ route('quotes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            {{-- Product & Job Type Row --}}
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Product (Optional)</span>
                                    </label>
                                    <select name="product_id" class="select select-bordered w-full focus:select-primary">
                                        <option value="">Custom job / Not listed</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected((int) old('product_id', $selectedQuoteProduct?->id) === $product->id)>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Job Type *</span>
                                    </label>
                                    <select name="job_type" class="select select-bordered w-full focus:select-primary @error('job_type') select-error @enderror" required>
                                        <option value="">Select job type</option>
                                        @foreach ($jobTypes as $jobType)
                                            <option @selected($selectedJobType === $jobType)>{{ $jobType }}</option>
                                        @endforeach
                                        @if ($selectedQuoteProduct && ! $jobTypeOptions->contains($selectedQuoteProduct->name))
                                            <option value="{{ $selectedQuoteProduct->name }}" @selected($selectedJobType === $selectedQuoteProduct->name)>{{ $selectedQuoteProduct->name }}</option>
                                        @endif
                                    </select>
                                    @error('job_type') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Size & Quantity Row --}}
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Size / Format</span>
                                    </label>
                                    <select name="size_format" class="select select-bordered w-full focus:select-primary">
                                        <option value="">Select size</option>
                                        @foreach ($sizes as $size)
                                            <option @selected(old('size_format', $selectedQuoteProduct?->paper_size) === $size)>{{ $size }}</option>
                                        @endforeach
                                        @if ($selectedQuoteProduct?->paper_size && ! collect($sizes)->contains($selectedQuoteProduct->paper_size))
                                            <option value="{{ $selectedQuoteProduct->paper_size }}" selected>{{ $selectedQuoteProduct->paper_size }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Quantity *</span>
                                    </label>
                                    <input type="number" min="1" name="quantity" value="{{ old('quantity', $selectedQuoteProduct?->moq ?? 1) }}" 
                                        class="input input-bordered w-full focus:input-primary @error('quantity') input-error @enderror" required />
                                    @error('quantity') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Budget Field --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-slate-700">Budget (Subject to negotiation & approval)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">₦</span>
                                    <input type="number" min="0" step="0.01" name="quote_budget" value="{{ old('quote_budget') }}" 
                                        id="quote-budget-input"
                                        data-naira-input data-naira-preview-id="quote-budget-preview"
                                        placeholder="Enter your planned budget"
                                        class="input input-bordered w-full pl-10 focus:input-primary @error('quote_budget') input-error @enderror" />
                                </div>
                                <span id="quote-budget-preview" class="mt-2 text-xs font-semibold text-slate-500">₦0.00</span>
                                <span class="mt-1 text-xs text-slate-400">Final pricing is still subject to Printbuka review, negotiation, and approval.</span>
                                @error('quote_budget') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Material & Finish Row --}}
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Material / Substrate</span>
                                    </label>
                                    <select name="material_substrate" class="select select-bordered w-full focus:select-primary">
                                        <option value="">Select material</option>
                                        @foreach ($materials as $material)
                                            <option @selected(old('material_substrate', $selectedQuoteProduct?->paper_type) === $material)>{{ $material }}</option>
                                        @endforeach
                                        @if ($selectedQuoteProduct?->paper_type && ! collect($materials)->contains($selectedQuoteProduct->paper_type))
                                            <option value="{{ $selectedQuoteProduct->paper_type }}" selected>{{ $selectedQuoteProduct->paper_type }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text font-semibold text-slate-700">Finish / Lamination</span>
                                    </label>
                                    <select name="finish_lamination" class="select select-bordered w-full focus:select-primary">
                                        <option value="">Select finish</option>
                                        @foreach ($finishes as $finish)
                                            <option @selected(old('finish_lamination', $selectedQuoteProduct?->finishing) === $finish)>{{ $finish }}</option>
                                        @endforeach
                                        @if ($selectedQuoteProduct?->finishing && ! collect($finishes)->contains($selectedQuoteProduct->finishing))
                                            <option value="{{ $selectedQuoteProduct->finishing }}" selected>{{ $selectedQuoteProduct->finishing }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            {{-- Customer Information --}}
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="form-control w-full">
                                    <label class="label"><span class="label-text font-semibold text-slate-700">Full Name *</span></label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->displayName() ?? '') }}" 
                                        class="input input-bordered w-full focus:input-primary @error('customer_name') input-error @enderror" required />
                                    @error('customer_name') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-control w-full">
                                    <label class="label"><span class="label-text font-semibold text-slate-700">Phone Number *</span></label>
                                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" 
                                        class="input input-bordered w-full focus:input-primary @error('customer_phone') input-error @enderror" required />
                                    @error('customer_phone') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-control w-full sm:col-span-2">
                                    <label class="label"><span class="label-text font-semibold text-slate-700">Email Address *</span></label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" 
                                        class="input input-bordered w-full focus:input-primary @error('customer_email') input-error @enderror" required />
                                    @error('customer_email') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-control w-full">
                                    <label class="label"><span class="label-text font-semibold text-slate-700">Delivery City</span></label>
                                    <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" 
                                        class="input input-bordered w-full focus:input-primary" />
                                </div>

                                <div class="form-control w-full">
                                    <label class="label"><span class="label-text font-semibold text-slate-700">Delivery Address</span></label>
                                    <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" 
                                        class="input input-bordered w-full focus:input-primary" />
                                </div>
                            </div>

                            {{-- Artwork Notes --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-slate-700">Artwork / Brief Notes</span>
                                </label>
                                <textarea name="artwork_notes" rows="5" 
                                    class="textarea textarea-bordered w-full focus:textarea-primary"
                                    placeholder="Mention colours, deadline, file status, product references or finishing instructions.">{{ old('artwork_notes', $selectedQuoteProduct ? 'Product: '.$selectedQuoteProduct->name : '') }}</textarea>
                            </div>

                            {{-- Image Uploads --}}
                            <div class="space-y-3">
                                <label class="font-semibold text-slate-700">Artwork / Image Assets</label>
                                <livewire:uploads.secure-image-upload
                                    input-name="job_asset_image_paths"
                                    :multiple="true"
                                    directory="job-assets/images"
                                    :max-size-kb="5120"
                                    :max-files="20"
                                    :initial-paths="old('job_asset_image_paths', [])"
                                />
                                <p class="text-xs text-slate-400">Upload images securely (JPG, PNG, WEBP up to 5MB each).</p>
                                @error('job_asset_image_paths') <span class="text-xs text-pink-600">{{ $message }}</span> @enderror
                                @error('job_asset_image_paths.*') <span class="text-xs text-pink-600">{{ $message }}</span> @enderror
                            </div>

                            {{-- External Drive Links --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-slate-700">External Drive Links (For PDF, SVG, ZIP Files)</span>
                                </label>
                                <textarea name="asset_drive_links" rows="4" 
                                    class="textarea textarea-bordered w-full focus:textarea-primary font-mono text-sm"
                                    placeholder="Paste one link per line (Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, Mega).">{{ old('asset_drive_links') }}</textarea>
                                <p class="mt-2 text-xs text-slate-400">Document and ZIP uploads are blocked for security. Share them as external links instead.</p>
                                @error('asset_drive_links') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-0 text-white font-bold shadow-md shadow-pink-200 mt-6">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Submit Quote Request
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
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
