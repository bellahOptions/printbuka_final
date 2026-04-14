@extends('layouts.theme')

@section('title', ($category->exists ? 'Edit Category' : 'Create Category').' | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8"><a href="{{ route('admin.product-categories.index') }}" class="text-sm font-black text-cyan-300">Categories</a><h1 class="mt-3 text-4xl">{{ $category->exists ? 'Edit category.' : 'Create category.' }}</h1></div>
            <form action="{{ $category->exists ? route('admin.product-categories.update', $category) : route('admin.product-categories.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @if ($category->exists) @method('PUT') @endif
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-black">Name<input name="name" value="{{ old('name', $category->name) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Slug<input name="slug" value="{{ old('slug', $category->slug) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Tag<input name="tag" value="{{ old('tag', $category->tag) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black">Image URL<input name="image" value="{{ old('image', $category->image) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                    <label class="text-sm font-black sm:col-span-2">Description<textarea name="description" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('description', $category->description) }}</textarea></label>
                    <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-black"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="h-5 w-5 rounded border-slate-300 text-pink-600"> Active</label>
                </div>
                <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Category</button>
            </form>
        </section>
    </main>
@endsection
