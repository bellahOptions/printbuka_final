@extends('layouts.admin')

@section('title', 'Admin Job Tracker | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Production Job Tracker</p>
                <h1 class="mt-2 text-4xl text-slate-950">All customer orders.</h1>
            </div>
            @if (auth()->user()->canAdmin('orders.create'))
                <a href="{{ route('admin.orders.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                    Create Job
                </a>
            @endif
        </div>

        <livewire:admin.orders-table />
    </div>
@endsection
