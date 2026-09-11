@extends('mail.layouts.base')

@section('title', 'Payroll Summary')
@section('headerTitle', 'Printbuka Payroll Summary')
@section('headerSubtitle', $run->periodLabel())
@section('footerNote', 'This report is strictly confidential.')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="font-size:14px;color:#475569;margin-bottom:24px">
        Hello,<br><br>
        Please find attached the payroll summary for <strong>{{ $run->periodLabel() }}</strong>, sent by <strong>{{ $sentByName }}</strong>.
    </p>

    @php
        $entries         = $run->entries;
        $totalGross      = $entries->sum('gross_salary');
        $totalDeductions = $entries->sum('total_deductions');
        $totalNet        = $entries->sum('net_salary');
    @endphp

    <div style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="color:#64748b">Payroll Period</td><td align="right" style="font-weight:600;color:#0f172a">{{ $run->periodLabel() }}</td></tr></table></div>
    <div style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="color:#64748b">Status</td><td align="right" style="font-weight:600;color:#0f172a">{{ ucfirst($run->status) }}</td></tr></table></div>
    <div style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="color:#64748b">Total Staff</td><td align="right" style="font-weight:600;color:#0f172a">{{ $entries->count() }}</td></tr></table></div>
    <div style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="color:#64748b">Total Gross</td><td align="right" style="font-weight:600;color:#0f172a">₦{{ number_format($totalGross, 2) }}</td></tr></table></div>
    <div style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="color:#64748b">Total Deductions</td><td align="right" style="font-weight:600;color:#ef4444">-₦{{ number_format($totalDeductions, 2) }}</td></tr></table></div>
    <div style="padding:13px 0;font-size:16px;font-weight:700;border-top:2px solid #0f172a;margin-top:4px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td>Total Net Payroll</td><td align="right" style="color:#059669">₦{{ number_format($totalNet, 2) }}</td></tr></table></div>

    @if ($run->payment_date)
    <p style="font-size:13px;color:#64748b;margin-top:16px">Payment Date: <strong>{{ $run->payment_date->format('M j, Y') }}</strong></p>
    @endif

    <p style="font-size:12px;color:#94a3b8;text-align:center;margin-top:24px">The full payroll report PDF is attached to this email.</p>
    {!! $outroHtml ?? '' !!}
@endsection
