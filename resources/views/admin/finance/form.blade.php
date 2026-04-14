@extends('layouts.theme')

@section('title', ($entry->exists ? 'Edit Finance Entry' : 'Add Finance Entry').' | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900"><section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8"><a href="{{ route('admin.finance.index') }}" class="text-sm font-black text-cyan-300">Finance</a><h1 class="mt-3 text-4xl">{{ $entry->exists ? 'Edit finance entry.' : 'Add finance entry.' }}</h1></div>
        <form action="{{ $entry->exists ? route('admin.finance.update', $entry) : route('admin.finance.store') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($entry->exists) @method('PUT') @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black">Order<select name="order_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">No job</option>@foreach ($orders as $order)<option value="{{ $order->id }}" @selected((int) old('order_id', $entry->order_id) === $order->id)>{{ $order->job_order_number ?? $order->displayNumber() }} · {{ $order->customer_name }}</option>@endforeach</select></label>
                <label class="text-sm font-black">Date<input type="date" name="entry_date" value="{{ old('entry_date', $entry->entry_date?->format('Y-m-d')) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Type<select name="type" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="income" @selected(old('type', $entry->type) === 'income')>Income</option><option value="expense" @selected(old('type', $entry->type) === 'expense')>Expense</option></select></label>
                <label class="text-sm font-black">Category<input name="category" value="{{ old('category', $entry->category) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black sm:col-span-2">Description<input name="description" value="{{ old('description', $entry->description) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Vendor / Payee<input name="payee" value="{{ old('payee', $entry->payee) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Amount<input type="number" min="0" step="0.01" name="amount" value="{{ old('amount', $entry->amount) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Payment Method<input name="payment_method" value="{{ old('payment_method', $entry->payment_method) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black sm:col-span-2">Notes<textarea name="notes" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('notes', $entry->notes) }}</textarea></label>
            </div>
            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Entry</button>
        </form>
    </section></main>
@endsection
