@extends('mail.layouts.base')

@php
    $documentType = $invoice->documentTypeLabel();
    $settings = \App\Support\SiteSettings::all();
    $payToAccount = $invoice->resolvedCompanyAccount();
    $companyAccountName = trim((string) ($payToAccount?->account_name ?? $settings['company_account_name'] ?? ''));
    $companyAccountNumber = trim((string) ($payToAccount?->account_number ?? $settings['company_account_number'] ?? ''));
    $companyAccountBankName = trim((string) ($payToAccount?->bank_name ?? $settings['company_account_bank_name'] ?? ''));
    $companyAccountNote = trim((string) ($payToAccount?->note ?? $settings['company_account_note'] ?? ''));
    $hasCompanyAccountDetails = $companyAccountName !== '' || $companyAccountNumber !== '' || $companyAccountBankName !== '' || $companyAccountNote !== '';
@endphp

@section('title', 'Payment confirmed')
@section('headerBadge', 'RECEIPT')
@section('headerTitle', 'Payment confirmed')
@section('headerSubtitle', $documentType.' '.$invoice->invoice_number.' has been marked as paid.')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $invoice->order->customer_name }},</p>
    <p style="margin:0 0 16px;line-height:1.6;">We have successfully confirmed your payment. Your receipt is attached as a PDF in this email.</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:24px 0;">
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Order</td>
            <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Total Paid</td>
            <td style="padding:12px;border:1px solid #e2e8f0;">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Paid At</td>
            <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->paid_at?->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Estimated delivery</td>
            <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->estimated_delivery_at?->format('M d, Y h:i A') ?? 'To be confirmed' }}</td>
        </tr>
    </table>
    @if ($hasCompanyAccountDetails)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px 0;">
            <tr>
                <td style="padding:14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;">
                    <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:0.3px;">Company account details</p>
                    @if ($companyAccountBankName !== '')
                        <p style="margin:0 0 4px;line-height:1.5;"><strong>Bank:</strong> {{ $companyAccountBankName }}</p>
                    @endif
                    @if ($companyAccountName !== '')
                        <p style="margin:0 0 4px;line-height:1.5;"><strong>Account name:</strong> {{ $companyAccountName }}</p>
                    @endif
                    @if ($companyAccountNumber !== '')
                        <p style="margin:0 0 4px;line-height:1.5;"><strong>Account number:</strong> {{ $companyAccountNumber }}</p>
                    @endif
                    @if ($companyAccountNote !== '')
                        <p style="margin:0;line-height:1.5;"><strong>Note:</strong> {{ $companyAccountNote }}</p>
                    @endif
                </td>
            </tr>
        </table>
    @endif
    <p style="margin:0;line-height:1.6;">Thank you for choosing Printbuka.</p>
    {!! $outroHtml ?? '' !!}
@endsection
