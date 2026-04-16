@extends('layouts.admin')

@section('title', ($invoice->exists ? 'Edit Invoice' : 'Create Invoice').' | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.invoices.index') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Invoices
                </a>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">{{ $invoice->exists ? 'Edit invoice' : 'Create invoice' }}</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">
                        {{ $invoice->exists ? 'Update invoice details and payment status.' : 'Generate a new invoice for an existing order.' }}
                    </p>
                </div>
                <div class="hidden sm:block">
                    <div class="rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 p-3 border border-cyan-500/20">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Summary -->
        @if ($errors->any())
            <div class="fade-in-up rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-black text-red-800">Please review the following issues:</p>
                        <ul class="mt-2 space-y-1 text-sm font-semibold text-red-700">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center gap-2">
                                    <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <form action="{{ $invoice->exists ? route('admin.invoices.update', $invoice) : route('admin.invoices.store') }}" method="POST" class="fade-in-up section-delay-1">
            @csrf
            @if ($invoice->exists) @method('PUT') @endif

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8 space-y-6">
                <!-- Order Selection -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                            <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Order Information</h2>
                            <p class="text-sm text-slate-500">Select the order for this invoice</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            Order *
                        </label>
                        <select name="order_id" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}" @selected((int) old('order_id', $invoice->order_id) === $order->id)>
                                    {{ $order->job_order_number ?? $order->displayNumber() }} · {{ $order->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Invoice Details Grid -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                            <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Invoice Details</h2>
                            <p class="text-sm text-slate-500">Enter invoice numbers and amounts</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Invoice Number *
                            </label>
                            <input name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="INV-XXX">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Subtotal (₦) *
                            </label>
                            <input type="number" min="0" step="0.01" name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="0.00">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                                </svg>
                                Tax (₦)
                            </label>
                            <input type="number" min="0" step="0.01" name="tax_amount" value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="0.00">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Discount (₦)
                            </label>
                            <input type="number" min="0" step="0.01" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="0.00">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Total (₦) *
                            </label>
                            <input type="number" min="0" step="0.01" name="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" required 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
                                   placeholder="0.00">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Status *
                            </label>
                            <select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                                @foreach (($invoiceStatuses ?? ['unpaid', 'paid', 'disputed']) as $value)
                                    <option value="{{ $value }}" @selected(old('status', $invoice->status) === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dates Section -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 border border-slate-200">
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Important Dates</h2>
                            <p class="text-sm text-slate-500">Set invoice timeline</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-3">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                Issued At
                            </label>
                            <input type="datetime-local" name="issued_at" value="{{ old('issued_at', $invoice->issued_at?->format('Y-m-d\\TH:i')) }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                Due At
                            </label>
                            <input type="datetime-local" name="due_at" value="{{ old('due_at', $invoice->due_at?->format('Y-m-d\\TH:i')) }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        </div>

                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                Sent At
                            </label>
                            <input type="datetime-local" name="sent_at" value="{{ old('sent_at', $invoice->sent_at?->format('Y-m-d\\TH:i')) }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                    <button type="submit" class="btn-primary group relative overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-8 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $invoice->exists ? 'Update Invoice' : 'Save Invoice' }}
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .section-delay-1 { animation-delay: 0.05s; }
    </style>
@endsection
