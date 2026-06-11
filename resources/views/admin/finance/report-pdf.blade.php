<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Printbuka Finance Report</title>
@php $fp = public_path('fonts'); @endphp
<style>
@@font-face { font-family:'Raleway'; font-weight:400; font-style:normal; src:url('{{ $fp }}/Raleway-Regular.ttf') format('truetype'); }
@@font-face { font-family:'Raleway'; font-weight:700; font-style:normal; src:url('{{ $fp }}/Raleway-Bold.ttf') format('truetype'); }
@@font-face { font-family:'Raleway'; font-weight:800; font-style:normal; src:url('{{ $fp }}/Raleway-ExtraBold.ttf') format('truetype'); }

* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: 'Raleway', 'DejaVu Sans', sans-serif;
    font-size: 10px;
    color: #1e293b;
    background: #ffffff;
    line-height: 1.5;
}

/* ₦ pinned to DejaVu Sans Bold — Raleway lacks this glyph and DejaVu has no weight >700 */
.naira { font-family: 'DejaVu Sans'; font-weight: bold; font-size: inherit; }

/* ── PAGE WRAPPER ── */
.page { padding: 30px 36px 28px; }

/* ── HEADER TABLE ── */
.hdr { width:100%; border-collapse:collapse; margin-bottom:0; }
.hdr td { vertical-align:middle; padding:0; }

.brand-logo { height:46px; width:auto; }

