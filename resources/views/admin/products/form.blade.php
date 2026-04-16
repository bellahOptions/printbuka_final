@extends('layouts.admin')

@section('title', ($product->exists ? 'Edit Product' : 'Create Product').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <a href="{{ route('admin.products.index') }}" class="text-sm font-black text-cyan-300 hover:text-cyan-200">Products</a>
                <h1 class="mt-3 text-4xl">{{ $product->exists ? 'Edit product.' : 'Create product.' }}</h1>
            </div>
            <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @if ($product->exists) @method('PUT') @endif
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-black">Name<input name="name" value="{{ old('name', $product->name) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <div class="text-sm font-black">
                        <label for="product-category-select">Category</label>
                        <select id="product-category-select" name="product_category_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Unassigned</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('product_category_id', $product->product_category_id) === $category->id)>{{ $category->parent ? $category->parent->name.' > '.$category->name : $category->name }}</option>
                            @endforeach
                        </select>
                        @error('product_category_id')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                        <livewire:admin.product-category-quick-create />
                    </div>
                    <label class="text-sm font-black">MOQ<input type="number" min="1" name="moq" value="{{ old('moq', $product->moq) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Price
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-black text-slate-500">₦</span>
                            <input id="product-price-input" type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}" required data-naira-input data-naira-preview-id="product-price-preview" class="min-h-12 w-full rounded-md border border-slate-200 px-4 pl-10 font-semibold">
                        </div>
                        <span id="product-price-preview" class="mt-2 block text-xs font-bold text-slate-500">₦0.00</span>
                        @error('price')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="text-sm font-black sm:col-span-2">Short Description<input name="short_description" value="{{ old('short_description', $product->short_description) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black sm:col-span-2">Description<textarea name="description" rows="5" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('description', $product->description) }}</textarea></label>
                    <label class="text-sm font-black">Paper Type
                        <select name="paper_type" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select paper type</option>
                            @foreach ($paperTypeOptions as $paperType)
                                <option value="{{ $paperType }}" @selected(old('paper_type', $product->paper_type) === $paperType)>{{ $paperType }}</option>
                            @endforeach
                        </select>
                        @error('paper_type')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="text-sm font-black">Paper Size
                        <select name="paper_size" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select paper size</option>
                            @foreach ($paperSizeOptions as $paperSize)
                                <option value="{{ $paperSize }}" @selected(old('paper_size', $product->paper_size) === $paperSize)>{{ $paperSize }}</option>
                            @endforeach
                        </select>
                        @error('paper_size')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="text-sm font-black">Finishing
                        <select name="finishing" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select finishing</option>
                            @foreach ($finishingOptions as $finishing)
                                <option value="{{ $finishing }}" @selected(old('finishing', $product->finishing) === $finishing)>{{ $finishing }}</option>
                            @endforeach
                        </select>
                        @error('finishing')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="text-sm font-black">Paper Density
                        <select name="paper_density" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select paper density</option>
                            @foreach ($paperDensityOptions as $paperDensity)
                                <option value="{{ $paperDensity }}" @selected(old('paper_density', $product->paper_density) === $paperDensity)>{{ $paperDensity }}</option>
                            @endforeach
                        </select>
                        @error('paper_density')<span class="mt-2 block text-xs font-bold text-pink-700">{{ $message }}</span>@enderror
                    </label>
                    <div class="sm:col-span-2 rounded-md border border-slate-200 p-5">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Option Pricing</p>
                        <p class="mt-2 text-xs font-bold text-slate-500">Use one option per line: Label|Extra price. Example: A3|5000</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="text-sm font-black">Size / Format Options<textarea name="size_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="A4|0&#10;A3|5000">{{ old('size_price_options', $optionLines['size_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Material Type Options<textarea name="material_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="Art Card 300gsm|0&#10;PVC|2500">{{ old('material_price_options', $optionLines['material_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Finish / Lamination Options<textarea name="finish_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="No Finish|0&#10;Gloss Lamination|1500">{{ old('finish_price_options', $optionLines['finish_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Paper Density Options<textarea name="density_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="300gsm|0&#10;350gsm|1200">{{ old('density_price_options', $optionLines['density_price_options'] ?? '') }}</textarea></label>
                            <label class="text-sm font-black">Delivery Options<textarea name="delivery_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="Pickup|0&#10;Deliver to address|3000">{{ old('delivery_price_options', $optionLines['delivery_price_options']) }}</textarea></label>
                        </div>
                    </div>
                    <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-black"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="h-5 w-5 rounded border-slate-300 text-pink-600"> Active</label>
                </div>
                <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Product</button>
            </form>
    </div>
    <script>
        (() => {
            const categorySelect = document.getElementById('product-category-select');
            const formatter = new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
            });

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
