@extends('layouts.admin')

@section('title', $invoice->documentTypeLabel().' '.$invoice->invoice_number.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Flash messages --}}
        @if (session('status'))
            <div class="fade-in-up rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-black text-emerald-800">{{ session('status') }}</p>
                </div>
            </div>
        @endif
        @if (session('warning'))
            <div class="fade-in-up rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm font-black text-amber-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        {{-- Hero Header --}}
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="mb-4 flex items-center gap-2">
                <a href="{{ route('admin.invoices.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex-1">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-300">{{ $invoice->documentTypeLabel() }}</p>
                    <h1 class="mt-2 text-4xl font-black tracking-tight lg:text-5xl">{{ $invoice->documentTypeLabel() }} {{ $invoice->invoice_number }}</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">
                        Order {{ $invoice->order?->job_order_number ?? 'N/A' }} · {{ $invoice->order?->customer_name ?? 'Unknown customer' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 sm:flex-shrink-0">
                    @if ($invoice->status !== 'paid')
                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="rounded-xl bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Edit</a>
                    @else
                        <span class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white/60">Paid · locked</span>
                    @endif
                    <a href="{{ route('admin.invoices.download', $invoice) }}" class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/20">Download PDF</a>
                </div>
            </div>
        </div>

        {{-- Status, Customer, Totals cards --}}
        <div class="fade-in-up section-delay-1 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl border border-pink-200 bg-gradient-to-br from-pink-100 to-pink-50 p-2">
                        <svg class="h-5 w-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Status</p>
                </div>
                <p class="mt-3 text-2xl font-black text-slate-950">{{ str($invoice->status)->replace('_', ' ')->title() }}</p>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Issued: {{ $invoice->issued_at?->format('M j, Y') ?? 'N/A' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Due: {{ $invoice->due_at?->format('M j, Y') ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl border border-cyan-200 bg-gradient-to-br from-cyan-100 to-cyan-50 p-2">
                        <svg class="h-5 w-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Customer</p>
                </div>
                <p class="mt-3 font-black text-slate-950">{{ $invoice->order?->customer_name ?? 'N/A' }}</p>
                <div class="mt-4 space-y-2">
                    @if ($invoice->order?->customer_email)
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <svg class="h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $invoice->order?->customer_email }}
                        </div>
                    @endif
                    @if ($invoice->order?->customer_phone)
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <svg class="h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $invoice->order?->customer_phone }}
                        </div>
                    @endif
                    @if ($invoice->order?->delivery_address)
                        <div class="flex items-start gap-2 text-sm text-slate-500">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $invoice->order?->delivery_address }}@if($invoice->order?->delivery_city), {{ $invoice->order?->delivery_city }}@endif</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl border border-pink-200 bg-gradient-to-br from-pink-100 to-pink-50 p-2">
                        <svg class="h-5 w-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Totals</p>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">₦{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Tax</span>
                        <span class="font-semibold">₦{{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Discount</span>
                        <span class="font-semibold">₦{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 text-lg font-black text-slate-900 flex justify-between">
                        <span>Total</span>
                        <span>₦{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Items Breakdown --}}
        @php
            $breakdown = is_array($invoice->order?->pricing_breakdown) ? $invoice->order->pricing_breakdown : [];
            $lineItems = collect($breakdown['line_items'] ?? [])
                ->filter(fn ($item) => is_array($item))
                ->map(function (array $item): array {
                    return [
                        'description' => (string) ($item['description'] ?? ''),
                        'quantity' => max(1, (int) ($item['quantity'] ?? 0)),
                        'rate' => max(0, (float) ($item['rate'] ?? 0)),
                        'amount' => (float) ($item['amount'] ?? ($item['quantity'] * $item['rate'] ?? 0)),
                    ];
                })
                ->filter(fn ($item) => $item['description'] !== '')
                ->values();

            if ($lineItems->isEmpty()) {
                $lineItems = collect([[
                    'description' => $invoice->order?->product?->name ?? ($invoice->order?->job_type ?? 'Custom order'),
                    'quantity' => max(1, (int) ($invoice->order?->quantity ?? 1)),
                    'rate' => max(0, (float) ($invoice->order?->unit_price ?? 0)),
                    'amount' => max(0, (float) $invoice->subtotal),
                ]]);
            }
        @endphp

        <div class="fade-in-up section-delay-2 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="rounded-xl border border-cyan-200 bg-gradient-to-br from-cyan-100 to-cyan-50 p-2">
                    <svg class="h-5 w-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-slate-950">Invoice Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left text-sm">
                    <thead class="text-xs font-black uppercase tracking-wide text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Price</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($lineItems as $item)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $item['description'] }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">{{ number_format($item['quantity'], 0) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">₦{{ number_format($item['rate'], 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">₦{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Job Details --}}
        <div class="fade-in-up section-delay-3 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-100 to-slate-50 p-2">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-slate-950">Job Details</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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

        {{-- Notes Section --}}
        <div class="fade-in-up section-delay-4 rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="rounded-xl border border-cyan-200 bg-gradient-to-br from-cyan-100 to-cyan-50 p-2">
                    <svg class="h-5 w-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-slate-950">Notes</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
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

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        .section-delay-1 { animation-delay: 0.05s; }
        .section-delay-2 { animation-delay: 0.1s; }
        .section-delay-3 { animation-delay: 0.15s; }
        .section-delay-4 { animation-delay: 0.2s; }
    </style>
@endsection