.brand-wordmark { font-size:26px; font-weight:800; color:#1e293b; }
.brand-wordmark em { color:#db2777; font-style:normal; }
.brand-tag { font-size:7.5px; color:#94a3b8; text-transform:uppercase; margin-top:3px; }

.doc-col { text-align:right; }
.doc-eyebrow {
    font-size:7.5px; font-weight:700; text-transform:uppercase;
    color:#94a3b8; margin-bottom:4px;
}
.doc-h1 { font-size:22px; font-weight:800; color:#be185d; }
.doc-period { font-size:10px; color:#475569; font-weight:600; margin-top:3px; }

/* ── ACCENT BAR ── */
.accent-bar {
    width:100%; height:4px;
    background:#be185d;
    margin:14px 0 0;
    border-radius:2px;
}
.accent-bar-thin {
    width:100%; height:1px;
    background:#f1f5f9;
    margin:0 0 16px;
}

/* ── META STRIP ── */
.meta { width:100%; border-collapse:collapse; margin-bottom:20px; }
.meta td { padding:6px 0 0; vertical-align:middle; }
.meta-item {
    display:inline-block;
    padding:3px 8px;
    border:1px solid #e2e8f0;
    border-radius:20px;
    font-size:7.5px;
    color:#64748b;
    font-weight:600;
    margin-right:4px;
}
.meta-right { text-align:right; font-size:8px; color:#94a3b8; }
.meta-right strong { color:#475569; }

/* ── KPI CARDS (table-based) ── */
.kpi-wrap { width:100%; border-collapse:separate; border-spacing:10px 0; margin-bottom:22px; }
.kpi-cell { width:33.33%; padding:0; vertical-align:top; }

.kpi-card {
    padding:14px 16px 12px;
    border-radius:8px;
    text-align:center;
    border-width:1px;
    border-style:solid;
}
.kpi-card.income { background:#f0fdf4; border-color:#6ee7b7; }
.kpi-card.expense { background:#fff1f2; border-color:#fca5a5; }
.kpi-card.net     { background:#eef2ff; border-color:#a5b4fc; }

.kpi-icon { font-size:14px; margin-bottom:4px; display:block; }
.kpi-label {
    font-size:7.5px; font-weight:700; text-transform:uppercase;
    display:block; margin-bottom:6px;
}
.kpi-card.income .kpi-label { color:#047857; }
.kpi-card.expense .kpi-label { color:#9f1239; }
.kpi-card.net     .kpi-label { color:#3730a3; }

.kpi-amount { font-size:20px; font-weight:800; display:block; line-height:1.1; }
.kpi-card.income .kpi-amount { color:#047857; }
.kpi-card.expense .kpi-amount { color:#be123c; }
.kpi-card.net     .kpi-amount { color:#1d4ed8; }

.kpi-sub { font-size:7.5px; display:block; margin-top:5px; color:#94a3b8; }

/* ── NET indicator strip ── */
.net-strip {
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:6px;
}
.net-strip td { padding:8px 14px; vertical-align:middle; font-size:9px; }

/* ── SECTION HEADING ── */
.section-hdr { width:100%; border-collapse:collapse; margin-bottom:10px; }
.section-hdr td { padding:0; vertical-align:bottom; }

.section-pill {
    display:inline-block;
    font-size:7px; font-weight:700; text-transform:uppercase;
    color:#be185d; background:#fdf2f8;
    padding:2px 8px; border-radius:3px;
    margin-bottom:4px;
}
.section-title { font-size:13px; font-weight:800; color:#0f172a; display:block; }
.section-count { font-size:9px; color:#64748b; font-weight:600; }

/* ── TRANSACTIONS TABLE ── */
.tx { width:100%; border-collapse:collapse; }

.tx thead th {
    background:#f8fafc;
    text-align:left;
    padding:8px 9px;
    font-size:7.5px;
    font-weight:700;
    text-transform:uppercase;
    color:#64748b;
    border-bottom:2px solid #e2e8f0;
    white-space:nowrap;
}
.tx thead th.r { text-align:right; }

.tx tbody td {
    padding:7px 9px;
    border-bottom:1px solid #f1f5f9;
    color:#334155;
    font-size:9px;
    vertical-align:top;
}
.tx tbody tr.even td { background:#fafbfc; }

.badge {
    display:inline-block;
    padding:2px 6px;
    border-radius:3px;
    font-size:7px;
    font-weight:700;
    text-transform:uppercase;
}
.badge-in  { background:#d1fae5; color:#065f46; }
.badge-out { background:#ffe4e6; color:#9f1239; }

.r { text-align:right; }
.fw { font-weight:700; }
.income-amt { color:#047857; font-weight:700; }
.expense-amt { color:#be123c; font-weight:700; }

.tx tfoot td {
    padding:8px 9px;
    border-top:2px solid #e2e8f0;
    background:#f8fafc;
    font-size:9px;
    font-weight:800;
}

/* ── EMPTY BOX ── */
.empty {
    padding:20px; text-align:center;
    border:1px dashed #cbd5e1; border-radius:6px;
    color:#94a3b8; font-size:9.5px;
}

/* ── FOOTER ── */
.footer {
    margin-top:28px;
    padding-top:12px;
    border-top:1px solid #e2e8f0;
}
.ftr { width:100%; border-collapse:collapse; }
.ftr td { padding:0; vertical-align:middle; }

.ftr-left { font-size:8px; color:#94a3b8; width:38%; }
.ftr-left strong { color:#475569; }
.ftr-center { text-align:center; font-size:7.5px; color:#cbd5e1; font-weight:700; text-transform:uppercase; }
.ftr-right { text-align:right; font-size:8px; color:#94a3b8; width:38%; }

.conf-tag {
    display:inline;
    font-size:6.5px; font-weight:700; text-transform:uppercase;
    color:#ef4444; border:1px solid #fca5a5;
    padding:1px 4px; border-radius:2px;
}
</style>
</head>
<body>
<div class="page">

{{-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ --}}
@php
    $logoPath = public_path('logo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<table class="hdr">
    <tr>
        <td style="width:52%;">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Printbuka" class="brand-logo">
            @else
                <div class="brand-wordmark">Print<em>buka</em></div>
                <div class="brand-tag">Your Print Partner</div>
            @endif
        </td>
        <td class="doc-col">
            <div class="doc-eyebrow">Confidential Financial Document</div>
            <div class="doc-h1">Finance Report</div>
            <div class="doc-period">
                @if($period === 'weekly')
                    Week of {{ now()->startOfWeek()->format('M j') }} &ndash; {{ now()->endOfWeek()->format('M j, Y') }}
                @elseif($period === 'monthly')
                    {{ now()->format('F Y') }}
                @elseif($period === 'custom')
                    {{ $dateFrom?->format('M j, Y') ?? 'Start' }} &ndash; {{ $dateTo?->format('M j, Y') ?? 'End' }}
                @endif
            </div>
        </td>
    </tr>
</table>

<div class="accent-bar"></div>
<div class="accent-bar-thin"></div>

{{-- ══════════════════════════════════════
     META STRIP
══════════════════════════════════════ --}}
<table class="meta">
    <tr>
        <td>
            <span class="meta-item">
                Report&nbsp;#{{ now()->format('Ymd') }}&ndash;{{ strtoupper(substr($period, 0, 3)) }}
            </span>
            <span class="meta-item">
                Period: {{ ucfirst($period) }}
            </span>
            @if(!empty($typeFilter))
                <span class="meta-item">Filter: {{ ucfirst($typeFilter) }} only</span>
            @endif
        </td>
        <td class="meta-right">
            Prepared by <strong>{{ $generatedBy?->displayName() ?? 'System' }}</strong>
            &nbsp;&middot;&nbsp; {{ now()->format('M j, Y h:i A') }}
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════
     KPI SUMMARY
══════════════════════════════════════ --}}
<table class="kpi-wrap">
    <tr>
        <td class="kpi-cell">
            <div class="kpi-card income">
                <span class="kpi-label">Total Income</span>
                <span class="kpi-amount"><span class="naira">₦</span>{{ number_format($incomeTotal, 2) }}</span>
                <span class="kpi-sub">Payments received in period</span>
            </div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-card expense">
                <span class="kpi-label">Total Expenses</span>
                <span class="kpi-amount"><span class="naira">₦</span>{{ number_format($expenseTotal, 2) }}</span>
                <span class="kpi-sub">Outgoing payments in period</span>
            </div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-card net">
                <span class="kpi-label">Net {{ $netTotal >= 0 ? 'Profit' : 'Loss' }}</span>
                <span class="kpi-amount"><span class="naira">₦</span>{{ number_format(abs($netTotal), 2) }}</span>
                <span class="kpi-sub">{{ $netTotal >= 0 ? 'Surplus for period' : 'Deficit for period' }}</span>
            </div>
        </td>
    </tr>
</table>

{{-- Margin indicator row --}}
@if($incomeTotal > 0)
<table class="net-strip" style="margin-bottom:22px;">
    <tr>
        <td style="color:#64748b;">
            Profit Margin&nbsp;&nbsp;
            <strong style="color:#0f172a;">{{ number_format(($netTotal / $incomeTotal) * 100, 1) }}%</strong>
        </td>
        <td style="text-align:center; color:#64748b;">
            Income-to-Expense Ratio&nbsp;&nbsp;
            @if($expenseTotal > 0)
                <strong style="color:#0f172a;">{{ number_format($incomeTotal / $expenseTotal, 2) }}x</strong>
            @else
                <strong style="color:#047857;">N/A (no expenses)</strong>
            @endif
        </td>
        <td style="text-align:right; color:#64748b;">
            Transactions&nbsp;&nbsp;
            <strong style="color:#0f172a;">{{ $entries->count() }}</strong>
        </td>
    </tr>
</table>
@endif

{{-- ══════════════════════════════════════
     TRANSACTIONS
══════════════════════════════════════ --}}
<table class="section-hdr">
    <tr>
        <td>
            <span class="section-pill">Ledger</span>
            <span class="section-title">Transaction Records</span>
        </td>
        <td style="text-align:right;">
            <span class="section-count">
                {{ $entries->count() }} {{ $entries->count() === 1 ? 'entry' : 'entries' }}
            </span>
        </td>
    </tr>
</table>

@if($entries->isEmpty())
    <div class="empty">No transactions recorded for the selected period and filters.</div>
@else
    <table class="tx">
        <thead>
            <tr>
                <th style="width:11%;">Date</th>
                <th style="width:9%;">Type</th>
                <th style="width:13%;">Category</th>
                <th style="width:29%;">Description</th>
                <th style="width:14%;">Payee</th>
                <th style="width:11%;">Method</th>
                <th class="r" style="width:13%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $i => $entry)
            <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                <td>{{ $entry->entry_date->format('M j, Y') }}</td>
                <td>
                    <span class="badge {{ $entry->type === 'income' ? 'badge-in' : 'badge-out' }}">
                        {{ ucfirst($entry->type) }}{{ $entry->entry_type === 'credit_from_ceo' ? ' CEO' : '' }}
                    </span>
                </td>
                <td>{{ $entry->category }}</td>
                <td style="color:#475569;">{{ $entry->description }}</td>
                <td style="color:#64748b;">{{ $entry->payee ?: '—' }}</td>
                <td style="color:#64748b;">{{ $entry->payment_method ?: '—' }}</td>
                <td class="r {{ $entry->type === 'income' ? 'income-amt' : 'expense-amt' }}">
                    {{ $entry->type === 'income' ? '+' : '-' }}&nbsp;<span class="naira">₦</span>{{ number_format($entry->amount, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="color:#64748b; font-weight:600; font-size:8.5px;">
                    Period totals &mdash; {{ $entries->count() }} transactions
                </td>
                <td colspan="2" style="text-align:right; color:#64748b; font-size:8.5px; font-weight:600;">
                    Income &nbsp;<span class="naira">₦</span>{{ number_format($incomeTotal, 2) }}
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    Expenses &nbsp;<span class="naira">₦</span>{{ number_format($expenseTotal, 2) }}
                </td>
                <td class="r {{ $netTotal >= 0 ? 'income-amt' : 'expense-amt' }}" style="font-size:11px;">
                    <span class="naira">₦</span>{{ number_format($netTotal, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ --}}
<div class="footer">
    <table class="ftr">
        <tr>
            <td class="ftr-left">
                Issued by <strong>{{ $generatedBy?->displayName() ?? 'System' }}</strong>
                &nbsp;&middot;&nbsp;
                <span class="conf-tag">Confidential</span>
            </td>
            <td class="ftr-center">
                Printbuka &middot; Your Print Partner
            </td>
            <td class="ftr-right">
                {{ now()->format('l, M j Y') }} &middot; {{ now()->format('h:i A') }}
            </td>
        </tr>
    </table>
</div>

</div>
</body>
</html>
