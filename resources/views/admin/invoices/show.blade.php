@extends('layouts.admin')

@section('title', $invoice->documentTypeLabel().' '.$invoice->invoice_number.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">{{ $invoice->documentTypeLabel() }}</p>
                <h1 class="mt-2 text-4xl font-black text-slate-950">{{ $invoice->documentTypeLabel() }} {{ $invoice->invoice_number }}</h1>
                <p class="mt-2 text-sm text-slate-500">Order {{ $invoice->order?->job_order_number ?? 'N/A' }} · {{ $invoice->order?->customer_name ?? 'Unknown customer' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="rounded-xl bg-cyan-900 px-5 py-3 text-sm font-black text-white hover:bg-cyan-800">Edit</a>
                <a href="{{ route('admin.invoices.download', $invoice) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Download PDF</a>
                <a href="{{ route('admin.invoices.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Back to list</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Status</p>
                <p class="mt-3 text-2xl font-black text-slate-950">{{ str($invoice->status)->replace('_', ' ')->title() }}</p>
                <p class="mt-2 text-sm text-slate-500">Issued: {{ $invoice->issued_at?->format('M j, Y') ?? 'N/A' }}</p>
                <p class="mt-2 text-sm text-slate-500">Due: {{ $invoice->due_at?->format('M j, Y') ?? 'N/A' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Customer</p>
                <p class="mt-3 font-black text-slate-950">{{ $invoice->order?->customer_name ?? 'N/A' }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $invoice->order?->customer_email ?? 'N/A' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $invoice->order?->customer_phone ?? 'N/A' }}</p>
                @if($invoice->order?->delivery_address)
                    <p class="mt-3 text-sm text-slate-500">{{ $invoice->order?->delivery_address }}</p>
                    <p class="text-sm text-slate-500">{{ $invoice->order?->delivery_city }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Totals</p>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-sm text-slate-600"><span>Subtotal</span><span>₦{{ number_format($invoice->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-sm text-slate-600"><span>Tax</span><span>₦{{ number_format($invoice->tax_amount, 2) }}</span></div>
                    <div class="flex justify-between text-sm text-slate-600"><span>Discount</span><span>₦{{ number_format($invoice->discount_amount, 2) }}</span></div>
                    <div class="border-t border-slate-200 pt-3 text-lg font-black text-slate-900 flex justify-between"><span>Total</span><span>₦{{ number_format($invoice->total_amount, 2) }}</span></div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Job Details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Job Number</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->job_order_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Service Type</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->service_type ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Quantity</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->quantity ?? 1 }}</p>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Payment Status</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->payment_status ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Invoice / Quotation Notes</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Artwork Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $invoice->order?->artwork_notes ?: 'None' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Internal Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $invoice->order?->internal_notes ?: 'None' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
