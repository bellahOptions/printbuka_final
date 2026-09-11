<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Finance Record #{{ $entry->id }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { padding: 28px 32px; font-family: Arial, sans-serif; color: #334155; background: #ffffff; font-size: 10px; line-height: 1.5; }
            /* ₦ pinned to DejaVu Sans — Arial lacks this glyph */
            .naira { font-family: 'DejaVu Sans', sans-serif; font-weight: bold; font-size: inherit; }

            .hdr { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
            .hdr td { vertical-align: top; padding: 0; }
            .title { font-size: 22px; font-weight: 800; margin: 0; color: #0f172a; }
            .subtitle { font-size: 10px; color: #db2777; font-weight: 700; margin-top: 3px; text-transform: uppercase; letter-spacing: .05em; }
            .meta { text-align: right; }
            .meta p { margin: 0 0 4px; font-size: 9.5px; color: #64748b; }
            .meta p:last-child { margin-bottom: 0; }
            .meta p strong { color: #334155; }

            .accent-bar { width: 100%; height: 3px; background: #db2777; border-radius: 2px; margin-bottom: 20px; }

            .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; }
            .badge-income { background: #d1fae5; color: #065f46; }
            .badge-expense { background: #ffe4e6; color: #9f1239; }

            .box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
            .box h2 { margin: 0 0 12px 0; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }
            .field { margin-bottom: 9px; }
            .field:last-child { margin-bottom: 0; }
            .field-label { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; margin-bottom: 3px; }
            .field-value { font-size: 11px; color: #0f172a; font-weight: 600; }

            .amount { font-size: 28px; font-weight: 800; margin: 0; color: #0f172a; }
            .notes-text { font-size: 10px; color: #475569; }

            .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
            .ftr { width: 100%; border-collapse: collapse; }
            .ftr td { padding: 0; font-size: 8.5px; color: #94a3b8; vertical-align: middle; }
            .ftr-right { text-align: right; }
        </style>
    </head>
    <body>
        {!! $introHtml ?? '' !!}

        <table class="hdr">
            <tr>
                <td>
                    <p class="title">Finance Record</p>
                    <p class="subtitle">Record #{{ $entry->id }}</p>
                </td>
                <td class="meta">
                    <p><strong>{{ $entry->entry_date->format('F j, Y') }}</strong></p>
                    <p><span class="badge {{ $entry->type === 'income' ? 'badge-income' : 'badge-expense' }}">{{ ucfirst($entry->type) }}</span> &middot; {{ $entry->entryTypeLabel() }}</p>
                </td>
            </tr>
        </table>

        <div class="accent-bar"></div>

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
            <p class="amount"><span class="naira">₦</span>{{ number_format($entry->amount, 2) }}</p>
        </div>

        <div class="box">
            <h2>Notes</h2>
            <p class="notes-text">{{ $entry->notes ?: 'No additional notes.' }}</p>
        </div>

        {!! $outroHtml ?? '' !!}

        <div class="footer">
            <table class="ftr">
                <tr>
                    <td>Generated on {{ now()->format('M j, Y g:i A') }}</td>
                    <td class="ftr-right">Finance Record #{{ $entry->id }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>
