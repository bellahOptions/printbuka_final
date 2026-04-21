@extends('layouts.admin')

@section('title', 'Invoice Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Invoice Management</p>
                <h1 class="mt-2 text-4xl text-slate-950">Invoices.</h1>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.invoices.quotations.create') }}" class="rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Create Quotation</a>
                <a href="{{ route('admin.invoices.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Create Invoice</a>
            </div>
        </div>

        <livewire:admin.invoices-table />
    </div>
@endsection
