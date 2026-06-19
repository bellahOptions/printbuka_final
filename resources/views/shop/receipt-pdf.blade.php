<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->reference }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #0f172a; background: #fff; }
        .page { padding: 40px 48px; }
        .header { border-bottom: 3px solid #0f172a; padding-bottom: 20px; margin-bottom: 24px; }
        .header-grid { width: 100%; }
        .logo-cell { width: 50%; vertical-align: top; }
        .receipt-label-cell { width: 50%; text-align: right; vertical-align: top; }
        .logo { font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: -0.5px; }
        .logo-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .receipt-label { font-size: 28px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; }
        .receipt-badge { display: inline-block; background: #0f172a; color: #ffffff; font-size: 10px; font-weight: bold; padding: 3px 10px; border-radius: 4px; margin-top: 4px; letter-spacing: 1px; }
        .meta-section { margin-bottom: 24px; }
        .meta-grid { width: 100%; }
        .meta-left { width: 55%; vertical-align: top; }
        .meta-right { width: 45%; vertical-align: top; text-align: right; }
        .section-label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
        .meta-value { font-size: 13px; font-weight: bold; color: #0f172a; }
        .meta-sub { font-size: 11px; color: #475569; line-height: 1.7; margin-top: 4px; }
        .badge-paid { display: inline-block; background: #dcfce7; color: #15803d; font-size: 10px; font-weight: bold; padding: 3px 10px; border-radius: 12px; letter-spacing: .5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items-table th { background: #0f172a; color: #ffffff; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 10px 12px; text-align: left; }
        .items-table th.right { text-align: right; }
        .items-table th.center { text-align: center; }
        .items-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .items-table tr:last-child td { border-bottom: none; }
        .items-table .odd { background: #f8fafc; }
        .item-name { font-weight: 600; font-size: 12px; }
        .item-options { font-size: 10px; color: #64748b; margin-top: 3px; line-height: 1.5; }
        .td-center { text-align: center; }
        .td-right { text-align: right; }
        .totals-table { width: 260px; margin-left: auto; border-collapse: collapse; margin-bottom: 24px; }
        .totals-table td { padding: 7px 12px; font-size: 12px; }
        .totals-table .total-row td { background: #0f172a; color: #fff; font-weight: bold; font-size: 13px; }
        .totals-table .subtotal-label { color: #64748b; }
        .totals-table .subtotal-value { text-align: right; }
        .naira { font-family: DejaVu Sans, Arial, sans-serif; font-weight: bold; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
        .footer { border-top: 2px solid #e2e8f0; padding-top: 16px; }
        .footer-grid { width: 100%; }
        .footer-left { width: 60%; vertical-align: top; font-size: 10px; color: #64748b; line-height: 1.7; }
        .footer-right { width: 40%; vertical-align: top; text-align: right; font-size: 10px; color: #64748b; }
        .thank-you { font-size: 13px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
    </style>
</head>
@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
    $address  = trim((string) ($settings['contact_address'] ?? ''));
    $phone    = trim((string) ($settings['contact_phone'] ?? ''));
    $email    = trim((string) ($settings['contact_email'] ?? ''));
@endphp
<body>
<div class="page">
    {{-- Header --}}
    <div class="header">
        <table class="header-grid">
            <tr>
                <td class="logo-cell">
                    <div class="logo">{{ $siteName }}</div>
                    @if ($address)<div class="logo-sub">{{ $address }}</div>@endif
                    @if ($phone || $email)<div class="logo-sub">{{ implode(' | ', array_filter([$phone, $email])) }}</div>@endif
                </td>
                <td class="receipt-label-cell">
                    <div class="receipt-label">Receipt</div>
                    <div><span class="receipt-badge">PAID</span></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Meta --}}
    <div class="meta-section">
        <table class="meta-grid">
            <tr>
                <td class="meta-left">
                    <div class="section-label">Bill To</div>
                    <div class="meta-value">{{ $order->customer_name }}</div>
                    <div class="meta-sub">
                        {{ $order->customer_email }}<br>
                        @if ($order->customer_phone){{ $order->customer_phone }}<br>@endif
                        {{ $order->shipping_name }}<br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}
                    </div>
                </td>
                <td class="meta-right">
                    <div class="section-label">Receipt Details</div>
                    <div class="meta-sub">
                        <strong>Reference:</strong> {{ $order->reference }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                        <strong>Time:</strong> {{ $order->created_at->format('h:i A') }}<br>
                        @if ($order->paystack_reference)
                        <strong>Paystack Ref:</strong> {{ $order->paystack_reference }}<br>
                        @endif
                        <strong>Status:</strong> <span class="badge-paid">Paid</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="center" style="width:55px;">Qty</th>
                <th class="right" style="width:100px;">Unit Price</th>
                <th class="right" style="width:110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $i => $item)
            <tr class="{{ $i % 2 === 0 ? '' : 'odd' }}">
                <td>
                    <div class="item-name">{{ $item->product_name }}</div>
                    @if ($item->selectedOptions->isNotEmpty())
                    <div class="item-options">
                        @foreach ($item->selectedOptions as $opt)
                            {{ $opt->group_name }}: {{ $opt->option_name }}@if (! $loop->last), @endif
                        @endforeach
                    </div>
                    @endif
                </td>
                <td class="td-center">{{ $item->quantity }}</td>
                <td class="td-right"><span class="naira">₦</span>{{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="td-right"><span class="naira">₦</span>{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-table">
        @if ((float) $order->subtotal !== (float) $order->total)
        <tr>
            <td class="subtotal-label">Subtotal</td>
            <td class="subtotal-value"><span class="naira">₦</span>{{ number_format((float) $order->subtotal, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>Total Paid</td>
            <td style="text-align:right;"><span class="naira">₦</span>{{ number_format((float) $order->total, 2) }}</td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <table class="footer-grid">
            <tr>
                <td class="footer-left">
                    <div class="thank-you">Thank you for your order!</div>
                    This receipt confirms your payment. Please keep it for your records.
                    @if ($phone || $email)<br>For support: {{ implode(' | ', array_filter([$phone, $email])) }}@endif
                </td>
                <td class="footer-right">
                    &copy; {{ date('Y') }} {{ $siteName }}<br>
                    All rights reserved.
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
