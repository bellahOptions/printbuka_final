@extends('layouts.admin')

@section('title', 'Blog Management | Printbuka')

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-pink-600 mb-1">Content</p>
            <h1 class="text-3xl font-black text-slate-900">Blog Posts</h1>
            <p class="text-sm text-slate-500 mt-1">Manage and publish articles for your blog.</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-5 py-3 rounded-xl transition-colors shadow-sm shadow-pink-200 shrink-0">
            <x-heroicon-o-plus class="w-4 h-4" />
            New Post
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('status'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    {{-- Stats strip --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <p class="text-2xl font-black text-slate-900">{{ $total }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mt-0.5">Total Posts</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm text-center">
            <p class="text-2xl font-black text-emerald-700">{{ $published }}</p>
            <p class="text-xs font-bold text-emerald-400 uppercase tracking-wide mt-0.5">Published</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 shadow-sm text-center">
            <p class="text-2xl font-black text-amber-700">{{ $drafts }}</p>
            <p class="text-xs font-bold text-amber-400 uppercase tracking-wide mt-0.5">Drafts</p>
        </div>
    </div>

    <livewire:admin.blog-posts-table />

</div>
@endsection
