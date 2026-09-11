@extends('layouts.admin')

@section('title', 'Invoice Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="pb-page-header">
            <div>
                <p class="pb-label">Invoice Management</p>
                <h1 class="pb-page-title">Invoices</h1>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.invoices.quotations.create') }}" class="pb-btn pb-btn-md pb-btn-outline">Create Quotation</a>
                <a href="{{ route('admin.invoices.create') }}" class="pb-btn pb-btn-md pb-btn-primary">Create Invoice</a>
            </div>
        </div>

        @if (request()->user()?->role === 'super_admin')
            <livewire:admin.invoice-csv-import />
        @endif

        <livewire:admin.invoices-table />
    </div>
@endsection
