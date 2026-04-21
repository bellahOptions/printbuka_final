@extends('layouts.admin')

@section('title', ($post->exists ? 'Edit Blog Post' : 'Create Blog Post').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
            <a href="{{ route('admin.blog.index') }}" class="text-sm font-black text-cyan-300">Blog</a>
            <h1 class="mt-3 text-4xl">{{ $post->exists ? 'Edit post.' : 'Create post.' }}</h1>
        </div>

        <form action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($post->exists) @method('PUT') @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black sm:col-span-2">
                    Title
                    <input name="title" value="{{ old('title', $post->title) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    @error('title') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black">
                    Slug
                    <input name="slug" value="{{ old('slug', $post->slug) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    @error('slug') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black">
                    Status
                    <select name="status" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                    </select>
                    @error('status') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black">
                    Featured Image URL (optional)
                    <input name="featured_image" value="{{ old('featured_image', filter_var($post->featured_image, FILTER_VALIDATE_URL) ? $post->featured_image : '') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    @error('featured_image') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black">
                    Featured Image Upload
                    <input type="file" name="featured_image_file" accept="image/*" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">
                    @error('featured_image_file') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black">
                    Published At
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    @error('published_at') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black sm:col-span-2">
                    Excerpt
                    <input name="excerpt" value="{{ old('excerpt', $post->excerpt) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    @error('excerpt') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black sm:col-span-2">
                    Content (HTML editor)
                    <textarea id="content-editor" name="content" rows="14" required class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('content', $post->content) }}</textarea>
                    <span class="mt-2 block text-xs font-semibold text-slate-500">Use the editor toolbar to format text and insert image URLs directly into the post body.</span>
                    @error('content') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-black sm:col-span-2">
                    Additional Images (also appended into content)
                    <input type="file" name="additional_images_files[]" accept="image/*" multiple class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">
                    @error('additional_images_files') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                    @error('additional_images_files.*') <span class="mt-1 block text-xs text-pink-600">{{ $message }}</span> @enderror
                </label>

                @if ($post->exists && $post->featuredImageUrl())
                    <div class="rounded-md border border-slate-200 p-4 sm:col-span-2">
                        <p class="text-sm font-black text-slate-700">Current Featured Image</p>
                        <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="mt-3 h-40 w-full rounded-md object-cover">
                        <label class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-slate-600">
                            <input type="checkbox" name="remove_featured_image" value="1" class="checkbox checkbox-sm" @checked(old('remove_featured_image'))>
                            Remove featured image
                        </label>
                    </div>
                @endif

                @if ($post->exists && ! empty($post->additional_images))
                    <div class="rounded-md border border-slate-200 p-4 sm:col-span-2">
                        <p class="text-sm font-black text-slate-700">Current Additional Images</p>
                        <div class="mt-3 grid gap-4 sm:grid-cols-3">
                            @foreach ((array) $post->additional_images as $index => $path)
                                @php
                                    $url = filter_var($path, FILTER_VALIDATE_URL)
                                        ? $path
                                        : \App\Support\MediaUrl::resolve($path);
                                @endphp
                                <div class="rounded-md border border-slate-200 p-2">
                                    <img src="{{ $url }}" alt="Additional blog image" class="h-24 w-full rounded object-cover">
                                    <label class="mt-2 inline-flex items-center gap-2 text-xs font-bold text-slate-600">
                                        <input type="checkbox" name="remove_additional_images[]" value="{{ $index }}" class="checkbox checkbox-sm">
                                        Remove
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Post</button>
        </form>
    </div>

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        if (window.tinymce) {
            tinymce.init({
                selector: '#content-editor',
                menubar: false,
                branding: false,
                plugins: 'lists link image table code fullscreen',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code fullscreen',
                height: 420,
                convert_urls: false,
            });
        }
    </script>
@endsection
