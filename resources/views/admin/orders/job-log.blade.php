@extends('layouts.admin')

@section('title', 'Job Log · '.$order->job_order_number.' | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="pb-page-header">
            <div>
                <p class="pb-label">Job Log</p>
                <h1 class="pb-page-title">{{ $order->job_order_number }}</h1>
                <p class="pb-page-subtitle">{{ $order->customer_name }} · {{ $order->status }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.orders.job-log.download', $order) }}" class="pb-btn pb-btn-md pb-btn-outline">Download PDF</a>
                <a href="{{ route('admin.orders.show', $order) }}" class="pb-btn pb-btn-md pb-btn-outline">Back to Job</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="pb-card p-6">
                <p class="pb-label">Job</p>
                <p class="mt-3 text-xl font-bold text-slate-900">{{ $order->job_order_number }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->job_type }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->quantity }} pcs</p>
            </div>
            <div class="pb-card p-6">
                <p class="pb-label">Customer</p>
                <p class="mt-3 text-lg font-bold text-slate-900">{{ $order->customer_name }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->customer_email }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $order->customer_phone }}</p>
            </div>
            <div class="pb-card p-6">
                <p class="pb-label">Status</p>
                <p class="mt-3 text-xl font-bold text-slate-900">{{ $order->status }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $order->payment_status }}</p>
            </div>
        </div>

        <div class="pb-card p-6">
            <h2 class="pb-section-title">Staff Activity Log</h2>
            <p class="pb-section-subtitle">Actions performed by staff during the work process of this job.</p>
            @if($staffActivities->isEmpty())
                <p class="mt-4 text-sm text-slate-500">No staff activities have been logged for this job yet.</p>
            @else
                <div class="mt-4 pb-table-wrapper">
                    <table class="pb-table">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Role / Department</th>
                                <th>Action</th>
                                <th>Date &amp; Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffActivities as $activity)
                                <tr>
                                    <td class="font-semibold text-slate-900">{{ $activity->user?->displayName() ?? 'Unknown' }}</td>
                                    <td>
                                        {{ $activity->role ?: ($activity->user?->role_label ?? '—') }}
                                        @if($activity->department)
                                            <span class="text-xs text-slate-400">· {{ $activity->department }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $activity->action }}</td>
                                    <td class="text-slate-500">{{ $activity->created_at->format('M j, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="pb-card p-6">
            <h2 class="pb-section-title">Job Comments</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <p class="pb-label">Internal Notes</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->internal_notes ?: 'No internal notes.' }}</p>
                </div>
                <div>
                    <p class="pb-label">Phase Approval Comment</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->phase_approval_comment ?: 'No approval comments yet.' }}</p>
                </div>
            </div>
        </div>

        <div class="pb-card p-6">
            <h2 class="pb-section-title">Expense Journal (Debits Only)</h2>
            @if($expenseEntries->isEmpty())
                <p class="mt-4 text-sm text-slate-500">No expense entries have been attached to this job yet.</p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach($expenseEntries as $entry)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-slate-900">{{ $entry->category }}</p>
                                <p class="text-sm font-semibold text-pink-700">-₦{{ number_format($entry->amount, 2) }}</p>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">{{ $entry->description }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ $entry->entry_date->format('M j, Y') }} · Recorded by {{ $entry->recorder?->displayName() ?? 'Unknown' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
