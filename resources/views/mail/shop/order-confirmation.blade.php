@extends('mail.layouts.base')

@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
@endphp

@section('title', 'Order confirmed')
@section('headerBadge', 'ORDER CONFIRMED')
@section('headerTitle', 'Payment Successful!')
@section('headerSubtitle', "Thank you for your order. We're getting it ready!")

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $order->customer_name }},</p>
    <p style="margin:0 0 24px;line-height:1.6;color:#475569;">
        Your payment has been confirmed and your order is now being processed.
        A PDF receipt is attached to this email for your records.
    </p>

    {{-- Order summary --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 24px;">
        <tr style="background:#f8fafc;">
            <td style="padding:12px 14px;border:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;" colspan="2">
                Order Summary
            </td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;width:160px;">Order Reference</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-family:monospace;">{{ $order->reference }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Order Date</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;">{{ $order->created_at->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Total Paid</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:700;color:#16a34a;">NGN {{ number_format((float) $order->total, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:600;">Ship To</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;">{{ $order->shipping_name }}, {{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</td>
        </tr>
    </table>

    {{-- Items --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 24px;">
        <tr style="background:#f8fafc;">
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Item</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;text-align:center;width:60px;">Qty</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;text-align:right;width:100px;">Total</td>
        </tr>
        @foreach ($order->items as $item)
        <tr>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;vertical-align:top;">
                <span style="font-weight:600;">{{ $item->product_name }}</span>
                @if ($item->selectedOptions->isNotEmpty())
                    <br>
                    @foreach ($item->selectedOptions as $opt)
                        <span style="font-size:12px;color:#64748b;">{{ $opt->group_name }}: {{ $opt->option_name }}</span>
                        @if (! $loop->last)<br>@endif
                    @endforeach
                @endif
            </td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;text-align:center;vertical-align:top;">{{ $item->quantity }}</td>
            <td style="padding:11px 14px;border:1px solid #e2e8f0;text-align:right;vertical-align:top;">NGN {{ number_format((float) $item->line_total, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background:#f0fdf4;">
            <td style="padding:12px 14px;border:1px solid #e2e8f0;font-weight:700;" colspan="2">Total</td>
            <td style="padding:12px 14px;border:1px solid #e2e8f0;font-weight:700;text-align:right;color:#16a34a;">NGN {{ number_format((float) $order->total, 2) }}</td>
        </tr>
    </table>

    <p style="margin:0 0 8px;line-height:1.6;color:#475569;">
        We will update you as your order progresses. If you have any questions, please don't hesitate to contact us.
    </p>
    <p style="margin:0;line-height:1.6;">Thank you for choosing {{ $siteName }}!</p>
    {!! $outroHtml ?? '' !!}
@endsection
