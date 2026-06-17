@extends('layouts.admin')
@section('title', $product ? 'Edit: ' . $product->name : 'New Shop Product')
@section('content')

<div class="pb-page-header">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.shop-products.index') }}" class="text-slate-400 hover:text-slate-700 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <h1 class="pb-page-title">{{ $product ? 'Edit Product' : 'New Shop Product' }}</h1>
    </div>
    @if($product)
        <p class="pb-page-subtitle">Editing: <strong>{{ $product->name }}</strong></p>
    @endif
</div>

@if($errors->any())
    <div class="alert alert-error mb-6">
        <ul class="list-disc ml-4 text-sm font-bold">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ $product ? route('admin.shop-products.update', $product) : route('admin.shop-products.store') }}"
      method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($product) @method('PUT') @endif

    <div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">

        {{-- Main fields --}}
        <div class="space-y-6">

            {{-- Basic info --}}
            <div class="pb-card p-6">
                <h2 class="font-black text-slate-950 text-base mb-5">Basic Information</h2>
                <div class="space-y-4">
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Product Name *</span>
                        <input type="text" name="name" value="{{ old('name', $product?->name) }}"
                               class="input input-bordered border-slate-200 @error('name') input-error @enderror"
                               placeholder="e.g. Branded Mug" required />
                    </label>
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Slug (URL)</span>
                        <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}"
                               class="input input-bordered border-slate-200"
                               placeholder="auto-generated from name" />
                    </label>
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Short Description</span>
                        <input type="text" name="short_description" value="{{ old('short_description', $product?->short_description) }}"
                               class="input input-bordered border-slate-200" placeholder="One-line summary (max 500 chars)" maxlength="500" />
                    </label>
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Full Description</span>
                        <textarea name="description" rows="5" class="textarea textarea-bordered border-slate-200"
                                  placeholder="Detailed product description…">{{ old('description', $product?->description) }}</textarea>
                    </label>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="pb-card p-6">
                <h2 class="font-black text-slate-950 text-base mb-5">Pricing</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Regular Price (NGN) *</span>
                        <input type="number" name="price" value="{{ old('price', $product?->price) }}"
                               step="0.01" min="0"
                               class="input input-bordered border-slate-200 @error('price') input-error @enderror"
                               placeholder="0.00" required />
                    </label>
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Sale Price (NGN)</span>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product?->sale_price) }}"
                               step="0.01" min="0"
                               class="input input-bordered border-slate-200"
                               placeholder="Leave blank for no sale" />
                        <span class="label-text-alt text-slate-400 mt-1">Must be less than regular price to show as "on sale"</span>
                    </label>
                    <label class="form-control">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">SKU</span>
                        <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}"
                               class="input input-bordered border-slate-200" placeholder="Stock keeping unit" />
                    </label>
                </div>
            </div>

            {{-- Stock --}}
            <div class="pb-card p-6" x-data="{ manageStock: {{ old('manage_stock', $product?->manage_stock ? 'true' : 'false') }} }">
                <h2 class="font-black text-slate-950 text-base mb-5">Stock Management</h2>
                <label class="flex items-center gap-3 cursor-pointer mb-4">
                    <input type="checkbox" name="manage_stock" value="1" x-model="manageStock"
                           class="checkbox checkbox-primary" {{ old('manage_stock', $product?->manage_stock) ? 'checked' : '' }} />
                    <span class="font-bold text-slate-700">Track stock quantity</span>
                </label>
                <div x-show="manageStock" class="mt-2">
                    <label class="form-control max-w-xs">
                        <span class="label-text font-bold text-xs uppercase text-slate-500">Stock Quantity</span>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}"
                               min="0" class="input input-bordered border-slate-200" />
                    </label>
                </div>
            </div>

            {{-- Option groups (Alpine.js dynamic) --}}
            <div class="pb-card p-6" x-data="optionGroupManager({{ json_encode($existingGroups) }})">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="font-black text-slate-950 text-base">Product Options</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Add selectable options (Color, Size, etc.) that customers choose at checkout. Price modifiers are added to the base price.</p>
                    </div>
                    <button type="button" @click="addGroup()" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">
                        <x-heroicon-o-plus class="w-4 h-4" /> Add Group
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(group, gi) in groups" :key="gi">
                        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-1 grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-bold uppercase text-slate-500">Group Name *</label>
                                        <input type="text" :name="`option_groups[${gi}][name]`" x-model="group.name"
                                               placeholder="e.g. Color, Size, Material"
                                               class="input input-bordered border-slate-200 input-sm w-full mt-1" required />
                                    </div>
                                    <div class="flex items-end gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer pb-2">
                                            <input type="checkbox" :name="`option_groups[${gi}][is_required]`" value="1"
                                                   x-model="group.is_required" class="checkbox checkbox-sm checkbox-primary" />
                                            <span class="text-sm font-bold text-slate-700">Required</span>
                                        </label>
                                    </div>
                                </div>
                                <button type="button" @click="groups.splice(gi, 1)"
                                        class="btn btn-xs btn-ghost text-red-400 hover:text-red-600 shrink-0 mt-6">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="space-y-2 ml-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Options</p>
                                <template x-for="(option, oi) in group.options" :key="oi">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="`option_groups[${gi}][options][${oi}][name]`"
                                               x-model="option.name" placeholder="Option name"
                                               class="input input-bordered border-slate-200 input-sm flex-1" required />
                                        <div class="flex items-center gap-1 shrink-0">
                                            <span class="text-xs font-bold text-slate-400">+NGN</span>
                                            <input type="number" step="0.01"
                                                   :name="`option_groups[${gi}][options][${oi}][price_modifier]`"
                                                   x-model="option.price_modifier" placeholder="0"
                                                   class="input input-bordered border-slate-200 input-sm w-24" />
                                        </div>
                                        <label class="flex items-center gap-1 shrink-0 cursor-pointer" title="Available">
                                            <input type="checkbox" :name="`option_groups[${gi}][options][${oi}][is_available]`"
                                                   value="1" x-model="option.is_available" class="checkbox checkbox-xs" />
                                            <span class="text-xs text-slate-500">Avail.</span>
                                        </label>
                                        <button type="button" @click="group.options.splice(oi, 1)"
                                                class="btn btn-xs btn-ghost text-red-400 hover:text-red-600">
                                            <x-heroicon-o-x-mark class="w-3 h-3" />
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="group.options.push({name: '', price_modifier: 0, is_available: true})"
                                        class="btn btn-xs btn-ghost text-slate-500 hover:text-pink-600 font-black">
                                    <x-heroicon-o-plus class="w-3 h-3" /> Add option
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="groups.length === 0" class="text-center py-6 text-slate-400 text-sm">
                        No option groups yet. Click "Add Group" to let customers choose variants.
                    </div>
                </div>
            </div>

            <script>
            function optionGroupManager(existingGroups) {
                return {
                    groups: existingGroups.length ? existingGroups : [],
                    addGroup() {
                        this.groups.push({ name: '', is_required: true, options: [{ name: '', price_modifier: 0, is_available: true }] });
                    }
                }
            }
            </script>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Publish --}}
            <div class="pb-card p-5">
                <h2 class="font-black text-slate-950 text-sm mb-4">Publish Settings</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-primary"
                               {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }} />
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Active / Visible</p>
                            <p class="text-xs text-slate-400">Show this product in the shop</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-warning"
                               {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }} />
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Featured</p>
                            <p class="text-xs text-slate-400">Highlight in shop listing</p>
                        </div>
                    </label>
                </div>
                <div class="mt-5 space-y-2">
                    <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full">
                        {{ $product ? 'Update Product' : 'Create Product' }}
                    </button>
                    <a href="{{ route('admin.shop-products.index') }}" class="btn btn-ghost font-black text-slate-500 w-full">Cancel</a>
                </div>
            </div>

            {{-- Product image --}}
            <div class="pb-card p-5">
                <h2 class="font-black text-slate-950 text-sm mb-4">Product Image</h2>
                @if($product?->featuredImageUrl())
                    <div class="mb-3">
                        <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                             class="w-full h-40 object-cover rounded-xl border border-slate-200" />
                        <p class="text-xs text-slate-400 mt-1">Upload a new image to replace.</p>
                    </div>
                @endif
                <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp"
                       class="file-input file-input-bordered border-slate-200 w-full text-sm" />
                <p class="text-xs text-slate-400 mt-2">JPG, PNG or WebP · max 4 MB</p>
            </div>

        </div>
    </div>
</form>
@endsection
