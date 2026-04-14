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
                    <label class="text-sm font-black">Category<select name="product_category_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Unassigned</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((int) old('product_category_id', $product->product_category_id) === $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                    <label class="text-sm font-black">MOQ<input type="number" min="1" name="moq" value="{{ old('moq', $product->moq) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Price<input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black sm:col-span-2">Short Description<input name="short_description" value="{{ old('short_description', $product->short_description) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black sm:col-span-2">Description<textarea name="description" rows="5" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('description', $product->description) }}</textarea></label>
                    <label class="text-sm font-black">Paper Type<input name="paper_type" value="{{ old('paper_type', $product->paper_type) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Paper Size<input name="paper_size" value="{{ old('paper_size', $product->paper_size) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Finishing<input name="finishing" value="{{ old('finishing', $product->finishing) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Paper Density<input name="paper_density" value="{{ old('paper_density', $product->paper_density) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <div class="sm:col-span-2 rounded-md border border-slate-200 p-5">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Option Pricing</p>
                        <p class="mt-2 text-xs font-bold text-slate-500">Use one option per line: Label|Extra price. Example: A3|5000</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="text-sm font-black">Size / Format Options<textarea name="size_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="A4|0&#10;A3|5000">{{ old('size_price_options', $optionLines['size_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Material Type Options<textarea name="material_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="Art Card 300gsm|0&#10;PVC|2500">{{ old('material_price_options', $optionLines['material_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Finish / Lamination Options<textarea name="finish_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="No Finish|0&#10;Gloss Lamination|1500">{{ old('finish_price_options', $optionLines['finish_price_options']) }}</textarea></label>
                            <label class="text-sm font-black">Delivery Options<textarea name="delivery_price_options" rows="6" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" placeholder="Pickup|0&#10;Deliver to address|3000">{{ old('delivery_price_options', $optionLines['delivery_price_options']) }}</textarea></label>
                        </div>
                    </div>
                    <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-black"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="h-5 w-5 rounded border-slate-300 text-pink-600"> Active</label>
                </div>
                <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Product</button>
            </form>
    </div>
@endsection
