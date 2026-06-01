<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Job Log {{ $order->job_order_number }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                margin: 0;
                padding: 32px;
                font-family: 'Helvetica Neue', Arial, sans-serif;
                color: #1e293b;
                background: #ffffff;
                line-height: 1.5;
            }
            .brand-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 16px;
                border-bottom: 3px solid #db2777;
                margin-bottom: 24px;
            }
            .brand-left {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .brand-name {
                font-size: 22px;
                font-weight: 900;
                color: #1e293b;
                letter-spacing: -0.3px;
            }
            .brand-name span {
                color: #db2777;
            }
            .brand-tagline {
                font-size: 9px;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 2px;
                margin-top: 2px;
            }
            .brand-right {
                text-align: right;
            }
            .doc-title {
                font-size: 20px;
                font-weight: 900;
                color: #db2777;
                letter-spacing: -0.2px;
            }
            .doc-ref {
                font-size: 11px;
                color: #64748b;
                margin-top: 3px;
                font-weight: 600;
            }
            .info-bar {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 24px;
                padding: 14px 18px;
                background: #f8fafc;
                border-radius: 10px;
                border: 1px solid #e2e8f0;
            }
            .info-item {
                flex: 1 0 140px;
            }
            .info-item .label {
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #64748b;
                font-weight: 700;
                display: block;
                margin-bottom: 2px;
            }
            .info-item .value {
                font-size: 13px;
                font-weight: 700;
                color: #1e293b;
            }
            .section {
                margin-bottom: 22px;
            }
            .section-title {
                font-size: 13px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                color: #db2777;
                padding-bottom: 8px;
                border-bottom: 2px solid #f1f5f9;
                margin-bottom: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10.5px;
                margin-bottom: 6px;
            }
            thead th {
                background: #f8fafc;
                text-align: left;
                padding: 9px 10px;
                border-bottom: 2px solid #e2e8f0;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                font-size: 9px;
                color: #64748b;
            }
            tbody td {
                padding: 8px 10px;
                border-bottom: 1px solid #f1f5f9;
                color: #334155;
            }
            tbody tr:last-child td {
                border-bottom: none;
            }
            tbody tr:nth-child(even) {
                background: #fafbfc;
            }
            .amount-cell {
                text-align: right;
                font-weight: 700;
                font-variant-numeric: tabular-nums;
            }
            .total-row td {
                border-top: 2px solid #1e293b;
                font-weight: 900;
                font-size: 12px;
                color: #1e293b;
                padding-top: 10px;
                background: transparent !important;
            }
            .total-row .amount-cell {
                color: #991b1b;
                font-size: 14px;
            }
            .card {
                padding: 12px 14px;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background: #fafbfc;
            }
            .card p {
                margin: 0;
                font-size: 12px;
                color: #475569;
            }
            .card .label {
                font-weight: 700;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #64748b;
                display: block;
                margin-bottom: 4px;
            }
            .card + .card {
                margin-top: 10px;
            }
            .footer {
                margin-top: 32px;
                padding-top: 14px;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 10px;
                color: #94a3b8;
            }
            .footer strong {
                color: #475569;
            }
        </style>
    </head>
    <body>
        <div class="brand-header">
            <div class="brand-left">
                <div>
                    <div class="brand-name">Print<span>buka</span></div>
                    <div class="brand-tagline">Your Print Partner</div>
                </div>
            </div>
            <div class="brand-right">
                <div class="doc-title">JOB LOG</div>
                <div class="doc-ref">{{ $order->job_order_number }}</div>
            </div>
        </div>
        <div class="info-bar">
            <div class="info-item"><span class="label">Client</span><span class="value">{{ $order->customer_name }}</span></div>
            <div class="info-item"><span class="label">Job Type</span><span class="value">{{ $order->job_type }}</span></div>
            <div class="info-item"><span class="label">Quantity</span><span class="value">{{ $order->quantity }}</span></div>
            <div class="info-item"><span class="label">Status</span><span class="value">{{ $order->status }}</span></div>
            <div class="info-item"><span class="label">Payment</span><span class="value">{{ $order->payment_status }}</span></div>
            <div class="info-item"><span class="label">Delivery</span><span class="value">{{ $order->actual_delivery_at?->format('M j, Y') ?? 'Pending' }}</span></div>
            <div class="info-item"><span class="label">Total Amount</span><span class="value">&#8358;{{ number_format((float) $order->total_price, 2) }}</span></div>
        </div>

        <div class="section">
            <h2 class="section-title">Staff Activity Log</h2>
            @if (!isset($staffActivities) || $staffActivities->isEmpty())
                <div class="card"><p>No staff activities logged for this job.</p></div>
            @else
                <table>
                    <thead><tr><th style="width:20%;">Staff</th><th style="width:18%;">Role / Dept</th><th style="width:42%;">Action</th><th style="width:20%;">Date &amp; Time</th></tr></thead>
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
            <h2 class="section-title">Comments</h2>
            <div class="card"><span class="label">Internal Notes</span><p>{{ $order->internal_notes ?: 'No internal notes recorded.' }}</p></div>
            <div class="card"><span class="label">Phase Approval Comment</span><p>{{ $order->phase_approval_comment ?: 'No approval comments recorded.' }}</p></div>
        </div>

        <div class="section">
            <h2 class="section-title">Expense Journal (Debits only)</h2>
            @if ($expenseEntries->isEmpty())
                <div class="card"><p>No expense entries are attached to this job.</p></div>
            @else
                @php $totalExpenses = $expenseEntries->sum(fn($e) => (float) $e->amount); @endphp
                <table>
                    <thead><tr><th style="width:16%;">Date</th><th style="width:18%;">Category</th><th style="width:36%;">Description</th><th style="width:18%;">Recorded By</th><th style="width:12%;" class="amount-cell">Amount</th></tr></thead>
                    <tbody>
                        @foreach($expenseEntries as $entry)
                            <tr>
                                <td>{{ $entry->entry_date->format('M j, Y') }}</td>
                                <td>{{ $entry->category }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>{{ $entry->recorder?->displayName() ?? 'Unknown' }}</td>
                                <td class="amount-cell">&#8358;{{ number_format($entry->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="4" style="text-align:right; font-weight:800;">Total Expenses</td>
                            <td class="amount-cell">&#8358;{{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
        <div class="footer">
            <span>Generated by <strong>{{ $generatedBy?->displayName() ?? 'System' }}</strong></span>
            <span>{{ now()->format('M j, Y h:i A') }}</span>
        </div>
    </body>
</html>
