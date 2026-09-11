@extends('layouts.admin')

@section('title', 'Finance Record #'.$entry->id.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="pb-page-header">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-brand-700">Finance Detail</p>
                <h1 class="pb-page-title">Finance Record #{{ $entry->id }}</h1>
                <p class="pb-page-subtitle">{{ ucfirst($entry->type) }} · {{ $entry->category }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.finance.download', $entry) }}" class="pb-btn pb-btn-md pb-btn-outline">Download PDF</a>
                <a href="{{ route('admin.finance.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Back to Finance</a>
            </div>
        </div>

        <div class="pb-card pb-card-content grid gap-4 sm:grid-cols-2">
            <div>
                <p class="pb-label">Date</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->entry_date->format('M j, Y') }}</p>
            </div>
            <div>
                <p class="pb-label">Type</p>
                <p class="mt-2 text-sm text-slate-900">{{ ucfirst($entry->type) }}</p>
            </div>
            <div>
                <p class="pb-label">Entry Type</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->entryTypeLabel() }}</p>
            </div>
            <div>
                <p class="pb-label">Category</p>
                <p class="mt-2 text-sm text-slate-900">{{ $entry->category }}</p>
            </div>
            <div>
                <p class="pb-label">Amount</p>
                <p class="mt-2 text-sm text-slate-900">₦{{ number_format($entry->amount, 2) }}</p>
            </div>
            @if ($entry->type === 'income')
                <div>
                    <p class="pb-label">Status</p>
                    <p class="mt-2">
                        <span class="pb-badge {{ $entry->statusBadgeClass() }}">
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

        <div class="pb-card pb-card-content">
            <h2 class="pb-section-title">Details</h2>
            <div class="mt-4 space-y-4">
                <p><span class="font-semibold text-slate-900">Payee:</span> {{ $entry->payee ?: 'N/A' }}</p>
                <p><span class="font-semibold text-slate-900">Payment Method:</span> {{ $entry->payment_method ?: 'N/A' }}</p>
                <p><span class="font-semibold text-slate-900">Order:</span> {{ $entry->order?->job_order_number ?? 'N/A' }}</p>
                <p><span class="font-semibold text-slate-900">Recorded by:</span> {{ $entry->recorder?->displayName() ?? 'N/A' }} on {{ $entry->created_at->format('M j, Y \a\t h:i A') }}</p>
                @if ($entry->last_edited_by)
                    <p><span class="font-semibold text-slate-900">Last edited by:</span> {{ $entry->lastEditor?->displayName() ?? 'N/A' }} on {{ $entry->last_edited_at?->format('M j, Y \a\t h:i A') }}</p>
                @endif
            </div>
        </div>

        @if (auth()->user()?->canAdmin('finance.view') && $entry->type !== 'income')
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.finance.edit', $entry) }}" class="pb-btn pb-btn-md pb-btn-primary">Edit Entry</a>
                <form action="{{ route('admin.finance.destroy', $entry) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                    @csrf
                    @method('DELETE')
                    <button class="pb-btn pb-btn-md pb-btn-destructive">Delete Entry</button>
                </form>
            </div>
        @endif

        @if (auth()->user()?->canAdmin('finance.view') && $entry->type === 'income')
            <div class="flex flex-wrap gap-3">
                @if ($entry->isRefunded())
                    <form action="{{ route('admin.finance.unrefund', $entry) }}" method="POST" onsubmit="return confirm('Undo the refund on this income entry? It will count toward income totals again.')">
                        @csrf
                        @method('DELETE')
                        <button class="pb-btn pb-btn-md pb-btn-outline">Undo Refund</button>
                    </form>
                @else
                    <form action="{{ route('admin.finance.refund', $entry) }}" method="POST" onsubmit="return confirm('Mark this income entry as refunded? It will be excluded from income totals.')">
                        @csrf
                        <button class="pb-btn pb-btn-md pb-btn-destructive">Mark as Refunded</button>
                    </form>
                @endif
            </div>
        @endif

        <div class="pb-card pb-card-content">
            <h2 class="pb-section-title">Notes</h2>
            <p class="mt-4 text-sm text-slate-700">{{ $entry->notes ?: 'No notes provided.' }}</p>
        </div>
    </div>
@endsection
