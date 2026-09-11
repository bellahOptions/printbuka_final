@extends('layouts.admin')

@section('title', ($category->exists ? 'Edit Category' : 'Create Category').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
            <div class="pb-page-header">
                <div>
                    <a href="{{ route('admin.product-categories.index') }}" class="text-sm font-black text-pink-700 hover:text-pink-800">Categories</a>
                    <h1 class="pb-page-title">{{ $category->exists ? 'Edit Category' : 'Create Category' }}</h1>
                </div>
            </div>
            <form action="{{ $category->exists ? route('admin.product-categories.update', $category) : route('admin.product-categories.store') }}" method="POST" class="pb-card p-6">
                @csrf
                @if ($category->exists) @method('PUT') @endif
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Parent Category</label>
                        <select name="parent_id" class="pb-select w-full">
                            <option value="">None (Top-level)</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((int) old('parent_id', $category->parent_id) === $parentCategory->id)>{{ $parentCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Name</label>
                        <input name="name" value="{{ old('name', $category->name) }}" required class="pb-input w-full">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Slug</label>
                        <input name="slug" value="{{ old('slug', $category->slug) }}" class="pb-input w-full">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Tag</label>
                        <input name="tag" value="{{ old('tag', $category->tag) }}" class="pb-input w-full">
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Image URL</label>
                        <input name="image" value="{{ old('image', $category->image) }}" class="pb-input w-full">
                    </div>
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Description</label>
                        <textarea name="description" rows="5" data-rich-editor class="pb-textarea w-full">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 text-sm font-black">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="h-5 w-5 rounded border-slate-300 text-pink-600"> Active
                    </label>
                </div>
                <button class="pb-btn pb-btn-md pb-btn-primary mt-6">Save Category</button>
            </form>
    </div>
@endsection
