<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payslip</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
    .page { padding: 30px 35px; }
    .header { border-bottom: 3px solid #0f172a; padding-bottom: 18px; margin-bottom: 18px; display: table; width: 100%; }
    .header-left { display: table-cell; vertical-align: top; width: 60%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }
    .company-name { font-size: 22px; font-weight: 700; color: #0f172a; }
    .payslip-label { font-size: 15px; font-weight: 700; color: #db2777; margin-top: 6px; }
    .period { font-size: 11px; color: #64748b; margin-top: 2px; }
    .confidential { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 8px; }
    .staff-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin-bottom: 18px; display: table; width: 100%; }
    .staff-left { display: table-cell; width: 60%; }
    .staff-right { display: table-cell; text-align: right; }
    .staff-name { font-size: 14px; font-weight: 700; color: #0f172a; }
    .staff-role { font-size: 11px; color: #64748b; margin-top: 2px; }
    .field-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-top: 8px; }
    .field-value { font-size: 11px; font-weight: 600; color: #475569; margin-top: 2px; }
    .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; background: #f1f5f9; padding: 6px 10px; margin-bottom: 0; border-radius: 4px 4px 0 0; }
    table.earnings { width: 100%; border-collapse: collapse; }
    table.earnings td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
    table.earnings tr:last-child td { border-bottom: none; }
    .td-label { width: 65%; color: #475569; }
    .td-value { text-align: right; font-weight: 600; }
    .td-deduction { text-align: right; font-weight: 600; color: #dc2626; }
    .totals-row { background: #f8fafc; }
    .totals-row td { padding: 9px 10px; font-weight: 700; font-size: 12px; }
    .net-row td { padding: 11px 10px; font-weight: 700; font-size: 14px; background: #0f172a; color: #fff; }
    .net-value { color: #34d399; text-align: right; }
    .two-col { display: table; width: 100%; margin-top: 16px; border-spacing: 12px 0; }
    .col-half { display: table-cell; width: 48%; vertical-align: top; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
    .footer { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 9px; color: #94a3b8; text-align: center; }
    .status-paid { background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">Printbuka</div>
            <div class="payslip-label">Employee Payslip</div>
            <div class="period">{{ $entry->payrollRun?->periodLabel() }}</div>
        </div>
        <div class="header-right">
            <div class="confidential">CONFIDENTIAL</div>
            <div class="field-label" style="margin-top:12px">Payment Status</div>
            <div class="field-value" style="margin-top:3px">
                @if ($entry->payment_status === 'paid')
                    <span class="status-paid">PAID</span>
                @else
                    <span style="color:#d97706;font-weight:700;font-size:11px">{{ strtoupper($entry->payment_status) }}</span>
                @endif
            </div>
            @if ($entry->paid_at)
                <div class="field-label">Payment Date</div>
                <div class="field-value">{{ $entry->paid_at->format('M j, Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Staff Details --}}
    <div class="staff-box">
        <div class="staff-left">
            <div class="staff-name">{{ $entry->staff?->displayName() }}</div>
            <div class="staff-role">{{ $entry->staff ? ucwords(str_replace('_', ' ', $entry->staff->role)) : '' }}</div>
        </div>
        <div class="staff-right">
            @if ($entry->staff?->staffProfile)
                <div class="field-label">Bank</div>
                <div class="field-value">{{ $entry->staff->staffProfile->bank_name }}</div>
                <div class="field-label">Account</div>
                <div class="field-value">{{ $entry->staff->staffProfile->bank_account_number }}</div>
            @endif
            @if ($entry->payment_method)
                <div class="field-label">Payment Method</div>
                <div class="field-value">{{ $entry->payment_method }}</div>
            @endif
        </div>
    </div>

    {{-- Earnings & Deductions side by side --}}
    <div class="two-col">
        <div class="col-half">
            <div class="section-title">Earnings</div>
            <table class="earnings">
                <tr><td class="td-label">Basic Salary</td><td class="td-value">&#8358;{{ number_format($entry->basic_salary, 2) }}</td></tr>
                @if ($entry->housing_allowance > 0)
                <tr><td class="td-label">Housing Allowance</td><td class="td-value">&#8358;{{ number_format($entry->housing_allowance, 2) }}</td></tr>
                @endif
                @if ($entry->transport_allowance > 0)
                <tr><td class="td-label">Transport Allowance</td><td class="td-value">&#8358;{{ number_format($entry->transport_allowance, 2) }}</td></tr>
                @endif
                @if ($entry->medical_allowance > 0)
                <tr><td class="td-label">Medical Allowance</td><td class="td-value">&#8358;{{ number_format($entry->medical_allowance, 2) }}</td></tr>
                @endif
                @if ($entry->other_allowances > 0)
                <tr><td class="td-label">Other Allowances</td><td class="td-value">&#8358;{{ number_format($entry->other_allowances, 2) }}</td></tr>
                @endif
                <tr class="totals-row"><td class="td-label" style="font-weight:700">Gross Salary</td><td class="td-value" style="font-weight:700">&#8358;{{ number_format($entry->gross_salary, 2) }}</td></tr>
            </table>
        </div>
        <div class="col-half" style="margin-left: 12px;">
            <div class="section-title">Deductions</div>
            <table class="earnings">
                @if ($entry->pension_deduction > 0)
                <tr><td class="td-label">Pension</td><td class="td-deduction">-&#8358;{{ number_format($entry->pension_deduction, 2) }}</td></tr>
                @endif
                @if ($entry->tax_deduction > 0)
                <tr><td class="td-label">Tax (PAYE)</td><td class="td-deduction">-&#8358;{{ number_format($entry->tax_deduction, 2) }}</td></tr>
                @endif
                @if ($entry->other_deductions > 0)
                <tr><td class="td-label">Other Deductions</td><td class="td-deduction">-&#8358;{{ number_format($entry->other_deductions, 2) }}</td></tr>
                @endif
                @if ($entry->total_deductions == 0)
                <tr><td class="td-label" style="color:#94a3b8">No deductions</td><td></td></tr>
                @endif
                <tr class="totals-row"><td class="td-label" style="font-weight:700">Total Deductions</td><td class="td-deduction" style="font-weight:700">-&#8358;{{ number_format($entry->total_deductions, 2) }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Net Pay --}}
    <table class="earnings" style="margin-top:0; border-radius:0 0 6px 6px; overflow:hidden; border: 1px solid #0f172a; border-top: none;">
        <tr class="net-row">
            <td class="td-label" style="color:#fff; font-size:14px;">NET PAY</td>
            <td class="net-value">&#8358;{{ number_format($entry->net_salary, 2) }}</td>
        </tr>
    </table>

    {{-- Reference --}}
    @if ($entry->payment_reference)
    <div style="margin-top:14px; font-size:10px; color:#64748b;">
        Payment Reference: <strong>{{ $entry->payment_reference }}</strong>
    </div>
    @endif

    <div class="footer">
        <p>This payslip is computer-generated and does not require a signature. &copy; {{ date('Y') }} Printbuka. All rights reserved.</p>
        <p style="margin-top:4px">Generated: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</div>
</body>
</html>
