<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>Order confirmed</title>
</head>
@php
    $logoUrl = asset('logo-dark.svg');
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
@endphp
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:top;">
                                        <img src="{{ $message->embed(public_path('logo-dark.svg')) }}" alt="{{ $siteName }}" width="140" style="display:inline-block;height:auto;">
                                    </td>
                                    <td style="text-align:right;vertical-align:middle;color:#cbd5e1;font-size:11px;text-transform:uppercase;letter-spacing:1px;">
                                        Order Confirmed
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin:20px 0 0;font-size:26px;line-height:1.2;">Payment Successful!</h1>
                            <p style="margin:8px 0 0;color:#94a3b8;line-height:1.5;">Thank you for your order. We're getting it ready!</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px;">
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
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 28px;text-align:center;color:#94a3b8;font-size:12px;">
                            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
