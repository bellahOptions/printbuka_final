<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>Order {{ $statusLabel }}</title>
</head>
@php
    $logoUrl = asset('logo-dark.svg');
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));

    $steps = [
        ['key' => 'order_received', 'label' => 'Order Received'],
        ['key' => 'processing',     'label' => 'Processing'],
        ['key' => 'dispatched',     'label' => 'Dispatched'],
        ['key' => 'delivered',      'label' => 'Delivered'],
    ];
    $stepKeys = array_column($steps, 'key');
    $currentIndex = array_search($newStatus, $stepKeys, true);
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
                                        Order Update
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:20px;display:inline-block;background:{{ $statusColor }};color:#fff;font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase;">
                                {{ $statusLabel }}
                            </div>
                            <h1 style="margin:10px 0 0;font-size:24px;line-height:1.2;">Order {{ $statusLabel }}</h1>
                            <p style="margin:8px 0 0;color:#94a3b8;line-height:1.5;font-size:14px;">Ref: <span style="font-family:monospace;">{{ $order->reference }}</span></p>
                        </td>
                    </tr>

                    {{-- Status Progress Bar --}}
                    <tr>
                        <td style="background:#f8fafc;padding:20px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    @foreach($steps as $i => $step)
                                    @php
                                        $isDone = $i <= $currentIndex;
                                        $isCurrent = $i === $currentIndex;
                                        $dotColor = $isDone ? $statusColor : '#e2e8f0';
                                        $dotBorder = $isCurrent ? "border:2px solid {$statusColor};" : '';
                                        $labelColor = $isDone ? '#0f172a' : '#94a3b8';
                                        $labelWeight = $isCurrent ? '700' : '400';
                                    @endphp
                                    <td align="center" style="padding:0;width:25%;vertical-align:top;">
                                        <div style="position:relative;">
                                            {{-- Line before dot --}}
                                            @if($i > 0)
                                            <div style="position:absolute;top:10px;left:0;right:50%;height:2px;background:{{ $i <= $currentIndex ? $statusColor : '#e2e8f0' }};"></div>
                                            @endif
                                            {{-- Line after dot --}}
                                            @if($i < count($steps) - 1)
                                            <div style="position:absolute;top:10px;left:50%;right:0;height:2px;background:{{ $i < $currentIndex ? $statusColor : '#e2e8f0' }};"></div>
                                            @endif
                                            {{-- Dot --}}
                                            <div style="width:20px;height:20px;border-radius:50%;background:{{ $dotColor }};margin:0 auto;position:relative;z-index:1;{{ $dotBorder }}"></div>
                                        </div>
                                        <p style="margin:6px 0 0;font-size:10px;color:{{ $labelColor }};font-weight:{{ $labelWeight }};text-align:center;letter-spacing:.3px;">{{ $step['label'] }}</p>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:24px 28px;">
                            <p style="margin:0 0 16px;font-size:15px;">Hello {{ $order->customer_name }},</p>
                            <p style="margin:0 0 24px;line-height:1.7;color:#475569;font-size:14px;">{{ $statusMessage }}</p>

                            {{-- Order summary box --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="border-collapse:collapse;background:#f8fafc;border-radius:8px;overflow:hidden;margin:0 0 24px;border:1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;" colspan="2">
                                        Order Details
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;width:40%;">Reference</td>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-size:13px;font-family:monospace;color:#0f172a;">{{ $order->reference }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Status</td>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                                        <span style="background:{{ $statusColor }};color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Order Total</td>
                                    <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:700;color:#16a34a;">NGN {{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#374151;">Delivery To</td>
                                    <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</td>
                                </tr>
                            </table>

                            @if($newStatus === 'dispatched')
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#ede9fe;border-radius:8px;overflow:hidden;margin:0 0 24px;border:1px solid #ddd6fe;">
                                <tr>
                                    <td style="padding:14px 16px;font-size:13px;color:#5b21b6;line-height:1.6;">
                                        <strong>Delivery info:</strong> Please ensure someone is available at <strong>{{ $order->shipping_address }}</strong> to receive the delivery. Contact us immediately if you experience any issues.
                                    </td>
                                </tr>
                            </table>
                            @endif

                            @if($newStatus === 'delivered')
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f0fdf4;border-radius:8px;overflow:hidden;margin:0 0 24px;border:1px solid #bbf7d0;">
                                <tr>
                                    <td style="padding:14px 16px;font-size:13px;color:#15803d;line-height:1.6;">
                                        We hope you love your order! If you have any feedback or concerns about your delivery, please don't hesitate to reach out to us.
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin:0 0 6px;font-size:13px;line-height:1.6;color:#475569;">
                                For questions, simply reply to this email or contact us. We're always happy to help!
                            </p>
                            <p style="margin:0;font-size:14px;font-weight:600;">Thank you for choosing {{ $siteName }}!</p>
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
