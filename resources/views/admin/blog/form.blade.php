@extends('layouts.admin')

@section('title', ($post->exists ? 'Edit Blog Post' : 'Create Blog Post').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8"><a href="{{ route('admin.blog.index') }}" class="text-sm font-black text-cyan-300">Blog</a><h1 class="mt-3 text-4xl">{{ $post->exists ? 'Edit post.' : 'Create post.' }}</h1></div>
        <form action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($post->exists) @method('PUT') @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black sm:col-span-2">Title<input name="title" value="{{ old('title', $post->title) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Slug<input name="slug" value="{{ old('slug', $post->slug) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Status<select name="status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option><option value="published" @selected(old('status', $post->status) === 'published')>Published</option></select></label>
                <label class="text-sm font-black">Featured Image URL<input name="featured_image" value="{{ old('featured_image', $post->featured_image) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Published At<input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black sm:col-span-2">Excerpt<input name="excerpt" value="{{ old('excerpt', $post->excerpt) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black sm:col-span-2">Content<textarea name="content" rows="10" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('content', $post->content) }}</textarea></label>
            </div>
            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Post</button>
        </form>
    </div>
@endsection
