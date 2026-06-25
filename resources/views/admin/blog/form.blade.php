@extends('layouts.admin')

@section('title', ($post->exists ? 'Edit Post' : 'New Post').' | Blog | Printbuka')

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.blog.index') }}"
           class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-400 transition-colors shrink-0">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
        </a>
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-pink-600 mb-0.5">Blog Management</p>
            <h1 class="text-2xl font-black text-slate-900">{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1>
        </div>
    </div>

    <form action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @if($post->exists) @method('PUT') @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="text-sm font-black text-red-700 mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-sm text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

            {{-- ── Left: main content ──────────────────────────── --}}
            <div class="space-y-5">

                {{-- Title --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <label class="block text-sm font-black text-slate-700 mb-2">
                        Title <span class="text-pink-600">*</span>
                    </label>
                    <input name="title"
                           value="{{ old('title', $post->title) }}"
                           required
                           placeholder="Your post title…"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-lg font-black text-slate-900 placeholder-slate-300 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">
                    @error('title')
                        <p class="mt-1.5 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Excerpt --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <label class="block text-sm font-black text-slate-700 mb-1">Excerpt</label>
                    <p class="text-xs text-slate-400 mb-3">A short summary shown on the blog listing page (max 500 chars).</p>
                    <textarea name="excerpt"
                              rows="3"
                              maxlength="500"
                              placeholder="Brief summary of this article…"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content editor --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <label class="block text-sm font-black text-slate-700 mb-1">
                        Content <span class="text-pink-600">*</span>
                    </label>
                    <p class="text-xs text-slate-400 mb-3">Use the toolbar to format text, insert links and embed images.</p>
                    <textarea id="content-editor"
                              name="content"
                              rows="18"
                              required
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Additional images --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <label class="block text-sm font-black text-slate-700 mb-1">Additional Images</label>
                    <p class="text-xs text-slate-400 mb-3">These are also appended into the post body. Max 10 files, 5 MB each.</p>
                    <label class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 rounded-xl py-8 cursor-pointer hover:border-pink-300 hover:bg-pink-50 transition-colors">
                        <x-heroicon-o-photo class="w-8 h-8 text-slate-300" />
                        <span class="text-sm font-bold text-slate-500">Click to select images</span>
                        <span class="text-xs text-slate-400">JPG, PNG, GIF, WebP</span>
                        <input type="file" name="additional_images_files[]" accept="image/*" multiple class="sr-only">
                    </label>
                    @error('additional_images_files')
                        <p class="mt-1.5 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror
                    @error('additional_images_files.*')
                        <p class="mt-1.5 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror

                    {{-- Existing additional images --}}
                    @if($post->exists && !empty($post->additional_images))
                        <div class="mt-5 pt-5 border-t border-slate-100">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-wide mb-3">Current Images</p>
                            <div class="grid gap-3 sm:grid-cols-3">
                                @foreach((array) $post->additional_images as $index => $path)
                                    @php
                                        $imgUrl = filter_var($path, FILTER_VALIDATE_URL)
                                            ? $path
                                            : \App\Support\MediaUrl::resolve($path);
                                    @endphp
                                    <div class="relative rounded-xl overflow-hidden border border-slate-200 group">
                                        <img src="{{ $imgUrl }}" alt="Additional image {{ $index + 1 }}"
                                             class="h-24 w-full object-cover">
                                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity">
                                            <input type="checkbox" name="remove_additional_images[]" value="{{ $index }}" class="sr-only peer">
                                            <span class="text-white text-xs font-black peer-checked:text-red-400">Remove</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Right: settings sidebar ─────────────────────── --}}
            <div class="space-y-5">

                {{-- Publish --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-black text-slate-700 mb-4">Publish Settings</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wide mb-1.5">Status</label>
                            <select name="status"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">
                                <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-pink-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wide mb-1.5">Publish Date</label>
                            <input type="datetime-local"
                                   name="published_at"
                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\\TH:i')) }}"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">
                            @error('published_at')
                                <p class="mt-1 text-xs text-pink-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wide mb-1.5">Slug</label>
                            <input name="slug"
                                   value="{{ old('slug', $post->slug) }}"
                                   placeholder="auto-generated"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 placeholder-slate-300 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">
                            <p class="mt-1 text-xs text-slate-400">Leave blank to auto-generate from title.</p>
                            @error('slug')
                                <p class="mt-1 text-xs text-pink-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col gap-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black py-3 rounded-xl transition-colors shadow-sm shadow-pink-200">
                            <x-heroicon-o-check class="w-4 h-4" />
                            {{ $post->exists ? 'Save Changes' : 'Publish Post' }}
                        </button>
                        <a href="{{ route('admin.blog.index') }}"
                           class="w-full inline-flex items-center justify-center text-sm font-bold text-slate-500 hover:text-slate-700 py-2 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Featured image --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-black text-slate-700 mb-4">Featured Image</p>

                    {{-- Existing preview --}}
                    @if($post->exists && $post->featuredImageUrl())
                        <div class="relative mb-4 rounded-xl overflow-hidden border border-slate-200">
                            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}"
                                 class="w-full h-36 object-cover">
                            <label class="absolute bottom-0 left-0 right-0 bg-black/60 py-2 px-3 flex items-center justify-between cursor-pointer">
                                <span class="text-white text-xs font-bold">Remove image</span>
                                <input type="checkbox" name="remove_featured_image" value="1"
                                       class="w-4 h-4 accent-pink-600" @checked(old('remove_featured_image'))>
                            </label>
                        </div>
                    @endif

                    {{-- Upload --}}
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-slate-200 rounded-xl py-6 cursor-pointer hover:border-pink-300 hover:bg-pink-50 transition-colors mb-3">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-slate-300" />
                        <span class="text-xs font-bold text-slate-500">Upload image file</span>
                        <input type="file" name="featured_image_file" accept="image/*" class="sr-only">
                    </label>
                    @error('featured_image_file')
                        <p class="mt-1 text-xs text-pink-600 font-bold">{{ $message }}</p>
                    @enderror

                    {{-- Or URL --}}
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wide mb-1.5">Or paste image URL</label>
                        <input name="featured_image"
                               value="{{ old('featured_image', filter_var($post->featured_image, FILTER_VALIDATE_URL) ? $post->featured_image : '') }}"
                               placeholder="https://…"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 transition">
                        @error('featured_image')
                            <p class="mt-1 text-xs text-pink-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Live preview link --}}
                @if($post->exists && $post->status === 'published')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3">
                        <x-heroicon-o-globe-alt class="w-5 h-5 text-emerald-500 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-emerald-700">Live on blog</p>
                            <p class="text-xs text-emerald-600 truncate">/blog/{{ $post->slug }}</p>
                        </div>
                        <a href="{{ route('blog.show', $post) }}" target="_blank"
                           class="shrink-0 text-emerald-600 hover:text-emerald-800 transition-colors">
                            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                        </a>
                    </div>
                @endif

            </div>
        </div>
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
            height: 480,
            convert_urls: false,
            skin: 'oxide',
            content_css: 'default',
        });
    }
</script>
@endsection
