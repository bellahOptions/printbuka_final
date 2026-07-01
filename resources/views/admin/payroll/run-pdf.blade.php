<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payroll Run — {{ $run->periodLabel() }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
    .page { padding: 28px 32px; }

    .header { display: table; width: 100%; border-bottom: 3px solid #0f172a; padding-bottom: 16px; margin-bottom: 16px; }
    .header-left { display: table-cell; vertical-align: top; width: 65%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }
    .company { font-size: 20px; font-weight: 700; color: #0f172a; }
    .doc-title { font-size: 13px; font-weight: 700; color: #db2777; margin-top: 4px; }
    .period { font-size: 10px; color: #64748b; margin-top: 2px; }
    .meta-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-top: 8px; }
    .meta-value { font-size: 10px; font-weight: 600; color: #475569; margin-top: 1px; }

    .status-draft     { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
    .status-finalized { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
    .status-paid      { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; }

    .section-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 8px; margin-top: 18px; }

    table.entries { width: 100%; border-collapse: collapse; font-size: 9.5px; }
    table.entries thead tr { background: #0f172a; color: #fff; }
    table.entries thead th { padding: 7px 8px; text-align: left; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    table.entries thead th.right { text-align: right; }
    table.entries tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.entries tbody tr:nth-child(even) { background: #f8fafc; }
    table.entries tbody td { padding: 7px 8px; vertical-align: middle; }
    table.entries tbody td.right { text-align: right; font-weight: 600; }
    table.entries tbody td.deduct { text-align: right; font-weight: 600; color: #dc2626; }
    table.entries tfoot tr { background: #0f172a; color: #fff; }
    table.entries tfoot td { padding: 9px 8px; font-weight: 700; font-size: 10px; }
    table.entries tfoot td.right { text-align: right; }
    table.entries tfoot td.net { text-align: right; color: #34d399; font-size: 11px; }

    .staff-name { font-weight: 700; color: #0f172a; font-size: 10px; }
    .staff-role { color: #64748b; font-size: 8.5px; margin-top: 1px; }

    .method-badge { background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 6px; font-size: 8px; font-weight: 600; }

    .footer { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 8px; color: #94a3b8; text-align: center; }

    .confidential { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 8px; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company">Printbuka</div>
            <div class="doc-title">Payroll Summary Report</div>
            <div class="period">{{ $run->periodLabel() }}</div>
        </div>
        <div class="header-right">
            <div class="confidential">CONFIDENTIAL</div>
            <div style="margin-top:10px">
                @if ($run->status === 'paid')
                    <span class="status-paid">PAID</span>
                @elseif ($run->status === 'finalized')
                    <span class="status-finalized">FINALIZED</span>
                @else
                    <span class="status-draft">DRAFT</span>
                @endif
            </div>
            <div class="meta-label">Total Staff</div>
            <div class="meta-value">{{ $entries->count() }}</div>
            <div class="meta-label">Total Net Payroll</div>
            <div class="meta-value" style="color:#059669;font-size:13px;font-weight:700">&#8358;{{ number_format($totalNet, 2) }}</div>
            @if ($run->payment_date)
                <div class="meta-label">Payment Date</div>
                <div class="meta-value">{{ $run->payment_date->format('M j, Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Entries table --}}
    <div class="section-title">Staff Payroll Entries</div>
    <table class="entries">
        <thead>
            <tr>
                <th style="width:22%">Staff</th>
                <th style="width:12%">Role</th>
                <th class="right" style="width:12%">Basic</th>
                <th class="right" style="width:12%">Allowances</th>
                <th class="right" style="width:12%">Gross</th>
                <th class="right" style="width:12%">Deductions</th>
                <th class="right" style="width:12%">Net Pay</th>
                <th style="width:6%">Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
            <tr>
                <td>
                    <div class="staff-name">{{ $entry->staff?->displayName() ?? '—' }}</div>
                </td>
                <td>
                    <div class="staff-role">{{ $entry->staff ? ucwords(str_replace('_', ' ', $entry->staff->role)) : '—' }}</div>
                </td>
                <td class="right">&#8358;{{ number_format($entry->basic_salary, 2) }}</td>
                <td class="right">
                    &#8358;{{ number_format(
                        $entry->housing_allowance + $entry->transport_allowance + $entry->medical_allowance + $entry->other_allowances,
                        2
                    ) }}
                </td>
                <td class="right">&#8358;{{ number_format($entry->gross_salary, 2) }}</td>
                <td class="deduct">-&#8358;{{ number_format($entry->total_deductions, 2) }}</td>
                <td class="right" style="font-weight:700">&#8358;{{ number_format($entry->net_salary, 2) }}</td>
                <td>
                    @if ($entry->payment_method)
                        <span class="method-badge">{{ $entry->payment_method }}</span>
                    @else
                        <span style="color:#94a3b8">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="font-weight:700">TOTALS</td>
                <td class="right">&#8358;{{ number_format($totalGross, 2) }}</td>
                <td class="right" style="color:#fca5a5">-&#8358;{{ number_format($totalDeductions, 2) }}</td>
                <td class="net">&#8358;{{ number_format($totalNet, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Metadata row --}}
    <div style="display:table; width:100%; margin-top:20px;">
        <div style="display:table-cell; width:50%; vertical-align:top;">
            @if ($run->createdBy)
                <div class="meta-label">Prepared By</div>
                <div class="meta-value">{{ $run->createdBy->displayName() }}</div>
            @endif
            @if ($run->finalizedBy)
                <div class="meta-label" style="margin-top:8px">Finalized By</div>
                <div class="meta-value">{{ $run->finalizedBy->displayName() }}</div>
                <div style="font-size:8.5px; color:#94a3b8">{{ $run->finalized_at?->format('M j, Y g:i A') }}</div>
            @endif
        </div>
        <div style="display:table-cell; width:50%; vertical-align:top; text-align:right;">
            @if ($run->notes)
                <div class="meta-label">Notes</div>
                <div class="meta-value" style="max-width:240px; margin-left:auto">{{ $run->notes }}</div>
            @endif
            <div class="meta-label" style="margin-top:8px">Generated</div>
            <div class="meta-value">{{ now()->format('M j, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This payroll report is computer-generated and confidential. For internal use only. &copy; {{ date('Y') }} Printbuka.</p>
    </div>
</div>
</body>
</html>
