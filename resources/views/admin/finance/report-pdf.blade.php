<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Finance Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .doc-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
            font-weight: 600;
        }

        .summary-grid {
            display: flex;
            gap: 14px;
            margin-bottom: 24px;
        }

        .summary-card {
            flex: 1;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
            display: block;
        }

        .summary-card .amount {
            font-size: 18px;
            font-weight: 900;
        }

        .summary-card.income {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .summary-card.income .amount {
            color: #065f46;
        }

        .summary-card.expense {
            border-color: #f43f5e;
            background: #fff1f2;
        }

        .summary-card.expense .amount {
            color: #9f1239;
        }

        .summary-card.net {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .summary-card.net .amount {
            color: #3730a3;
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

        tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .amount-cell {
            text-align: right;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .income-amount {
            color: #065f46;
        }

        .expense-amount {
            color: #9f1239;
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
            <div class="doc-title">FINANCE REPORT</div>
            <div class="doc-subtitle">
                @if ($period === 'weekly')
                    Week of {{ now()->startOfWeek()->format('M j') }} – {{ now()->endOfWeek()->format('M j, Y') }}
                @elseif ($period === 'monthly')
                    {{ now()->format('F Y') }}
                @elseif ($period === 'custom')
                    {{ $dateFrom?->format('M j, Y') ?? 'Start' }} – {{ $dateTo?->format('M j, Y') ?? 'End' }}
                @endif
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card income">
            <span class="label">Total Income</span>
            <span class="amount">₦{{ number_format($incomeTotal, 2) }}</span>
        </div>
        <div class="summary-card expense">
            <span class="label">Total Expenses</span>
            <span class="amount">₦{{ number_format($expenseTotal, 2) }}</span>
        </div>
        <div class="summary-card net">
            <span class="label">Net {{ $netTotal >= 0 ? 'Profit' : 'Loss' }}</span>
            <span class="amount">₦{{ number_format($netTotal, 2) }}</span>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Transactions ({{ $entries->count() }} entries)</h2>
        @if ($entries->isEmpty())
            <div class="card">
                <p>No transactions found for the selected period.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:13%;">Date</th>
                        <th style="width:10%;">Type</th>
                        <th style="width:14%;">Category</th>
                        <th style="width:28%;">Description</th>
                        <th style="width:13%;">Payee</th>
                        <th style="width:10%;">Method</th>
                        <th style="width:12%;" class="amount-cell">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>{{ $entry->entry_date->format('M j, Y') }}</td>
                            <td>
                                {{ ucfirst($entry->type) }}
                                @if($entry->entry_type === 'credit_from_ceo')
                                    (CEO)
                                @endif
                            </td>
                            <td>{{ $entry->category }}</td>
                            <td>{{ $entry->description }}</td>
                            <td>{{ $entry->payee ?: '—' }}</td>
                            <td>{{ $entry->payment_method ?: '—' }}</td>
                            <td class="amount-cell {{ $entry->type === 'income' ? 'income-amount' : 'expense-amount' }}">
                                {{ $entry->type === 'income' ? '+' : '-' }} ₦ {{ number_format($entry->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
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