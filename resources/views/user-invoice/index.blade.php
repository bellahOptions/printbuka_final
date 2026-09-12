@extends('layouts.new-app')

@section('title', 'My Invoices | PrintBuka')

@section('content')
@php
    $paymentRouteExists = false;
@endphp
<main class="invoice-page min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="badge bg-brand-100 text-brand-700 border-0 mb-2">Billing</div>
                    <h1 class="pb-display text-3xl">My Invoices</h1>
                    <p class="mt-1 text-sm text-slate-500">View and manage all your invoices and quotations</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-brand-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Order
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            {{-- Total Invoices --}}
            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-brand-500">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Total Documents</div>
                <div class="stat-value text-2xl text-slate-900">{{ $totalInvoices }}</div>
                <div class="stat-desc text-slate-400">Invoices & Quotations</div>
            </div>

            {{-- Pending Amount --}}
            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-amber-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Pending Payment</div>
                <div class="stat-value text-2xl text-slate-900">₦{{ number_format($pendingAmount, 2) }}</div>
                <div class="stat-desc text-amber-600">{{ $overdueInvoices }} overdue</div>
            </div>

            {{-- Paid Amount --}}
            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-emerald-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Total Paid</div>
                <div class="stat-value text-2xl text-slate-900">₦{{ number_format($paidAmount, 2) }}</div>
                <div class="stat-desc text-emerald-600">Completed payments</div>
            </div>

            {{-- Overdue Badge --}}
            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-red-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Overdue</div>
                <div class="stat-value text-2xl text-slate-900">{{ $overdueInvoices }}</div>
                <div class="stat-desc text-red-600">Requires attention</div>
            </div>
        </div>

        {{-- Invoices Table --}}
        <livewire:invoice.invoice-list />
    </section>
</main>
<style>
    .invoice-page,
    .invoice-page * {
        font-family: "Open Sans", Arial, sans-serif;
    }
</style>
@endsection
