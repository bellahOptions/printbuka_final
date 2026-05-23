@extends('layouts.admin')

@section('title', 'Finance Record #'.$entry->id.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Finance Detail</p>
                <h1 class="mt-2 text-4xl font-black text-slate-950">Finance Record #{{ $entry->id }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ ucfirst($entry->type) }} · {{ $entry->category }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.finance.download', $entry) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Download PDF</a>
                <a href="{{ route('admin.finance.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Back to Finance</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Date</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->entry_date->format('M j, Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Type</p>
                <p class="mt-2 text-sm text-slate-900">{{ ucfirst($entry->type) }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Category</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->category }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Amount</p>
                <p class="mt-2 text-sm text-slate-900">₦{{ number_format($entry->amount, 2) }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Details</h2>
            <div class="mt-4 space-y-4">
                <p><span class="font-black">Payee:</span> {{ $entry->payee ?: 'N/A' }}</p>
                <p><span class="font-black">Payment Method:</span> {{ $entry->payment_method ?: 'N/A' }}</p>
                <p><span class="font-black">Order:</span> {{ $entry->order?->job_order_number ?? 'N/A' }}</p>
                <p><span class="font-black">Recorded by:</span> {{ $entry->recorder?->displayName() ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Notes</h2>
            <p class="mt-4 text-sm text-slate-700">{{ $entry->notes ?: 'No notes provided.' }}</p>
        </div>
    </div>
@endsection
