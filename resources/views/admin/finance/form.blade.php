@extends('layouts.admin')

@section('title', ($entry->exists ? 'Edit Finance Entry' : 'Add Expense Entry').' | Printbuka')

@section('content')
    @php($isAutoIncome = (bool) ($isAutoIncome ?? false))
    <div class="mx-auto max-w-5xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
            <a href="{{ route('admin.finance.index') }}" class="text-sm font-black text-cyan-300">Finance</a>
            <h1 class="mt-3 text-4xl">
                {{ $isAutoIncome ? 'View auto income entry.' : ($entry->exists ? 'Edit expense entry.' : 'Add expense entry.') }}
            </h1>
            <p class="mt-2 text-sm text-slate-300">Income entries are generated automatically whenever an invoice is marked as paid.</p>
        </div>

        <form action="{{ $entry->exists ? route('admin.finance.update', $entry) : route('admin.finance.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($entry->exists) @method('PUT') @endif
            <input type="hidden" name="type" value="expense">

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black">
                    Order
                    <select name="order_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                        <option value="">No job</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" @selected((int) old('order_id', $entry->order_id) === $order->id)>
                                {{ $order->job_order_number ?? $order->displayNumber() }} · {{ $order->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-black">
                    Date
                    <input type="date" name="entry_date" value="{{ old('entry_date', $entry->entry_date?->format('Y-m-d')) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black">
                    Type
                    <input value="{{ ucfirst($entry->type ?: 'expense') }}" readonly class="mt-2 min-h-12 w-full rounded-md border border-slate-200 bg-slate-50 px-4 font-semibold text-slate-600">
                </label>

                <label class="text-sm font-black">
                    Category
                    <input name="category" value="{{ old('category', $entry->category) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black sm:col-span-2">
                    Description
                    <input name="description" value="{{ old('description', $entry->description) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black">
                    Vendor / Payee
                    <input name="payee" value="{{ old('payee', $entry->payee) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black">
                    Amount
                    <input type="number" min="0" step="0.01" name="amount" value="{{ old('amount', $entry->amount) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black">
                    Payment Method
                    <input name="payment_method" value="{{ old('payment_method', $entry->payment_method) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold" @disabled($isAutoIncome)>
                </label>

                <label class="text-sm font-black sm:col-span-2">
                    Notes
                    <textarea name="notes" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold" @disabled($isAutoIncome)>{{ old('notes', $entry->notes) }}</textarea>
                </label>
            </div>

            @if (! $isAutoIncome)
                <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                    Save Expense Entry
                </button>
            @else
                <a href="{{ route('admin.finance.index') }}" class="mt-6 inline-flex rounded-md bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-700">
                    Back To Finance
                </a>
            @endif
        </form>
    </div>
@endsection
