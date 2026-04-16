@extends('layouts.admin')

@section('title', 'Policy Management | Printbuka Admin')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-black text-cyan-300">Admin Dashboard</a>
            <h1 class="mt-3 text-4xl">Policy Management.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                Super Admin only area for managing legal documents shown to customers and staff.
            </p>
        </div>

        @if (session('status'))
            <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </p>
        @endif

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-slate-950">Terms & Conditions</h2>
                <p class="text-xs font-black uppercase tracking-wide {{ $terms->is_published ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $terms->is_published ? 'Published' : 'Draft' }}
                </p>
            </div>

            <form action="{{ route('admin.policies.terms.update') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @method('PUT')
                <label class="block text-sm font-black text-slate-800">
                    Title
                    <input name="title" value="{{ old('title', $terms->title) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                </label>
                <label class="block text-sm font-black text-slate-800">
                    Content
                    <textarea name="content" rows="12" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold">{{ old('content', $terms->content) }}</textarea>
                </label>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $terms->is_published)) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                    Publish document
                </label>
                <button class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Terms</button>
            </form>
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-slate-950">Privacy Policy</h2>
                <p class="text-xs font-black uppercase tracking-wide {{ $privacy->is_published ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $privacy->is_published ? 'Published' : 'Draft' }}
                </p>
            </div>

            <form action="{{ route('admin.policies.privacy.update') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @method('PUT')
                <label class="block text-sm font-black text-slate-800">
                    Title
                    <input name="title" value="{{ old('title', $privacy->title) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                </label>
                <label class="block text-sm font-black text-slate-800">
                    Content
                    <textarea name="content" rows="12" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold">{{ old('content', $privacy->content) }}</textarea>
                </label>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $privacy->is_published)) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                    Publish document
                </label>
                <button class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Privacy Policy</button>
            </form>
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-slate-950">Refund Policy</h2>
                <p class="text-xs font-black uppercase tracking-wide {{ $refund->is_published ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $refund->is_published ? 'Published' : 'Draft' }}
                </p>
            </div>

            <form action="{{ route('admin.policies.refund.update') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @method('PUT')
                <label class="block text-sm font-black text-slate-800">
                    Title
                    <input name="title" value="{{ old('title', $refund->title) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold">
                </label>
                <label class="block text-sm font-black text-slate-800">
                    Content
                    <textarea name="content" rows="12" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold">{{ old('content', $refund->content) }}</textarea>
                </label>
                <label class="flex items-center gap-3 text-sm font-bold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $refund->is_published)) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                    Publish document
                </label>
                <button class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Refund Policy</button>
            </form>
        </section>
    </div>
@endsection
