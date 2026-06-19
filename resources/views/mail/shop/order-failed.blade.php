<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>Payment unsuccessful</title>
</head>
@php
    $logoUrl = asset('logo-dark.svg');
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
    $phone    = trim((string) ($settings['contact_phone'] ?? ''));
    $email    = trim((string) ($settings['contact_email'] ?? ''));
@endphp
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background:#7f1d1d;color:#ffffff;padding:24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:top;">
                                        <img src="{{ $message->embed(public_path('logo-dark.svg')) }}" alt="{{ $siteName }}" width="140" style="display:inline-block;height:auto;">
                                    </td>
                                    <td style="text-align:right;vertical-align:middle;color:#fca5a5;font-size:11px;text-transform:uppercase;letter-spacing:1px;">
                                        Payment Failed
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin:20px 0 0;font-size:26px;line-height:1.2;">Payment Unsuccessful</h1>
                            <p style="margin:8px 0 0;color:#fca5a5;line-height:1.5;">Unfortunately your payment could not be processed.</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px;">
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
