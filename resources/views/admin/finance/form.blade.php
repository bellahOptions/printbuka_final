@extends('layouts.admin')

@section('title', ($entry->exists ? 'Edit Finance Entry' : 'Add Expense Entry').' | Printbuka')

@section('content')
    @php($isAutoIncome = (bool) ($isAutoIncome ?? false))
    <div class="mx-auto max-w-5xl">
        <div class="pb-page-header">
            <div>
                <a href="{{ route('admin.finance.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Finance</a>
                <h1 class="pb-page-title">
                    {{ $isAutoIncome ? 'View auto income entry.' : ($entry->exists ? 'Edit expense entry.' : 'Add expense entry.') }}
                </h1>
                <p class="pb-page-subtitle">Income entries are generated automatically whenever an invoice is marked as paid.</p>
            </div>
        </div>

        <form action="{{ $entry->exists ? route('admin.finance.update', $entry) : route('admin.finance.store') }}" method="POST" class="pb-card" @if (! $entry->exists) data-idempotent-form @endif @unless ($isAutoIncome) data-confirm-amount="amount" data-confirm-label="this {{ $entry->exists ? 'updated' : 'new' }} expense entry" @endunless>
            @csrf
            @if ($entry->exists) @method('PUT') @endif
            @if (! $entry->exists)
                <input type="hidden" name="idempotency_key" data-idempotency-key>
            @endif
            <input type="hidden" name="type" value="expense">

            <div class="pb-card-content">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Order</label>
                        <select name="order_id" class="pb-select w-full" @disabled($isAutoIncome)>
                            <option value="">No job</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}" @selected((int) old('order_id', $entry->order_id) === $order->id)>
                                    {{ $order->job_order_number ?? $order->displayNumber() }} · {{ $order->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Date</label>
                        <input type="date" name="entry_date" value="{{ old('entry_date', $entry->entry_date?->format('Y-m-d')) }}" required class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Type</label>
                        <select name="type" class="pb-select w-full" @disabled($isAutoIncome)>
                            <option value="expense" @selected(old('type', $entry->type) === 'expense')>Expense</option>
                            <option value="income" @selected(old('type', $entry->type) === 'income')>Income</option>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Entry Type</label>
                        <select name="entry_type" class="pb-select w-full" @disabled($isAutoIncome)>
                            <option value="">Manual Entry</option>
                            <option value="credit_from_ceo" @selected(old('entry_type', $entry->entry_type) === 'credit_from_ceo')>Credit from CEO</option>
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Category</label>
                        <input name="category" value="{{ old('category', $entry->category) }}" required class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Description</label>
                        <input name="description" value="{{ old('description', $entry->description) }}" required class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Vendor / Payee</label>
                        <input name="payee" value="{{ old('payee', $entry->payee) }}" class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Amount</label>
                        <input type="number" min="0" step="0.01" name="amount" value="{{ old('amount', $entry->amount) }}" required class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Payment Method</label>
                        <input name="payment_method" value="{{ old('payment_method', $entry->payment_method) }}" class="pb-input w-full" @disabled($isAutoIncome)>
                    </div>

                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Notes</label>
                        <textarea name="notes" rows="4" data-rich-editor class="pb-textarea w-full" @disabled($isAutoIncome)>{{ old('notes', $entry->notes) }}</textarea>
                    </div>
                </div>

                @if (! $isAutoIncome)
                    <button class="pb-btn pb-btn-md pb-btn-primary mt-6">
                        Save Expense Entry
                    </button>
                @else
                    <a href="{{ route('admin.finance.index') }}" class="pb-btn pb-btn-md pb-btn-secondary mt-6">
                        Back To Finance
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection
