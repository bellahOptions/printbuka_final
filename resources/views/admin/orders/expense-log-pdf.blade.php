<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Log - {{ $order->job_order_number ?? $order->displayNumber() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #334155; margin: 0; padding: 28px 32px; font-size: 10px; line-height: 1.5; background: #ffffff; }
        /* ₦ pinned to DejaVu Sans explicitly — if dompdf ever falls through to
           a fallback font in the stack above, that font may have no glyph for
           U+20A6 and silently substitute "?". */
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

        .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 8px; }

        table.log { width: 100%; border-collapse: collapse; }
        table.log th { background: #f8fafc; text-align: left; padding: 8px 9px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        table.log th.right { text-align: right; }
        table.log td { padding: 8px 9px; border-bottom: 1px solid #f1f5f9; vertical-align: top; color: #334155; }
        table.log tbody tr:nth-child(even) { background: #fafbfc; }
        table.log td.right { text-align: right; font-weight: 600; }
        table.log tfoot td { padding: 10px 9px; border-top: 2px solid #e2e8f0; background: #f8fafc; font-weight: 800; font-size: 11px; color: #0f172a; }
        table.log tfoot td.right { text-align: right; }

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
                <p class="title">Expense Log</p>
                <p class="subtitle">{{ $order->job_order_number ?? $order->displayNumber() }}</p>
            </td>
            <td class="meta">
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>Generated {{ now()->format('F j, Y g:i A') }}</p>
            </td>
        </tr>
    </table>

    <div class="accent-bar"></div>

    <div class="section-title">Expense Entries</div>
    <table class="log">
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
                    <td class="right"><span class="naira">₦</span>{{ number_format((float) $entry->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No expense entries are attached to this job.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Expenses</td>
                <td class="right"><span class="naira">₦</span>{{ number_format((float) $expenseEntries->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {!! $outroHtml ?? '' !!}

    <div class="footer">
        <table class="ftr">
            <tr>
                <td>Generated on {{ now()->format('M j, Y g:i A') }}</td>
                <td class="ftr-right">{{ $order->job_order_number ?? $order->displayNumber() }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
