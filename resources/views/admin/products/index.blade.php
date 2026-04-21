@extends('layouts.admin')

@section('title', 'Product Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Product Management</p>
                <h1 class="mt-2 text-4xl text-slate-950">Products.</h1>
            </div>
            <a href="{{ route('admin.products.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Create Product</a>
        </div>

        <livewire:admin.products-table />
    </div>
@endsection
