<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Job Log {{ $order->job_order_number }}</title>
        <style>
            body { margin: 0; padding: 26px; font-family: Arial, sans-serif; color: #111827; }
            header { margin-bottom: 26px; }
            .title { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
            .subtitle { font-size: 12px; color: #475569; margin: 0; }
            .section { margin-bottom: 20px; }
            .section h2 { font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: .08em; color: #374151; }
            .meta-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .card { padding: 14px; border: 1px solid #e5e7eb; border-radius: 12px; }
            .card p { margin: 0; font-size: 12px; color: #111827; }
            .card .label { font-weight: 700; margin-bottom: 6px; display: block; color: #475569; }
            .expense-item { padding: 12px; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 10px; }
            .expense-item-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
            .expense-item-meta { font-size: 11px; color: #6b7280; }
            table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 16px; }
            th { background: #f8fafc; text-align: left; padding: 8px; border-bottom: 2px solid #e5e7eb; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; font-size: 10px; color: #475569; }
            td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        </style>
    </head>
    <body>
        <header>
            <p class="title">Job Log · {{ $order->job_order_number }}</p>
            <p class="subtitle">Client {{ $order->customer_name }} · Status {{ $order->status }} · {{ $order->payment_status }}</p>
        </header>

        <div class="section">
            <h2>Job information</h2>
            <div class="meta-grid">
                <div class="card"><span class="label">Job</span><p>{{ $order->job_type }}</p></div>
                <div class="card"><span class="label">Quantity</span><p>{{ $order->quantity }}</p></div>
                <div class="card"><span class="label">Delivered</span><p>{{ $order->actual_delivery_at?->format('M j, Y') ?? 'Pending' }}</p></div>
                <div class="card"><span class="label">Total</span><p>₦{{ number_format((float) $order->total_price, 2) }}</p></div>
            </div>
        </div>

        <div class="section">
            <h2>Staff Activity Log</h2>
            @if (!isset($staffActivities) || $staffActivities->isEmpty())
                <div class="card"><p>No staff activities logged for this job.</p></div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Role/Dept</th>
                            <th>Action</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffActivities as $activity)
                            <tr>
                                <td>{{ $activity->user?->displayName() ?? 'Unknown' }}</td>
                                <td>{{ $activity->role ?: ($activity->user?->role_label ?? '—') }}{{ $activity->department ? ' · '.$activity->department : '' }}</td>
                                <td>{{ $activity->action }}</td>
                                <td>{{ $activity->created_at->format('M j, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Comments</h2>
            <div class="card"><span class="label">Internal Notes</span><p>{{ $order->internal_notes ?: 'No internal notes recorded.' }}</p></div>
            <div class="card" style="margin-top: 10px;"><span class="label">Phase Approval Comment</span><p>{{ $order->phase_approval_comment ?: 'No approval comments recorded.' }}</p></div>
        </div>

        <div class="section">
            <h2>Expense Journal (Debits only)</h2>
            @if ($expenseEntries->isEmpty())
                <div class="card"><p>No expense entries are attached to this job.</p></div>
            @else
                @php $totalExpenses = $expenseEntries->sum(fn($e) => (float) $e->amount); @endphp

                @foreach ($expenseEntries as $entry)
                    <div class="expense-item">
                        <p class="expense-item-title">{{ $entry->category }} · ₦{{ number_format($entry->amount, 2) }}</p>
                        <p>{{ $entry->description }}</p>
                        <p class="expense-item-meta">{{ $entry->entry_date->format('M j, Y') }} · Recorded by {{ $entry->recorder?->displayName() ?? 'Unknown' }}</p>
                    </div>
                @endforeach

                <div class="card" style="margin-top: 12px; background: #fef2f2; border-color: #fecaca;">
                    <span class="label">Total Expenses</span>
                    <p style="font-size: 16px; font-weight: 800; color: #991b1b;">₦{{ number_format($totalExpenses, 2) }}</p>
                </div>
            @endif
        </div>
    </body>
</html>
