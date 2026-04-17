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

        @if (session('status'))
            <p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
        @endif
        @if (session('warning'))
            <p class="mt-6 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">{{ session('warning') }}</p>
        @endif

        <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">Document</th>
                        <th class="px-5 py-4">Type</th>
                        <th class="px-5 py-4">Job</th>
                        <th class="px-5 py-4">Client</th>
                        <th class="px-5 py-4">Total</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $invoice)
                        @php
                            $documentType = $invoice->documentTypeLabel();
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-black">{{ $invoice->invoice_number }}</td>
                            <td class="px-5 py-4">{{ $documentType }}</td>
                            <td class="px-5 py-4">{{ $invoice->order?->job_order_number ?? 'No job' }}</td>
                            <td class="px-5 py-4">{{ $invoice->order?->customer_name ?? 'Pending' }}</td>
                            <td class="px-5 py-4">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                            <td class="px-5 py-4">{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center justify-end gap-3">
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="font-black text-pink-700">Edit</a>

                                    @if ($invoice->status !== 'paid')
                                        <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="font-black text-emerald-700 transition hover:text-emerald-900">Mark Paid</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-black text-slate-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $invoices->links() }}</div>
    </div>
@endsection
