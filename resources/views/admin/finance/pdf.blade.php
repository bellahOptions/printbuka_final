<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Finance Record #{{ $entry->id }}</title>
        <style>
            body { margin: 0; padding: 24px; font-family: Arial, sans-serif; color: #1f2937; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
            .title { font-size: 24px; font-weight: 800; margin: 0; }
            .meta { text-align: right; }
            .meta p { margin: 0; font-size: 12px; color: #475569; }
            .box { border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; margin-bottom: 20px; }
            .box h2 { margin: 0 0 12px 0; font-size: 14px; text-transform: uppercase; letter-spacing: .08em; color: #64748b; }
            .field { margin-bottom: 10px; }
            .field-label { font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px; }
            .field-value { font-size: 14px; color: #0f172a; }
            .amount { font-size: 32px; font-weight: 800; margin: 0; color: #0f172a; }
        </style>
    </head>
    <body>
        <div class="header">
            <div>
                <p class="title">Finance Record</p>
                <p>Record #{{ $entry->id }}</p>
            </div>
            <div class="meta">
                <p>{{ $entry->entry_date->format('F j, Y') }}</p>
                <p>{{ ucfirst($entry->type) }}</p>
            </div>
        </div>

        <div class="box">
            <h2>Transaction Details</h2>
            <div class="field"><div class="field-label">Category</div><div class="field-value">{{ $entry->category }}</div></div>
            <div class="field"><div class="field-label">Description</div><div class="field-value">{{ $entry->description }}</div></div>
            <div class="field"><div class="field-label">Payee</div><div class="field-value">{{ $entry->payee ?: 'N/A' }}</div></div>
            <div class="field"><div class="field-label">Payment Method</div><div class="field-value">{{ $entry->payment_method ?: 'N/A' }}</div></div>
            <div class="field"><div class="field-label">Order</div><div class="field-value">{{ $entry->order?->job_order_number ?? 'N/A' }}</div></div>
        </div>

        <div class="box">
            <h2>Amount</h2>
            <p class="amount">₦{{ number_format($entry->amount, 2) }}</p>
        </div>

        <div class="box">
            <h2>Notes</h2>
            <p>{{ $entry->notes ?: 'No additional notes.' }}</p>
        </div>
    </body>
</html>
