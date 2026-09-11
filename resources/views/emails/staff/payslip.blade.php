@extends('mail.layouts.base')

@section('title', 'Payslip')
@section('headerTitle', 'Printbuka Payslip')
@section('headerSubtitle', $entry->payrollRun?->periodLabel())
@section('footerNote', 'This payslip is confidential.')

@section('content')
    <style>
        .row{padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;overflow:hidden}
        .row .label{color:#64748b;float:left}
        .row .value{font-weight:600;color:#0f172a;float:right;text-align:right}
        .total-row{padding:12px 0;font-size:16px;font-weight:700;border-top:2px solid #0f172a;margin-top:8px;overflow:hidden}
        .total-row span:first-child{float:left}
        .total-row span:last-child{float:right;text-align:right}
    </style>
    {!! $introHtml ?? '' !!}
    <p style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $entry->staff?->displayName() }}</p>
    <p style="font-size:13px;color:#64748b;margin-bottom:24px">{{ $entry->staff?->role ? ucwords(str_replace('_', ' ', $entry->staff->role)) : '' }}</p>

    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:8px">Earnings</p>
    <div class="row"><span class="label">Basic Salary</span><span class="value">₦{{ number_format($entry->basic_salary, 2) }}</span></div>
    @if ($entry->housing_allowance > 0)<div class="row"><span class="label">Housing Allowance</span><span class="value">₦{{ number_format($entry->housing_allowance, 2) }}</span></div>@endif
    @if ($entry->transport_allowance > 0)<div class="row"><span class="label">Transport Allowance</span><span class="value">₦{{ number_format($entry->transport_allowance, 2) }}</span></div>@endif
    @if ($entry->medical_allowance > 0)<div class="row"><span class="label">Medical Allowance</span><span class="value">₦{{ number_format($entry->medical_allowance, 2) }}</span></div>@endif
    @if ($entry->other_allowances > 0)<div class="row"><span class="label">Other Allowances</span><span class="value">₦{{ number_format($entry->other_allowances, 2) }}</span></div>@endif
    <div class="row" style="font-weight:700"><span class="label">Gross Salary</span><span class="value">₦{{ number_format($entry->gross_salary, 2) }}</span></div>

    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-top:20px;margin-bottom:8px">Deductions</p>
    @if ($entry->pension_deduction > 0)<div class="row"><span class="label">Pension</span><span class="value" style="color:#ef4444">-₦{{ number_format($entry->pension_deduction, 2) }}</span></div>@endif
    @if ($entry->tax_deduction > 0)<div class="row"><span class="label">Tax (PAYE)</span><span class="value" style="color:#ef4444">-₦{{ number_format($entry->tax_deduction, 2) }}</span></div>@endif
    @if ($entry->other_deductions > 0)<div class="row"><span class="label">Other Deductions</span><span class="value" style="color:#ef4444">-₦{{ number_format($entry->other_deductions, 2) }}</span></div>@endif
    <div class="total-row"><span>Net Pay</span><span style="color:#059669">₦{{ number_format($entry->net_salary, 2) }}</span></div>

    <p style="font-size:12px;color:#94a3b8;text-align:center;margin-top:20px">Please find your detailed payslip PDF attached to this email.</p>
    {!! $outroHtml ?? '' !!}
@endsection
