@extends('layouts.admin')

@section('title', 'Finance Record #'.$entry->id.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Finance Detail</p>
                <h1 class="mt-2 text-4xl font-black text-slate-950">Finance Record #{{ $entry->id }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ ucfirst($entry->type) }} · {{ $entry->category }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.finance.download', $entry) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Download PDF</a>
                <a href="{{ route('admin.finance.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Back to Finance</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Date</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->entry_date->format('M j, Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Type</p>
                <p class="mt-2 text-sm text-slate-900">{{ ucfirst($entry->type) }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Entry Type</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->entryTypeLabel() }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Category</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->category }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Amount</p>
                <p class="mt-2 text-sm text-slate-900">₦{{ number_format($entry->amount, 2) }}</p>
            </div>
            @if ($entry->type === 'income')
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Status</p>
                    <p class="mt-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider {{ $entry->statusBadgeClass() }}">
                            {{ $entry->statusLabel() }}
                        </span>
                    </p>
                    @if ($entry->isRefunded())
                        <p class="mt-1 text-xs text-slate-500">
                            Refunded by {{ $entry->refundedBy?->displayName() ?? 'N/A' }} on {{ $entry->refunded_at?->format('M j, Y \a\t h:i A') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Details</h2>
            <div class="mt-4 space-y-4">
                <p><span class="font-black">Payee:</span> {{ $entry->payee ?: 'N/A' }}</p>
                <p><span class="font-black">Payment Method:</span> {{ $entry->payment_method ?: 'N/A' }}</p>
                <p><span class="font-black">Order:</span> {{ $entry->order?->job_order_number ?? 'N/A' }}</p>
                <p><span class="font-black">Recorded by:</span> {{ $entry->recorder?->displayName() ?? 'N/A' }} on {{ $entry->created_at->format('M j, Y \a\t h:i A') }}</p>
                @if ($entry->last_edited_by)
                    <p><span class="font-black">Last edited by:</span> {{ $entry->lastEditor?->displayName() ?? 'N/A' }} on {{ $entry->last_edited_at?->format('M j, Y \a\t h:i A') }}</p>
                @endif
            </div>
        </div>

        @if (auth()->user()?->canAdmin('finance.view') && $entry->type !== 'income')
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.finance.edit', $entry) }}" class="rounded-xl bg-pink-700 px-5 py-3 text-sm font-black text-white hover:bg-pink-800">Edit Entry</a>
                <form action="{{ route('admin.finance.destroy', $entry) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-black text-red-700 hover:bg-red-50">Delete Entry</button>
                </form>
            </div>
        @endif

        @if (auth()->user()?->canAdmin('finance.view') && $entry->type === 'income')
            <div class="flex flex-wrap gap-3">
                @if ($entry->isRefunded())
                    <form action="{{ route('admin.finance.unrefund', $entry) }}" method="POST" onsubmit="return confirm('Undo the refund on this income entry? It will count toward income totals again.')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 hover:bg-slate-50">Undo Refund</button>
                    </form>
                @else
                    <form action="{{ route('admin.finance.refund', $entry) }}" method="POST" onsubmit="return confirm('Mark this income entry as refunded? It will be excluded from income totals.')">
                        @csrf
                        <button class="rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white hover:bg-red-700">Mark as Refunded</button>
                    </form>
                @endif
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Notes</h2>
            <p class="mt-4 text-sm text-slate-700">{{ $entry->notes ?: 'No notes provided.' }}</p>
        </div>
    </div>
@endsection
