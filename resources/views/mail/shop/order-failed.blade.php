@extends('mail.layouts.base')

@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
    $phone    = trim((string) ($settings['contact_phone'] ?? ''));
    $email    = trim((string) ($settings['contact_email'] ?? ''));
@endphp

@section('title', 'Payment unsuccessful')
@section('headerBadge', 'PAYMENT FAILED')
@section('headerTitle', 'Payment Unsuccessful')
@section('headerSubtitle', 'Unfortunately your payment could not be processed.')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $order->customer_name }},</p>
    <p style="margin:0 0 24px;line-height:1.6;color:#475569;">
        We're sorry — the payment for your order was not completed successfully.
        No funds have been charged. You're welcome to try again at any time.
    </p>

    {{-- Order details --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 24px;">
        <tr style="background:#f8fafc;">
            <td style="padding:12px 14px;border:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;" colspan="2">
                Order Details
            </td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;width:160px;">Order Reference</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-family:monospace;">{{ $order->reference }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Date</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;">{{ $order->created_at->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Amount</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;">NGN {{ number_format((float) $order->total, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Status</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;color:#dc2626;font-weight:700;">Payment Failed</td>
        </tr>
    </table>

    {{-- Retry CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
        <tr>
            <td align="center" style="padding:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;">
                <p style="margin:0 0 12px;font-weight:600;color:#991b1b;">Would you like to try again?</p>
                <a href="{{ route('shop.index') }}" style="display:inline-block;padding:12px 28px;background:#0f172a;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;font-size:14px;">
                    Return to Shop
                </a>
            </td>
        </tr>
    </table>

    @if ($phone || $email)
    <p style="margin:0 0 8px;line-height:1.6;color:#475569;">
        If funds were unexpectedly deducted or you need assistance, please contact us:
    </p>
    <p style="margin:0;line-height:1.8;color:#475569;">
        @if ($phone)<strong>Phone:</strong> {{ $phone }}<br>@endif
        @if ($email)<strong>Email:</strong> {{ $email }}@endif
    </p>
    @else
    <p style="margin:0;line-height:1.6;color:#475569;">
        If you need any assistance, please contact our support team.
    </p>
    @endif
    {!! $outroHtml ?? '' !!}
@endsection
