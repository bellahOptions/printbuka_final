@extends('layouts.admin')

@section('title', 'Product Categories | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-sm font-black uppercase tracking-wide text-pink-700">Product Category Management</p><h1 class="mt-2 text-4xl text-slate-950">Categories.</h1></div>
                <a href="{{ route('admin.product-categories.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Create Category</a>
            </div>
            @if (session('status'))<p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>@endif
            <div class="mt-8">
                <livewire:admin.product-categories-table />
            </div>
    </div>
@endsection
