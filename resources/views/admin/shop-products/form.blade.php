@extends('layouts.admin')
@section('title', $product ? 'Edit: ' . $product->name : 'New Shop Product')
@section('content')

<div class="pb-page-header">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.shop-products.index') }}" class="text-slate-400 hover:text-slate-700 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h1 class="pb-page-title">{{ $product ? 'Edit Product' : 'New Shop Product' }}</h1>
            @if($product)
                <p class="pb-page-subtitle">{{ $product->sku ? 'SKU: '.$product->sku.' · ' : '' }}{{ $product->is_active ? 'Active' : 'Inactive' }}</p>
            @endif
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error mb-6">
        <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0" />
        <ul class="list-disc ml-3 text-sm font-bold space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ $product ? route('admin.shop-products.update', $product) : route('admin.shop-products.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($product) @method('PUT') @endif

    <div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

        {{-- ── MAIN COLUMN ── --}}
        <div class="space-y-5">

            {{-- 1. Basic Info --}}
            <div class="pb-card overflow-hidden">
                <div class="h-0.5 bg-gradient-to-r from-pink-500 to-rose-400"></div>
                <div class="p-6">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                        <x-heroicon-o-document-text class="w-4 h-4 text-pink-500" /> Basic Information
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product?->name) }}"
                                   id="product-name"
                                   class="input input-bordered border-slate-200 w-full @error('name') input-error @enderror"
                                   placeholder="e.g. Custom Branded Mug" required />
                            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">
                                Slug (URL)
                                <span class="text-slate-400 normal-case font-medium ml-1">— auto-generated from name</span>
                            </label>
                            <input type="text" name="slug" id="product-slug" value="{{ old('slug', $product?->slug) }}"
                                   class="input input-bordered border-slate-200 w-full font-mono text-sm"
                                   placeholder="auto-generated-from-name" />
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Short Description</label>
                            <input type="text" name="short_description"
                                   value="{{ old('short_description', $product?->short_description) }}"
                                   class="input input-bordered border-slate-200 w-full"
                                   placeholder="One-line summary shown in listings (max 500 chars)"
                                   maxlength="500" />
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Full Description</label>
                            <textarea name="description" rows="5"
                                      class="textarea textarea-bordered border-slate-200 w-full"
                                      placeholder="Detailed product information, dimensions, materials…">{{ old('description', $product?->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Pricing & SKU --}}
            <div class="pb-card overflow-hidden">
                <div class="h-0.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                <div class="p-6">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-4 h-4 text-emerald-500" /> Pricing & SKU
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Regular Price (NGN) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">₦</span>
                                <input type="number" name="price" value="{{ old('price', $product?->price) }}"
                                       step="0.01" min="0"
                                       class="input input-bordered border-slate-200 w-full pl-8 @error('price') input-error @enderror"
                                       placeholder="0.00" required />
                            </div>
                            @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Sale Price (NGN)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">₦</span>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product?->sale_price) }}"
                                       step="0.01" min="0"
                                       class="input input-bordered border-slate-200 w-full pl-8"
                                       placeholder="Leave blank — no sale" />
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Must be less than regular price to activate "On Sale" badge</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}"
                                   class="input input-bordered border-slate-200 w-full font-mono"
                                   placeholder="{{ $product ? 'Leave blank to keep current' : 'Auto-generated if left blank (PBK-YYYY-NNNNN)' }}" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Stock Management --}}
            <div class="pb-card overflow-hidden" x-data="{ manageStock: {{ old('manage_stock', $product?->manage_stock) ? 'true' : 'false' }} }">
                <div class="h-0.5 bg-gradient-to-r from-amber-500 to-orange-400"></div>
                <div class="p-6">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
                        <x-heroicon-o-archive-box class="w-4 h-4 text-amber-500" /> Stock Management
                    </h2>
                    <label class="flex items-center gap-3 cursor-pointer mb-4 group">
                        <input type="checkbox" name="manage_stock" value="1" x-model="manageStock"
                               class="checkbox checkbox-warning"
                               {{ old('manage_stock', $product?->manage_stock) ? 'checked' : '' }} />
                        <div>
                            <p class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition-colors">Track stock quantity</p>
                            <p class="text-xs text-slate-400">When enabled, orders will be blocked if stock runs out</p>
                        </div>
                    </label>
                    <div x-show="manageStock" x-transition class="pl-1">
                        <label class="text-xs font-bold uppercase text-slate-500 block mb-1.5">Stock Quantity</label>
                        <input type="number" name="stock_quantity"
                               value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}"
                               min="0"
                               class="input input-bordered border-slate-200 w-full max-w-xs" />
                        <p class="text-xs text-slate-400 mt-1">Product-level stock — option-level stock is set below</p>
                    </div>
                    <div x-show="!manageStock" class="text-sm text-slate-400 italic">
                        Unlimited stock — customers can always order this product.
                    </div>
                </div>
            </div>

            {{-- 4. Product Options --}}
            <div class="pb-card overflow-hidden" x-data="optionGroupManager({{ json_encode($existingGroups) }})">
                <div class="h-0.5 bg-gradient-to-r from-violet-500 to-purple-400"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-1 flex items-center gap-2">
                                <x-heroicon-o-adjustments-horizontal class="w-4 h-4 text-violet-500" /> Product Options
                            </h2>
                            <p class="text-xs text-slate-400 max-w-sm">Add selectable variants (Color, Size, Material) that customers choose at checkout. Price modifiers are added to the base price.</p>
                        </div>
                        <button type="button" @click="addGroup()"
                                class="btn btn-sm btn-outline font-black border-violet-200 text-violet-700 hover:border-violet-500 hover:bg-violet-50 shrink-0">
                            <x-heroicon-o-plus class="w-4 h-4" /> Add Group
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(group, gi) in groups" :key="gi">
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                {{-- Group header --}}
                                <div class="bg-slate-50 px-4 py-3 flex items-center gap-3 border-b border-slate-100">
                                    <input type="hidden" :name="`option_groups[${gi}][id]`" :value="group.id ?? ''" />
                                    <input type="text" :name="`option_groups[${gi}][name]`" x-model="group.name"
                                           placeholder="Group name, e.g. Color, Size, Material"
                                           class="input input-bordered border-slate-200 input-sm flex-1 font-bold"
                                           required />
                                    <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                        <input type="checkbox" :name="`option_groups[${gi}][is_required]`" value="1"
                                               x-model="group.is_required" class="checkbox checkbox-sm checkbox-primary" />
                                        <span class="text-xs font-bold text-slate-600">Required</span>
                                    </label>
                                    <button type="button" @click="groups.splice(gi, 1)"
                                            class="btn btn-xs btn-ghost text-red-400 hover:text-red-600 hover:bg-red-50 shrink-0"
                                            title="Remove group">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>

                                {{-- Options within group --}}
                                <div class="p-4 space-y-2">
                                    <template x-for="(option, oi) in group.options" :key="oi">
                                        <div class="border border-slate-100 rounded-lg bg-white p-3">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <input type="hidden" :name="`option_groups[${gi}][options][${oi}][id]`" :value="option.id ?? ''" />
                                                <input type="text"
                                                       :name="`option_groups[${gi}][options][${oi}][name]`"
                                                       x-model="option.name"
                                                       placeholder="e.g. Red, Large, Matte"
                                                       class="input input-bordered border-slate-200 input-sm flex-1 min-w-[120px]"
                                                       required />
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <span class="text-xs font-bold text-slate-500">+₦</span>
                                                    <input type="number" step="0.01"
                                                           :name="`option_groups[${gi}][options][${oi}][price_modifier]`"
                                                           x-model="option.price_modifier"
                                                           placeholder="0"
                                                           class="input input-bordered border-slate-200 input-sm w-24"
                                                           title="Price modifier (add to base price)" />
                                                </div>
                                                <label class="flex items-center gap-1.5 shrink-0 cursor-pointer" title="Mark as available">
                                                    <input type="checkbox"
                                                           :name="`option_groups[${gi}][options][${oi}][is_available]`"
                                                           value="1" x-model="option.is_available"
                                                           class="checkbox checkbox-xs checkbox-success" />
                                                    <span class="text-xs text-slate-500 font-bold">Avail.</span>
                                                </label>
                                                <button type="button" @click="group.options.splice(oi, 1)"
                                                        class="btn btn-xs btn-ghost text-red-400 hover:text-red-600 hover:bg-red-50 shrink-0">
                                                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-400 w-16 shrink-0">Stock qty</span>
                                                    <input type="number" min="0"
                                                           :name="`option_groups[${gi}][options][${oi}][stock_quantity]`"
                                                           x-model="option.stock_quantity"
                                                           placeholder="∞ unlimited"
                                                           class="input input-bordered border-slate-200 input-sm w-28"
                                                           title="Leave blank for unlimited stock" />
                                                </div>
                                                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                                                    <span class="text-xs font-bold text-slate-400 w-16 shrink-0">Image</span>
                                                    <input type="text"
                                                           :name="`option_groups[${gi}][options][${oi}][image]`"
                                                           x-model="option.image"
                                                           placeholder="Cloudinary ID or URL (optional)"
                                                           class="input input-bordered border-slate-200 input-sm w-full" />
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <button type="button"
                                            @click="group.options.push({ id: null, name: '', price_modifier: 0, is_available: true, stock_quantity: null, image: '' })"
                                            class="btn btn-xs btn-ghost text-violet-600 hover:text-violet-800 hover:bg-violet-50 font-black w-full border border-dashed border-violet-200 mt-1">
                                        <x-heroicon-o-plus class="w-3 h-3" /> Add option
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="groups.length === 0"
                             class="border-2 border-dashed border-slate-200 rounded-xl py-10 text-center">
                            <x-heroicon-o-adjustments-horizontal class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                            <p class="text-sm font-bold text-slate-500">No option groups yet</p>
                            <p class="text-xs text-slate-400 mt-1">Click "Add Group" to let customers select variants like Color or Size.</p>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function optionGroupManager(existingGroups) {
                return {
                    groups: existingGroups.length ? existingGroups : [],
                    addGroup() {
                        this.groups.push({
                            id: null, name: '', is_required: true,
                            options: [{ id: null, name: '', price_modifier: 0, is_available: true, stock_quantity: null, image: '' }]
                        });
                    }
                }
            }

            // Auto-generate slug from product name
            (function () {
                const nameEl = document.getElementById('product-name');
                const slugEl = document.getElementById('product-slug');
                if (!nameEl || !slugEl) return;
                let userEdited = slugEl.value !== '';
                slugEl.addEventListener('input', () => { userEdited = true; });
                nameEl.addEventListener('input', () => {
                    if (userEdited) return;
                    slugEl.value = nameEl.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim()
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                });
            })();
            </script>

        </div>
        {{-- end main column --}}

        {{-- ── SIDEBAR ── --}}
        <div class="space-y-4">

            {{-- Publish / Save --}}
            <div class="pb-card overflow-hidden">
                <div class="h-0.5 bg-gradient-to-r from-slate-700 to-slate-500"></div>
                <div class="p-5">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-heroicon-o-rocket-launch class="w-4 h-4 text-slate-500" /> Publish Settings
                    </h2>
                    <div class="space-y-3 mb-5">
                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-primary"
                                   {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }} />
                            <div>
                                <p class="font-bold text-slate-900 text-sm">Active / Visible</p>
                                <p class="text-xs text-slate-400">Show this product in the shop</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-warning"
                                   {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }} />
                            <div>
                                <p class="font-bold text-slate-900 text-sm">Featured Product</p>
                                <p class="text-xs text-slate-400">Highlighted in shop listing and homepage</p>
                            </div>
                        </label>
                    </div>
                    <button type="submit"
                            class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full mb-2">
                        <x-heroicon-o-check class="w-4 h-4" />
                        {{ $product ? 'Save Changes' : 'Create Product' }}
                    </button>
                    <a href="{{ route('admin.shop-products.index') }}"
                       class="btn btn-ghost font-black text-slate-500 w-full text-sm">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Product Images --}}
            <div class="pb-card overflow-hidden">
                <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
                <div class="p-5 space-y-5">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                        <x-heroicon-o-photo class="w-4 h-4 text-pink-500" /> Product Images
                    </h2>

                    {{-- Featured Image --}}
                    <div>
                        <p class="text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Featured Image</p>
                        <livewire:uploads.secure-image-upload
                            :key="'shop-featured-'.($product?->id ?: 'create')"
                            input-name="featured_image_path"
                            directory="shop-products/featured"
                            :max-size-kb="4096"
                            :max-files="1"
                            :multiple="false"
                            :initial-path="old('featured_image_path')"
                        />
                        <p class="text-xs text-slate-400 mt-1.5">JPG, PNG or WebP · max 4 MB · used on product cards</p>
                        @error('featured_image') <p class="text-xs text-pink-600 mt-1">{{ $message }}</p> @enderror
                        @error('featured_image_path') <p class="text-xs text-pink-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Additional Images --}}
                    <div>
                        <p class="text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Gallery Images</p>
                        <livewire:uploads.secure-image-upload
                            :key="'shop-gallery-'.($product?->id ?: 'create')"
                            input-name="additional_image_paths"
                            directory="shop-products/gallery"
                            :max-size-kb="4096"
                            :max-files="10"
                            :multiple="true"
                            :initial-paths="old('additional_image_paths', [])"
                        />
                        <p class="text-xs text-slate-400 mt-1.5">Up to 10 images · shown in the product image slider</p>
                        @error('additional_images') <p class="text-xs text-pink-600 mt-1">{{ $message }}</p> @enderror
                        @error('additional_image_paths') <p class="text-xs text-pink-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Existing images (edit mode) --}}
                    @if($product?->exists && ($product->featured_image || !empty($product->additional_images)))
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wide">Current Images</p>

                            @if($product->featuredImageUrl())
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-xs font-bold text-slate-500 mb-2">Featured</p>
                                    <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                         class="h-32 w-full rounded-lg border border-slate-200 object-cover bg-white" />
                                    <label class="flex cursor-pointer items-center gap-2 mt-2">
                                        <input type="checkbox" name="remove_featured_image" value="1" class="checkbox checkbox-sm checkbox-error" @checked(old('remove_featured_image'))>
                                        <span class="text-xs text-slate-600">Remove featured image</span>
                                    </label>
                                </div>
                            @endif

                            @if(!empty($product->additional_images))
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-bold text-slate-500">Gallery ({{ count($product->additional_images) }})</p>
                                        <label class="flex cursor-pointer items-center gap-1.5">
                                            <input type="checkbox" name="remove_additional_images" value="1" class="checkbox checkbox-sm checkbox-error" @checked(old('remove_additional_images'))>
                                            <span class="text-xs text-slate-600">Clear gallery</span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5">
                                        @foreach($product->additionalImageUrls() as $imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                                                 class="h-16 w-full rounded-lg border border-slate-200 object-cover bg-white" />
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            {{-- Quick stats (edit mode only) --}}
            @if($product)
            <div class="pb-card p-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-slate-400" /> Product Stats
                </h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Views</span>
                        <span class="font-black text-slate-900">{{ number_format((int)$product->view_count) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Created</span>
                        <span class="font-black text-slate-700">{{ $product->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Stock</span>
                        <span class="font-black {{ $product->isInStock() ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $product->manage_stock ? ($product->stock_quantity ?? 0).' units' : 'Unlimited' }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
                       class="btn btn-xs btn-ghost text-slate-500 hover:text-pink-600 font-black w-full">
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" /> View on site
                    </a>
                </div>
            </div>
            @endif

        </div>
        {{-- end sidebar --}}

    </div>
</form>
@endsection
