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
                    <h1 class="text-3xl font-bold text-slate-900">{{ $product->exists ? 'Edit Product' : 'Create New Product' }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $product->exists ? 'Update product details, pricing, and options.' : 'Add a new product to your catalog.' }}
                    </p>
                </div>
                <div class="flex gap-3">
                    @if($product->exists)
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline btn-pink-600">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="card bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="p-6 sm:p-8">
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
                        <h2 class="text-lg font-bold text-slate-900">Basic Information</h2>
                    </div>
                    
                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Product Name --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Product Name *</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                                class="input input-bordered w-full focus:input-primary @error('name') input-error @enderror"
                                placeholder="e.g., Premium Business Cards" required />
                            @error('name') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Category</span>
                            </label>
                            <select name="product_category_id" id="product-category-select" 
                                class="select select-bordered w-full focus:select-primary">
                                <option value="">Unassigned</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('product_category_id', $product->product_category_id) === $category->id)>
                                        {{ $category->parent ? $category->parent->name.' > '.$category->name : $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_id') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- MOQ --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Minimum Order Quantity (MOQ) *</span>
                            </label>
                            <input type="number" min="1" name="moq" value="{{ old('moq', $product->moq) }}" 
                                class="input input-bordered w-full focus:input-primary @error('moq') input-error @enderror"
                                placeholder="100" required />
                            @error('moq') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Price --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Base Price (₦) *</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">₦</span>
                                <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}" 
                                    id="product-price-input"
                                    data-naira-input data-naira-preview-id="product-price-preview"
                                    class="input input-bordered w-full pl-10 focus:input-primary @error('price') input-error @enderror"
                                    placeholder="0.00" required />
                            </div>
                            <span id="product-price-preview" class="mt-2 text-xs font-semibold text-slate-500">₦0.00</span>
                            @error('price') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Short Description --}}
                        <div class="form-control w-full sm:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Short Description *</span>
                            </label>
                            <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" 
                                class="input input-bordered w-full focus:input-primary @error('short_description') input-error @enderror"
                                placeholder="Brief description for product listings" required />
                            @error('short_description') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Full Description --}}
                        <div class="form-control w-full sm:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Full Description *</span>
                            </label>
                            <textarea name="description" rows="5" 
                                class="textarea textarea-bordered w-full focus:textarea-primary @error('description') textarea-error @enderror"
                                placeholder="Detailed product description including features, benefits, and specifications..." required>{{ old('description', $product->description) }}</textarea>
                            @error('description') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Specifications Section --}}
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-cyan-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Product Specifications</h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Paper Type --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Paper Type *</span>
                            </label>
                            <select name="paper_type" class="select select-bordered w-full focus:select-primary @error('paper_type') select-error @enderror" required>
                                <option value="">Select paper type</option>
                                @foreach ($paperTypeOptions as $paperType)
                                    <option value="{{ $paperType }}" @selected(old('paper_type', $product->paper_type) === $paperType)>{{ $paperType }}</option>
                                @endforeach
                            </select>
                            @error('paper_type') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Paper Size --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Paper Size *</span>
                            </label>
                            <select name="paper_size" class="select select-bordered w-full focus:select-primary @error('paper_size') select-error @enderror" required>
                                <option value="">Select paper size</option>
                                @foreach ($paperSizeOptions as $paperSize)
                                    <option value="{{ $paperSize }}" @selected(old('paper_size', $product->paper_size) === $paperSize)>{{ $paperSize }}</option>
                                @endforeach
                            </select>
                            @error('paper_size') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Finishing --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Finishing *</span>
                            </label>
                            <select name="finishing" class="select select-bordered w-full focus:select-primary @error('finishing') select-error @enderror" required>
                                <option value="">Select finishing</option>
                                @foreach ($finishingOptions as $finishing)
                                    <option value="{{ $finishing }}" @selected(old('finishing', $product->finishing) === $finishing)>{{ $finishing }}</option>
                                @endforeach
                            </select>
                            @error('finishing') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Paper Density --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Paper Density *</span>
                            </label>
                            <select name="paper_density" class="select select-bordered w-full focus:select-primary @error('paper_density') select-error @enderror" required>
                                <option value="">Select paper density</option>
                                @foreach ($paperDensityOptions as $paperDensity)
                                    <option value="{{ $paperDensity }}" @selected(old('paper_density', $product->paper_density) === $paperDensity)>{{ $paperDensity }}</option>
                                @endforeach
                            </select>
                            @error('paper_density') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Option Pricing Section --}}
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Option Pricing</h2>
                    </div>
                    
                    <div class="alert bg-blue-50 border-blue-200 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-blue-800 text-sm">Use one option per line: <strong>Label|Extra price</strong>. Example: A3|5000</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Size Options --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Size / Format Options</span>
                            </label>
                            <textarea name="size_price_options" rows="6" 
                                class="textarea textarea-bordered w-full font-mono text-sm"
                                placeholder="A4|0&#10;A3|5000&#10;A2|10000">{{ old('size_price_options', $optionLines['size_price_options'] ?? '') }}</textarea>
                        </div>

                        {{-- Material Options --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Material Type Options</span>
                            </label>
                            <textarea name="material_price_options" rows="6" 
                                class="textarea textarea-bordered w-full font-mono text-sm"
                                placeholder="Art Card 300gsm|0&#10;PVC|2500&#10;Foil Paper|5000">{{ old('material_price_options', $optionLines['material_price_options'] ?? '') }}</textarea>
                        </div>

                        {{-- Finish Options --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Finish / Lamination Options</span>
                            </label>
                            <textarea name="finish_price_options" rows="6" 
                                class="textarea textarea-bordered w-full font-mono text-sm"
                                placeholder="No Finish|0&#10;Gloss Lamination|1500&#10;Matte Lamination|1500">{{ old('finish_price_options', $optionLines['finish_price_options'] ?? '') }}</textarea>
                        </div>

                        {{-- Density Options --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Paper Density Options</span>
                            </label>
                            <textarea name="density_price_options" rows="6" 
                                class="textarea textarea-bordered w-full font-mono text-sm"
                                placeholder="300gsm|0&#10;350gsm|1200&#10;400gsm|2500">{{ old('density_price_options', $optionLines['density_price_options'] ?? '') }}</textarea>
                        </div>

                        {{-- Delivery Options (Full width on mobile, half on desktop) --}}
                        <div class="form-control sm:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold text-slate-700">Delivery Options</span>
                            </label>
                            <textarea name="delivery_price_options" rows="4" 
                                class="textarea textarea-bordered w-full font-mono text-sm"
                                placeholder="Pickup|0&#10;Deliver to address|3000&#10;Express Delivery|5000">{{ old('delivery_price_options', $optionLines['delivery_price_options'] ?? '') }}</textarea>
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
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-slate-600">
                        Cancel
                    </a>
                    <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-md shadow-pink-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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