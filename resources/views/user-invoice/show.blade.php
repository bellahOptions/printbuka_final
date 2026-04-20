@extends('layouts.theme')

@section('title', $invoice->documentTypeLabel() . ' ' . ($invoice->invoice_number ?? '#' . $invoice->id) . ' | PrintBuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('user.invoices.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-pink-600 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Invoices
            </a>
        </div>

        {{-- Invoice Card --}}
        <div class="card bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            
            {{-- Invoice Header --}}
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-8 sm:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="badge bg-cyan-500/20 text-cyan-300 border-0 mb-2">
                            {{ $invoice->documentTypeLabel() }}
                        </div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ $invoice->invoice_number ?? 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-slate-300 text-sm mt-1">
                            Issued: {{ $invoice->issued_at ? $invoice->issued_at->format('F d, Y') : 'Pending' }}
                        </p>
                    </div>
                    <div>
                        @php
                            $statusBadge = [
                                'paid' => 'success',
                                'pending' => 'warning',
                                'overdue' => 'error',
                                'cancelled' => 'error',
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusBadge[$invoice->status] ?? 'info' }} badge-lg">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Invoice Body --}}
            <div class="p-6 sm:p-8">
                
                {{-- Company & Customer Info --}}
                <div class="grid gap-8 sm:grid-cols-2 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400 mb-3">From</h3>
                        <div class="space-y-1">
                            <p class="font-bold text-slate-800 text-lg">PrintBuka Limited</p>
                            <p class="text-sm text-slate-600">123 Printing Avenue, Ikeja</p>
                            <p class="text-sm text-slate-600">Lagos, Nigeria</p>
                            <p class="text-sm text-slate-600">hello@printbuka.com</p>
                            <p class="text-sm text-slate-600">+234 801 234 5678</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400 mb-3">Bill To</h3>
                        <div class="space-y-1">
                            <p class="font-bold text-slate-800">{{ $invoice->order->user->first_name ?? 'Customer' }} {{ $invoice->order->user->last_name ?? '' }}</p>
                            <p class="text-sm text-slate-600">{{ $invoice->order->customer_email ?? $invoice->order->user->email ?? 'N/A' }}</p>
                            <p class="text-sm text-slate-600">{{ $invoice->order->customer_phone ?? 'N/A' }}</p>
                            @if($invoice->order->delivery_address)
                                <p class="text-sm text-slate-600">{{ $invoice->order->delivery_address }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order Details --}}
                <div class="mb-8">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-400 mb-4">Order Details</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left py-3 text-slate-600 font-semibold">Item</th>
                                    <th class="text-left py-3 text-slate-600 font-semibold">Description</th>
                                    <th class="text-right py-3 text-slate-600 font-semibold">Qty</th>
                                    <th class="text-right py-3 text-slate-600 font-semibold">Unit Price</th>
                                    <th class="text-right py-3 text-slate-600 font-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100">
                                    <td class="py-3">
                                        <p class="font-medium text-slate-800">{{ $invoice->order->product->name ?? 'Custom Print Order' }}</p>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm text-slate-600">
                                            {{ $invoice->order->product->short_description ?? $invoice->order->service_type ?? 'Print Service' }}
                                        </p>
                                        @if($invoice->order->job_type)
                                            <p class="text-xs text-slate-400 mt-1">Type: {{ $invoice->order->job_type }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-slate-700">{{ $invoice->order->quantity ?? 1 }}</td>
                                    <td class="py-3 text-right text-slate-700">₦{{ number_format($invoice->subtotal / max($invoice->order->quantity ?? 1, 1), 2) }}</td>
                                    <td class="py-3 text-right font-semibold text-slate-800">₦{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="border-t border-slate-200 pt-6">
                    <div class="flex flex-col items-end">
                        <div class="w-full sm:w-80 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Subtotal:</span>
                                <span class="text-slate-700">₦{{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            @if($invoice->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Discount:</span>
                                <span class="text-emerald-600">-₦{{ number_format($invoice->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($invoice->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Tax (VAT):</span>
                                <span class="text-slate-700">₦{{ number_format($invoice->tax_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-slate-200">
                                <span class="text-slate-900">Total:</span>
                                <span class="text-pink-600">₦{{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                @if($invoice->status === 'paid' && $invoice->paid_at)
                <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-800">Payment Completed</p>
                            <p class="text-xs text-emerald-700">Paid on {{ $invoice->paid_at->format('F d, Y \a\t h:i A') }}</p>
                            @if($invoice->payment_reference)
                                <p class="text-xs text-emerald-600 mt-1">Ref: {{ $invoice->payment_reference }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @elseif($invoice->status === 'pending')
                <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Awaiting Payment</p>
                                @if($invoice->due_at)
                                    <p class="text-xs text-amber-700">Due by {{ $invoice->due_at->format('F d, Y') }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('payment.process', $invoice) ?? '#' }}" class="btn btn-sm bg-pink-600 hover:bg-pink-700 text-white border-0">
                            Proceed to Payment
                            <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Notes --}}
                @if($invoice->order->artwork_notes || $invoice->order->internal_notes)
                <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <h4 class="text-sm font-semibold text-slate-700 mb-2">Notes</h4>
                    <p class="text-sm text-slate-600">{{ $invoice->order->artwork_notes ?? $invoice->order->internal_notes }}</p>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-8 flex flex-wrap gap-3 justify-end">
                    <a href="{{ route('user.invoices.download', $invoice) }}" class="btn btn-outline btn-pink-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-outline btn-slate-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    @media print {
        .btn, .dropdown, .navbar, footer, .badge a:not(.print-friendly) {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endsection