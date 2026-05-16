@extends('layouts.admin')

@section('title', 'Advertisements | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-pink-700">Public Ads</p>
            <h1 class="mt-2 text-4xl font-black text-slate-950">Advertisements</h1>
            <p class="mt-2 text-sm font-semibold text-slate-600">Create promotional ads that render across public pages.</p>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Create ad</h2>
                <form action="{{ route('admin.advertisements.store') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="text-sm font-black text-slate-700">Placement</label>
                        <select name="placement" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold" required>
                            @foreach ($placements as $value => $label)
                                <option value="{{ $value }}" @selected(old('placement', 'top_banner') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-black text-slate-700">Title</label>
                        <input name="title" value="{{ old('title') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold" required>
                    </div>
                    <div>
                        <label class="text-sm font-black text-slate-700">Body</label>
                        <textarea name="body" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3 text-sm font-semibold">{{ old('body') }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-black text-slate-700">Image URL</label>
                        <input name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://..." class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">CTA label</label>
                            <input name="cta_label" value="{{ old('cta_label') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        </div>
                        <div>
                            <label class="text-sm font-black text-slate-700">CTA URL</label>
                            <input name="cta_url" type="url" value="{{ old('cta_url') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">Starts at</label>
                            <input name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        </div>
                        <div>
                            <label class="text-sm font-black text-slate-700">Ends at</label>
                            <input name="ends_at" type="datetime-local" value="{{ old('ends_at') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <label class="inline-flex items-center gap-3 text-sm font-black text-slate-700">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" checked>
                            Active
                        </label>
                        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="min-h-11 w-28 rounded-md border border-slate-200 px-3 text-sm font-semibold" aria-label="Sort order">
                    </div>
                    <button class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Publish Ad</button>
                </form>
            </section>

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Active library</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($advertisements as $ad)
                        <article class="rounded-md border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-black text-slate-950">{{ $ad->title }}</p>
                                    <p class="mt-1 text-xs font-black uppercase tracking-wide text-slate-500">{{ $placements[$ad->placement] ?? $ad->placement }} · {{ $ad->is_active ? 'Active' : 'Inactive' }}</p>
                                    @if ($ad->body)
                                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $ad->body }}</p>
                                    @endif
                                </div>
                                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md border border-slate-200 px-3 py-2 text-xs font-black text-slate-600 transition hover:border-pink-300 hover:text-pink-700">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500">No ads created yet.</p>
                    @endforelse
                </div>
                <div class="mt-5">{{ $advertisements->links() }}</div>
            </section>
        </div>
    </div>
@endsection
