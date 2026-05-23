<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Log - {{ $order->job_order_number ?? $order->displayNumber() }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; margin: 0; padding: 24px; font-size: 12px; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        p { margin: 4px 0; }
        .meta { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }
        .right { text-align: right; }
        .summary { margin-top: 16px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Expense Log</h1>
    <div class="meta">
        <p><strong>Job:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 14%;">Date</th>
                <th style="width: 16%;">Category</th>
                <th>Description</th>
                <th style="width: 16%;">Recorded By</th>
                <th style="width: 14%;" class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenseEntries as $entry)
                <tr>
                    <td>{{ optional($entry->entry_date)->format('M j, Y') ?? 'N/A' }}</td>
                    <td>{{ $entry->category ?? 'N/A' }}</td>
                    <td>{{ $entry->description ?? ($entry->notes ?? 'N/A') }}</td>
                    <td>{{ $entry->recorder?->displayName() ?? 'Unknown' }}</td>
                    <td class="right">₦{{ number_format((float) $entry->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No expense entries are attached to this job.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="summary">Total Expenses: ₦{{ number_format((float) $expenseEntries->sum('amount'), 2) }}</p>
</body>
</html>

