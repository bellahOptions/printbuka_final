@extends('layouts.admin')

@section('title', 'Job Log · '.$order->job_order_number.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Job Log</p>
                <h1 class="mt-2 text-4xl font-black text-slate-950">{{ $order->job_order_number }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $order->customer_name }} · {{ $order->status }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.orders.job-log.download', $order) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Download PDF</a>
                <a href="{{ route('admin.orders.show', $order) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Back to Job</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Job</p>
                <p class="mt-3 text-xl font-black text-slate-950">{{ $order->job_order_number }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->job_type }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->quantity }} pcs</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Customer</p>
                <p class="mt-3 text-lg font-black text-slate-950">{{ $order->customer_name }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->customer_email }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $order->customer_phone }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Status</p>
                <p class="mt-3 text-xl font-black text-slate-950">{{ $order->status }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->payment_status }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Job Comments</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Internal Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->internal_notes ?: 'No internal notes.' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Phase Approval Comment</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->phase_approval_comment ?: 'No approval comments yet.' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Expense Journal (Debits Only)</h2>
            @if($expenseEntries->isEmpty())
                <p class="mt-4 text-sm text-slate-500">No expense entries have been attached to this job yet.</p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach($expenseEntries as $entry)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-black text-slate-900">{{ $entry->category }}</p>
                                <p class="text-sm font-black text-pink-700">-₦{{ number_format($entry->amount, 2) }}</p>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">{{ $entry->description }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ $entry->entry_date->format('M j, Y') }} · Recorded by {{ $entry->recorder?->displayName() ?? 'Unknown' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
