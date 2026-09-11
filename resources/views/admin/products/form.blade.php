@extends('layouts.admin')

@section('title', ($product->exists ? 'Edit Product' : 'Create Product') . ' | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                        <a href="{{ route('admin.products.index') }}" class="hover:text-pink-600 transition flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            Products
                        </a>
                        <span>/</span>
                        <span class="text-slate-700 font-medium">{{ $product->exists ? 'Edit Product' : 'Create Product' }}</span>
                    </div>
                    <h1 class="pb-page-title">{{ $product->exists ? 'Edit Product' : 'Create New Product' }}</h1>
                    <p class="pb-page-subtitle">
                        {{ $product->exists ? 'Update product details, pricing, and options.' : 'Add a new product to your catalog.' }}
                    </p>
                </div>
                <div class="flex gap-3">
                    @if($product->exists)
                        <a href="{{ route('products.show', $product) }}" class="pb-btn pb-btn-md pb-btn-outline self-start">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Product
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="pb-card overflow-hidden">
            <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
                @csrf
                @if ($product->exists) @method('PUT') @endif

                {{-- Basic Information Section --}}
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-pink-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="pb-section-title">Basic Information</h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Product Name --}}
                        <div class="pb-field">
                            <label class="pb-label">Product Name *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                class="pb-input w-full @error('name') pb-input-error @enderror"
                                placeholder="e.g., Premium Business Cards" required />
                            @error('name') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="pb-field">
                            <label class="pb-label">Category</label>
                            <select name="product_category_id" id="product-category-select"
                                class="pb-select w-full">
                                <option value="">Unassigned</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('product_category_id', $product->product_category_id) === $category->id)>
                                        {{ $category->parent ? $category->parent->name.' > '.$category->name : $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_id') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Service Type --}}
                        <div class="pb-field">
                            <label class="pb-label">Service Bucket *</label>
                            <select name="service_type"
                                class="pb-select w-full @error('service_type') pb-input-error @enderror"
                                required>
                                @foreach ($serviceOptions as $serviceKey => $serviceLabel)
                                    <option value="{{ $serviceKey }}" @selected(old('service_type', $product->service_type ?? 'print') === $serviceKey)>
                                        {{ $serviceLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400 mt-1">This links the product to a service flow (print, gift, DTF, UV DTF, engraving, etc.).</p>
                            @error('service_type') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- MOQ --}}
                        <div class="pb-field">
                            <label class="pb-label">Minimum Order Quantity (MOQ) *</label>
                            <input type="number" min="1" name="moq" value="{{ old('moq', $product->moq) }}"
                                class="pb-input w-full @error('moq') pb-input-error @enderror"
                                placeholder="100" required />
                            @error('moq') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Price --}}
                        <div class="pb-field">
                            <label class="pb-label">Base Price (₦)</label>
                            @if ($product->exists)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-lg font-black text-slate-900">₦{{ number_format((float) $product->price, 2) }}</p>
                                    <a href="{{ route('admin.pricelist.products.edit', $product) }}" class="mt-1 inline-block text-xs font-black text-pink-600 hover:text-pink-700">Manage pricing in the Pricelist →</a>
                                </div>
                            @else
                                <p class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs font-semibold text-slate-500">Pricing can be set from the Pricelist once this product is created.</p>
                            @endif
                            <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <input type="checkbox" name="price_unavailable" value="1" @checked(old('price_unavailable', $product->price_unavailable)) class="mt-0.5 h-5 w-5 rounded border-amber-300 text-pink-600 focus:ring-pink-500">
                                <span>
                                    <span class="block text-sm font-black text-amber-900">Price not available</span>
                                    <span class="mt-0.5 block text-xs font-semibold text-amber-800">Customers will be sent to the quotation form instead of direct ordering.</span>
                                </span>
                            </label>
                        </div>

                        {{-- Short Description --}}
                        <div class="pb-field sm:col-span-2">
                            <label class="pb-label">Short Description *</label>
                            <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}"
                                class="pb-input w-full @error('short_description') pb-input-error @enderror"
                                placeholder="Brief description for product listings" required />
                            @error('short_description') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Full Description --}}
                        <div class="pb-field sm:col-span-2">
                            <label class="pb-label">Full Description *</label>
                            <textarea name="description" rows="5" data-rich-editor
                                class="pb-textarea w-full @error('description') pb-input-error @enderror"
                                placeholder="Detailed product description including features, benefits, and specifications..." required>{{ old('description', $product->description) }}</textarea>
                            @error('description') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Product Images Section --}}
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="pb-section-title">Product Images</h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="pb-field">
                            <label class="pb-label">Featured Image</label>
                            <livewire:uploads.secure-image-upload
                                :key="'product-featured-image-'.($product->id ?: 'create')"
                                input-name="featured_image_path"
                                directory="product-images/featured"
                                :max-size-kb="4096"
                                :max-files="1"
                                :multiple="false"
                                :initial-path="old('featured_image_path')"
                            />
                            <p class="text-xs text-slate-400 mt-1">One image for product cards and main product display.</p>
                            @error('featured_image') <span class="pb-field-error">{{ $message }}</span> @enderror
                            @error('featured_image_path') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="pb-field">
                            <label class="pb-label">Additional Images</label>
                            <livewire:uploads.secure-image-upload
                                :key="'product-gallery-images-'.($product->id ?: 'create')"
                                input-name="additional_image_paths"
                                directory="product-images/gallery"
                                :max-size-kb="4096"
                                :max-files="12"
                                :multiple="true"
                                :initial-paths="old('additional_image_paths', [])"
                            />
                            <p class="text-xs text-slate-400 mt-1">Upload multiple gallery images (up to 12).</p>
                            @error('additional_images') <span class="pb-field-error">{{ $message }}</span> @enderror
                            @error('additional_images.*') <span class="pb-field-error">{{ $message }}</span> @enderror
                            @error('additional_image_paths') <span class="pb-field-error">{{ $message }}</span> @enderror
                            @error('additional_image_paths.*') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if ($product->exists && ($product->featured_image || !empty($product->additional_images)))
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-3">Current Featured Image</p>
                                @if ($product->featuredImageUrl())
                                    <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}" class="h-40 w-full rounded-lg border border-slate-200 object-cover bg-white" />
                                    <label class="flex cursor-pointer items-center gap-3 mt-2">
                                        <input type="checkbox" name="remove_featured_image" value="1" class="checkbox checkbox-sm" @checked(old('remove_featured_image'))>
                                        <span class="text-sm text-slate-700">Remove featured image</span>
                                    </label>
                                @else
                                    <p class="text-sm font-semibold text-slate-500">No featured image uploaded yet.</p>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Current Gallery Images</p>
                                    @if (!empty($product->additional_images))
                                        <label class="flex cursor-pointer items-center gap-2">
                                            <input type="checkbox" name="remove_additional_images" value="1" class="checkbox checkbox-sm" @checked(old('remove_additional_images'))>
                                            <span class="text-xs text-slate-700">Clear gallery</span>
                                        </label>
                                    @endif
                                </div>

                                @if (!empty($product->additional_images))
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach ($product->additionalImageUrls() as $imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }} gallery image" class="h-20 w-full rounded-lg border border-slate-200 object-cover bg-white" />
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm font-semibold text-slate-500">No additional images uploaded yet.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Specifications Section --}}
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="pb-section-title">Product Specifications</h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Paper Type --}}
                        <div class="pb-field">
                            <label class="pb-label">Paper Type *</label>
                            <select name="paper_type" class="pb-select w-full @error('paper_type') pb-input-error @enderror" required>
                                <option value="">Select paper type</option>
                                @foreach ($paperTypeOptions as $paperType)
                                    <option value="{{ $paperType }}" @selected(old('paper_type', $product->paper_type) === $paperType)>{{ $paperType }}</option>
                                @endforeach
                            </select>
                            @error('paper_type') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Paper Size --}}
                        <div class="pb-field">
                            <label class="pb-label">Paper Size *</label>
                            <select name="paper_size" class="pb-select w-full @error('paper_size') pb-input-error @enderror" required>
                                <option value="">Select paper size</option>
                                @foreach ($paperSizeOptions as $paperSize)
                                    <option value="{{ $paperSize }}" @selected(old('paper_size', $product->paper_size) === $paperSize)>{{ $paperSize }}</option>
                                @endforeach
                            </select>
                            @error('paper_size') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Finishing --}}
                        <div class="pb-field">
                            <label class="pb-label">Finishing *</label>
                            <select name="finishing" class="pb-select w-full @error('finishing') pb-input-error @enderror" required>
                                <option value="">Select finishing</option>
                                @foreach ($finishingOptions as $finishing)
                                    <option value="{{ $finishing }}" @selected(old('finishing', $product->finishing) === $finishing)>{{ $finishing }}</option>
                                @endforeach
                            </select>
                            @error('finishing') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- Paper Density --}}
                        <div class="pb-field">
                            <label class="pb-label">Paper Density *</label>
                            <select name="paper_density" class="pb-select w-full @error('paper_density') pb-input-error @enderror" required>
                                <option value="">Select paper density</option>
                                @foreach ($paperDensityOptions as $paperDensity)
                                    <option value="{{ $paperDensity }}" @selected(old('paper_density', $product->paper_density) === $paperDensity)>{{ $paperDensity }}</option>
                                @endforeach
                            </select>
                            @error('paper_density') <span class="pb-field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Status Section --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-700">Product Status</p>
                            <p class="text-xs text-slate-500">Active products are visible to customers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-wrap gap-3 justify-end pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.products.index') }}" class="pb-btn pb-btn-md pb-btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $product->exists ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Quick Create Category Modal Trigger --}}
        <div class="mt-4 text-right">
            <livewire:admin.product-category-quick-create />
        </div>
    </div>
</main>

<script>
    (() => {
        const categorySelect = document.getElementById('product-category-select');
        const formatter = new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN',
            minimumFractionDigits: 2,
        });

        // Listen for category created event from Livewire
        window.addEventListener('product-category-created', (event) => {
            const rawDetail = event.detail ?? {};
            const detail = Array.isArray(rawDetail) ? (rawDetail[0] ?? {}) : rawDetail;
            const categoryId = String(detail.categoryId ?? '');
            const categoryName = detail.categoryName ?? '';

            if (!categorySelect || !categoryId || !categoryName) {
                return;
            }

            const existing = Array.from(categorySelect.options).find((option) => option.value === categoryId);

            if (!existing) {
                const option = document.createElement('option');
                option.value = categoryId;
                option.textContent = categoryName;
                categorySelect.appendChild(option);
            }

            categorySelect.value = categoryId;
            categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
        });

        // Naira input formatting
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
