@extends('layouts.admin')

@section('title', ($invoice->exists ? 'Edit Invoice' : 'Create Invoice').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8"><a href="{{ route('admin.invoices.index') }}" class="text-sm font-black text-cyan-300">Invoices</a><h1 class="mt-3 text-4xl">{{ $invoice->exists ? 'Edit invoice.' : 'Create invoice.' }}</h1></div>
        <form action="{{ $invoice->exists ? route('admin.invoices.update', $invoice) : route('admin.invoices.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($invoice->exists) @method('PUT') @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black">Order<select name="order_id" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($orders as $order)<option value="{{ $order->id }}" @selected((int) old('order_id', $invoice->order_id) === $order->id)>{{ $order->job_order_number ?? $order->displayNumber() }} · {{ $order->customer_name }}</option>@endforeach</select></label>
                <label class="text-sm font-black">Invoice #<input name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Subtotal<input type="number" min="0" step="0.01" name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Tax<input type="number" min="0" step="0.01" name="tax_amount" value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Discount<input type="number" min="0" step="0.01" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Total<input type="number" min="0" step="0.01" name="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Status<select name="status" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach (['unpaid' => 'Unpaid', 'partially_paid' => 'Partially Paid', 'paid' => 'Paid', 'draft' => 'Draft'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $invoice->status) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label class="text-sm font-black">Issued At<input type="datetime-local" name="issued_at" value="{{ old('issued_at', $invoice->issued_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Due At<input type="datetime-local" name="due_at" value="{{ old('due_at', $invoice->due_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Sent At<input type="datetime-local" name="sent_at" value="{{ old('sent_at', $invoice->sent_at?->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
            </div>
            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Invoice</button>
        </form>
    </div>
@endsection
