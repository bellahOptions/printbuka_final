@extends('layouts.admin')

@section('title', 'Blog Management | Printbuka')

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- Header --}}
    <div class="pb-page-header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 mb-1">Content</p>
            <h1 class="pb-page-title">Blog Posts</h1>
            <p class="pb-page-subtitle">Manage and publish articles for your blog.</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="pb-btn pb-btn-md pb-btn-primary shrink-0">
            <x-heroicon-o-plus class="w-4 h-4" />
            New Post
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('status'))
        <div class="pb-alert pb-alert-success mb-6">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    {{-- Stats strip --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="pb-stat-card text-center">
            <p class="pb-stat-value">{{ $total }}</p>
            <p class="pb-stat-label mt-0.5">Total Posts</p>
        </div>
        <div class="pb-stat-card text-center">
            <p class="pb-stat-value text-emerald-700">{{ $published }}</p>
            <p class="pb-stat-label mt-0.5">Published</p>
        </div>
        <div class="pb-stat-card text-center">
            <p class="pb-stat-value text-amber-700">{{ $drafts }}</p>
            <p class="pb-stat-label mt-0.5">Drafts</p>
        </div>
    </div>

    <livewire:admin.blog-posts-table />

</div>
@endsection
