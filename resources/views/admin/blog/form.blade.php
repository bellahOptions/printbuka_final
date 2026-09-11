@extends('layouts.admin')

@section('title', ($post->exists ? 'Edit Post' : 'New Post').' | Blog | Printbuka')

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.blog.index') }}"
           class="pb-btn pb-btn-icon pb-btn-outline shrink-0">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 mb-0.5">Blog Management</p>
            <h1 class="pb-page-title">{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1>
        </div>
    </div>

    <form action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @if($post->exists) @method('PUT') @endif

        @if($errors->any())
            <div class="pb-alert pb-alert-error mb-6 flex-col items-start">
                <p class="font-semibold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

            {{-- ── Left: main content ──────────────────────────── --}}
            <div class="space-y-5">

                {{-- Title --}}
                <div class="pb-card p-6">
                    <div class="pb-field">
                        <label class="pb-label">
                            Title <span class="text-brand-600">*</span>
                        </label>
                        <input name="title"
                               value="{{ old('title', $post->title) }}"
                               required
                               placeholder="Your post title…"
                               class="pb-input text-lg font-semibold @error('title') pb-input-error @enderror">
                        @error('title')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="pb-card p-6">
                    <div class="pb-field">
                        <label class="pb-label">Excerpt</label>
                        <p class="text-xs text-slate-400 mb-1">A short summary shown on the blog listing page (max 500 chars).</p>
                        <textarea name="excerpt"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Brief summary of this article…"
                                  class="pb-textarea @error('excerpt') pb-input-error @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Content editor --}}
                <div class="pb-card p-6">
                    <div class="pb-field">
                        <label class="pb-label">
                            Content <span class="text-brand-600">*</span>
                        </label>
                        <p class="text-xs text-slate-400 mb-1">Use the toolbar to format text, insert links and embed images.</p>
                        <textarea id="content-editor"
                                  name="content"
                                  rows="18"
                                  required
                                  class="pb-textarea @error('content') pb-input-error @enderror">{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Additional images --}}
                <div class="pb-card p-6">
                    <label class="pb-label">Additional Images</label>
                    <p class="text-xs text-slate-400 mb-3">These are also appended into the post body. Max 10 files, 5 MB each.</p>
                    <label class="pb-upload-zone w-full">
                        <x-heroicon-o-photo class="w-8 h-8 text-slate-300" />
                        <span class="text-sm font-semibold text-slate-500 mt-2">Click to select images</span>
                        <span class="text-xs text-slate-400">JPG, PNG, GIF, WebP</span>
                        <input type="file" name="additional_images_files[]" accept="image/*" multiple class="sr-only">
                    </label>
                    @error('additional_images_files')
                        <p class="pb-field-error">{{ $message }}</p>
                    @enderror
                    @error('additional_images_files.*')
                        <p class="pb-field-error">{{ $message }}</p>
                    @enderror

                    {{-- Existing additional images --}}
                    @if($post->exists && !empty($post->additional_images))
                        <div class="mt-5 pt-5 border-t border-slate-100">
                            <p class="pb-label mb-3">Current Images</p>
                            <div class="grid gap-3 sm:grid-cols-3">
                                @foreach((array) $post->additional_images as $index => $path)
                                    @php
                                        $imgUrl = filter_var($path, FILTER_VALIDATE_URL)
                                            ? $path
                                            : \App\Support\MediaUrl::resolve($path);
                                    @endphp
                                    <div class="pb-upload-preview group">
                                        <img src="{{ $imgUrl }}" alt="Additional image {{ $index + 1 }}"
                                             class="h-24 w-full object-cover">
                                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity">
                                            <input type="checkbox" name="remove_additional_images[]" value="{{ $index }}" class="sr-only peer">
                                            <span class="text-white text-xs font-semibold peer-checked:text-red-400">Remove</span>
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
                <div class="pb-card p-5">
                    <p class="pb-section-title text-sm mb-4">Publish Settings</p>

                    <div class="space-y-4">
                        <div class="pb-field">
                            <label class="pb-label">Status</label>
                            <select name="status" class="pb-select">
                                <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                            </select>
                            @error('status')
                                <p class="pb-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pb-field">
                            <label class="pb-label">Publish Date</label>
                            <input type="datetime-local"
                                   name="published_at"
                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\\TH:i')) }}"
                                   class="pb-input">
                            @error('published_at')
                                <p class="pb-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pb-field">
                            <label class="pb-label">Slug</label>
                            <input name="slug"
                                   value="{{ old('slug', $post->slug) }}"
                                   placeholder="auto-generated"
                                   class="pb-input">
                            <p class="text-xs text-slate-400">Leave blank to auto-generate from title.</p>
                            @error('slug')
                                <p class="pb-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col gap-2">
                        <button type="submit" class="pb-btn pb-btn-lg pb-btn-primary w-full">
                            <x-heroicon-o-check class="w-4 h-4" />
                            {{ $post->exists ? 'Save Changes' : 'Publish Post' }}
                        </button>
                        <a href="{{ route('admin.blog.index') }}" class="pb-btn pb-btn-md pb-btn-ghost w-full">
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Featured image --}}
                <div class="pb-card p-5">
                    <p class="pb-section-title text-sm mb-4">Featured Image</p>

                    {{-- Existing preview --}}
                    @if($post->exists && $post->featuredImageUrl())
                        <div class="pb-upload-preview mb-4">
                            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}"
                                 class="w-full h-36 object-cover">
                            <label class="absolute bottom-0 left-0 right-0 bg-black/60 py-2 px-3 flex items-center justify-between cursor-pointer">
                                <span class="text-white text-xs font-semibold">Remove image</span>
                                <input type="checkbox" name="remove_featured_image" value="1"
                                       class="w-4 h-4 accent-brand-600" @checked(old('remove_featured_image'))>
                            </label>
                        </div>
                    @endif

                    {{-- Upload --}}
                    <label class="pb-upload-zone w-full py-6 mb-3">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-slate-300" />
                        <span class="text-xs font-semibold text-slate-500 mt-2">Upload image file</span>
                        <input type="file" name="featured_image_file" accept="image/*" class="sr-only">
                    </label>
                    @error('featured_image_file')
                        <p class="pb-field-error">{{ $message }}</p>
                    @enderror

                    {{-- Or URL --}}
                    <div class="pb-field">
                        <label class="pb-label">Or paste image URL</label>
                        <input name="featured_image"
                               value="{{ old('featured_image', filter_var($post->featured_image, FILTER_VALIDATE_URL) ? $post->featured_image : '') }}"
                               placeholder="https://…"
                               class="pb-input">
                        @error('featured_image')
                            <p class="pb-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Live preview link --}}
                @if($post->exists && $post->status === 'published')
                    <div class="pb-alert pb-alert-success items-center">
                        <x-heroicon-o-globe-alt class="w-5 h-5 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold">Live on blog</p>
                            <p class="text-xs truncate">/blog/{{ $post->slug }}</p>
                        </div>
                        <a href="{{ route('blog.show', $post) }}" target="_blank"
                           class="shrink-0 hover:opacity-70 transition-colors">
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
