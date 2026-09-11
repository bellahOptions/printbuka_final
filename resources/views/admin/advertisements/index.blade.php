@extends('layouts.admin')

@section('title', 'Advertisements | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="pb-page-header">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Public Ads</p>
                <h1 class="pb-page-title">Advertisements</h1>
                <p class="pb-page-subtitle">Create promotional ads that render across public pages.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="pb-card">
                <div class="pb-card-header">
                    <h2 class="pb-card-title">Create ad</h2>
                </div>
                <form action="{{ route('admin.advertisements.store') }}" method="POST" class="pb-card-content space-y-5">
                    @csrf
                    <div class="pb-field">
                        <label class="pb-label">Placement</label>
                        <select name="placement" class="pb-select" required>
                            @foreach ($placements as $value => $label)
                                <option value="{{ $value }}" @selected(old('placement', 'top_banner') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Title</label>
                        <input name="title" value="{{ old('title') }}" class="pb-input" required>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Body</label>
                        <textarea name="body" rows="4" data-rich-editor class="pb-textarea">{{ old('body') }}</textarea>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Image URL</label>
                        <input name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://..." class="pb-input">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="pb-field">
                            <label class="pb-label">CTA label</label>
                            <input name="cta_label" value="{{ old('cta_label') }}" class="pb-input">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">CTA URL</label>
                            <input name="cta_url" type="url" value="{{ old('cta_url') }}" class="pb-input">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="pb-field">
                            <label class="pb-label">Starts at</label>
                            <input name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" class="pb-input">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Ends at</label>
                            <input name="ends_at" type="datetime-local" value="{{ old('ends_at') }}" class="pb-input">
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" checked>
                            Active
                        </label>
                        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="pb-input w-28" aria-label="Sort order">
                    </div>
                    <button class="pb-btn pb-btn-lg pb-btn-primary w-full">Publish Ad</button>
                </form>
            </section>

            <section class="pb-card">
                <div class="pb-card-header">
                    <h2 class="pb-card-title">Active library</h2>
                </div>
                <div class="pb-card-content pt-0">
                    <livewire:admin.advertisements-list />
                </div>
            </section>
        </div>
    </div>
@endsection
