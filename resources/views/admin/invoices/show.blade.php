@extends('layouts.admin')

@section('title', $invoice->documentTypeLabel().' '.$invoice->invoice_number.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Flash messages --}}
        @if (session('status'))
            <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
        @endif
        @if (session('warning'))
            <div class="pb-alert pb-alert-warning">{{ session('warning') }}</div>
        @endif

        {{-- Hero Header --}}
        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
                <p class="pb-label">{{ $invoice->documentTypeLabel() }}</p>
                <h1 class="pb-page-title">{{ $invoice->documentTypeLabel() }} {{ $invoice->invoice_number }}</h1>
                <p class="pb-page-subtitle">Order {{ $invoice->order?->job_order_number ?? 'N/A' }} · {{ $invoice->order?->customer_name ?? 'Unknown customer' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($invoice->status !== 'paid')
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="pb-btn pb-btn-md pb-btn-primary">Edit</a>
                @else
                    <span class="pb-badge pb-badge-secondary">Paid · locked</span>
                @endif
                <a href="{{ route('admin.invoices.download', $invoice) }}" class="pb-btn pb-btn-md pb-btn-outline">Download PDF</a>
            </div>
        </div>

        {{-- Status, Customer, Totals cards --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="pb-card p-6">
                <p class="pb-label">Status</p>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ str($invoice->status)->replace('_', ' ')->title() }}</p>
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

            <div class="pb-card p-6">
                <p class="pb-label">Customer</p>
                <p class="mt-3 font-bold text-slate-900">{{ $invoice->order?->customer_name ?? 'N/A' }}</p>
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

            <div class="pb-card p-6">
                <p class="pb-label">Totals</p>
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
                    <div class="border-t border-slate-200 pt-3 text-lg font-bold text-slate-900 flex justify-between">
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

        <div class="pb-card p-6">
            <h2 class="pb-section-title mb-6">Invoice Items</h2>
            <div class="pb-table-wrapper">
                <table class="pb-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lineItems as $item)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $item['description'] }}</td>
                                <td class="text-right">{{ number_format($item['quantity'], 0) }}</td>
                                <td class="text-right">₦{{ number_format($item['rate'], 2) }}</td>
                                <td class="text-right font-bold text-slate-900">₦{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Job Details --}}
        <div class="pb-card p-6">
            <h2 class="pb-section-title mb-6">Job Details</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="pb-label">Job Number</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->job_order_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="pb-label">Service Type</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->service_type ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="pb-label">Quantity</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->quantity ?? 1 }}</p>
                </div>
                <div>
                    <p class="pb-label">Payment Status</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $invoice->order?->payment_status ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Payment Terms --}}
        @if(auth()->user()->canAdmin('invoices.manage'))
        <div class="pb-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="pb-section-title">Payment Terms</h2>
                @if ($invoice->order?->payment_terms === 'credit')
                    <span class="pb-badge pb-badge-warning">Credit Terms Active</span>
                @endif
            </div>
            <p class="mb-4 text-sm text-slate-600">
                <strong>Standard</strong> — client must settle 70% deposit before production begins.<br>
                <strong>Credit Terms</strong> — trusted client; job can proceed and be delivered before full payment.
            </p>
            <form method="POST" action="{{ route('admin.invoices.payment-terms', $invoice) }}" class="flex flex-wrap items-end gap-4">
                @csrf @method('PATCH')
                <div class="pb-field">
                    <label class="pb-label">Terms</label>
                    <select name="payment_terms" class="pb-input">
                        <option value="standard" @selected(($invoice->order?->payment_terms ?? 'standard') === 'standard')>Standard (70% before production)</option>
                        <option value="credit" @selected(($invoice->order?->payment_terms ?? 'standard') === 'credit')>Credit Terms (deliver before payment)</option>
                    </select>
                </div>
                <button type="submit" class="pb-btn pb-btn-md pb-btn-secondary">
                    Update Terms
                </button>
            </form>
        </div>
        @endif

        {{-- Record Payment --}}
        @if ($invoice->status !== 'paid' && auth()->user()->canAdmin('invoices.manage'))
        @php
            $paidSoFar   = $invoice->amountPaid();
            $balance     = $invoice->balance();
            $pct         = $invoice->paymentPercentage();
            $invoiceTotal = (float) $invoice->total_amount;
            $amt60       = round($invoiceTotal * 0.60, 2);
            $amt70       = round($invoiceTotal * 0.70, 2);
            $defaultAmt  = old('amount', ($paidSoFar == 0
                ? number_format($amt70, 2, '.', '')   // default to 70% deposit on first payment
                : number_format($balance, 2, '.', '') // default to remaining balance on subsequent payments
            ));
        @endphp
        <div class="pb-card p-6 border-emerald-200">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="pb-section-title">Record Payment</h2>
                <span class="ml-auto text-sm font-semibold text-slate-600">
                    Paid: <span class="text-emerald-700">₦{{ number_format($paidSoFar, 2) }}</span>
                    &nbsp;·&nbsp; Balance: <span class="text-pink-700">₦{{ number_format($balance, 2) }}</span>
                    &nbsp;·&nbsp; <span class="{{ $pct >= 70 ? 'text-emerald-700' : 'text-amber-700' }}">{{ number_format($pct, 1) }}% paid</span>
                </span>
            </div>

            {{-- Overall progress bar --}}
            <div class="mb-5 pb-progress">
                <div class="{{ $pct >= 100 ? 'pb-progress-success' : ($pct >= 70 ? 'pb-progress-warning' : 'pb-progress-primary') }}"
                    style="width: {{ min(100, $pct) }}%"></div>
            </div>

            {{-- Quick-select buttons --}}
            <div class="mb-5">
                <p class="pb-label mb-2">Quick-set amount</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setPaymentAmount({{ $amt60 }})"
                        class="pb-btn pb-btn-sm pb-btn-outline">
                        60% &nbsp;— ₦{{ number_format($amt60, 2) }}
                    </button>
                    <button type="button" onclick="setPaymentAmount({{ $amt70 }})"
                        class="pb-btn pb-btn-sm pb-btn-success">
                        70% (deposit) &nbsp;— ₦{{ number_format($amt70, 2) }}
                    </button>
                    <button type="button" onclick="setPaymentAmount({{ $balance }})"
                        class="pb-btn pb-btn-sm pb-btn-outline">
                        Balance &nbsp;— ₦{{ number_format($balance, 2) }}
                    </button>
                    <button type="button" onclick="setPaymentAmount({{ $invoiceTotal }})"
                        class="pb-btn pb-btn-sm pb-btn-outline">
                        Full &nbsp;— ₦{{ number_format($invoiceTotal, 2) }}
                    </button>
                </div>
            </div>

            {{-- Payment form — standalone, no nested forms --}}
            <form id="recordPaymentForm" method="POST" action="{{ route('admin.invoices.record-payment', $invoice) }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-confirm-amount="amount" data-confirm-label="this payment">
                @csrf
                <input type="hidden" name="idempotency_key" data-idempotency-key>
                <div class="pb-field sm:col-span-2 lg:col-span-1">
                    <label class="pb-label">
                        Amount (₦) <span class="text-pink-600">*</span>
                    </label>
                    <input type="number" id="paymentAmountInput" name="amount" step="0.01" min="0.01"
                        value="{{ $defaultAmt }}"
                        oninput="updatePaymentPct(this.value)"
                        class="pb-input"
                        placeholder="0.00" required>
                    <p id="paymentPctLabel" class="mt-1.5 text-xs font-semibold text-slate-500"></p>
                    @error('amount')<p class="pb-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pb-field">
                    <label class="pb-label">Payment Method <span class="text-pink-600">*</span></label>
                    <select name="payment_method" class="pb-input" required>
                        @foreach (config('printbuka_admin.payment_methods', ['Bank Transfer','Cash','POS','Cheque','Online (Paystack)','Other']) as $pm)
                            <option value="{{ $pm }}" @selected(old('payment_method', 'Bank Transfer') === $pm)>{{ $pm }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<p class="pb-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pb-field">
                    <label class="pb-label">Date Paid <span class="text-pink-600">*</span></label>
                    <input type="date" name="paid_at" value="{{ old('paid_at', now()->toDateString()) }}"
                        class="pb-input" required>
                    @error('paid_at')<p class="pb-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="pb-field">
                    <label class="pb-label">Reference / Transaction ID</label>
                    <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                        class="pb-input"
                        placeholder="e.g. TRF-00123456">
                </div>
                <div class="pb-field sm:col-span-2">
                    <label class="pb-label">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                        class="pb-input"
                        placeholder="Optional note about this payment">
                </div>
                <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-3">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-success">
                        Record Payment
                    </button>
                </div>
            </form>

            {{-- Mark Fully Paid — separate standalone form, NOT nested --}}
            <div class="mt-4 border-t border-emerald-200/60 pt-4">
                <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                        onclick="return confirm('Mark this invoice as fully paid without recording individual payments?')"
                        class="pb-btn pb-btn-md pb-btn-outline">
                        Mark Fully Paid (skip breakdown)
                    </button>
                </form>
            </div>
        </div>

        <script>
        (function () {
            var invoiceTotal = {{ $invoiceTotal }};
            var alreadyPaid  = {{ $paidSoFar }};

            function milestone(totalPct) {
                if (totalPct >= 100) return { label: 'Invoice Settled (100%)', color: '#059669' };
                if (totalPct >= 70)  return { label: 'Invoice Settled (70%)', color: '#d97706' };
                if (totalPct > 0)    return { label: 'Part Payment', color: '#e11d48' };
                return { label: '', color: '#64748b' };
            }

            window.setPaymentAmount = function (val) {
                var input = document.getElementById('paymentAmountInput');
                if (input) { input.value = parseFloat(val).toFixed(2); window.updatePaymentPct(input.value); }
            };

            window.updatePaymentPct = function (val) {
                var lbl = document.getElementById('paymentPctLabel');
                if (!lbl || !invoiceTotal) return;
                var amt = parseFloat(val) || 0;
                var thisPct   = (amt / invoiceTotal) * 100;
                var totalPct  = ((alreadyPaid + amt) / invoiceTotal) * 100;
                var m = milestone(totalPct);
                lbl.innerHTML = 'This payment: <strong>' + thisPct.toFixed(1) + '%</strong>'
                    + ' &nbsp;·&nbsp; Total after: <strong style="color:' + m.color + '">'
                    + totalPct.toFixed(1) + '%</strong>'
                    + (m.label ? ' &nbsp;<span style="color:' + m.color + '">(' + m.label + ')</span>' : '');
            };

            // Run on page load to show label for default value
            var initInput = document.getElementById('paymentAmountInput');
            if (initInput) window.updatePaymentPct(initInput.value);
        })();
        </script>
        @endif

        {{-- Payment History --}}
        @php $payments = $invoice->payments()->with('recorder')->latest()->get(); @endphp
        @if ($payments->isNotEmpty())
        <div class="pb-card p-6">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="pb-section-title">Payment History</h2>
                <span class="ml-auto text-sm font-semibold text-slate-500">{{ $payments->count() }} {{ Str::plural('payment', $payments->count()) }}</span>
            </div>
            <div class="pb-table-wrapper">
                <table class="pb-table min-w-[560px]">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                        <tr>
                            <td class="font-semibold text-slate-700">{{ $payment->paid_at->format('M j, Y') }}</td>
                            <td class="text-right font-bold text-emerald-700">₦{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td class="font-mono text-xs">{{ $payment->payment_reference ?: '—' }}</td>
                            <td>{{ $payment->recorder?->displayName() ?? 'System' }}</td>
                            <td class="text-xs">{{ $payment->notes ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200">
                        <tr>
                            <td class="font-bold text-slate-700">Total Recorded</td>
                            <td class="text-right font-bold text-emerald-700">₦{{ number_format($payments->sum('amount'), 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- Notes Section --}}
        <div class="pb-card p-6">
            <h2 class="pb-section-title mb-6">Notes</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="pb-label">Artwork Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $invoice->order?->artwork_notes ?: 'None' }}</p>
                </div>
                <div>
                    <p class="pb-label">Internal Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $invoice->order?->internal_notes ?: 'None' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection