<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>Phase Alert</title>
    </head>
    @php
        $logoUrl = asset('logo-dark.svg');
        $phaseName = (string) ($phase['phase'] ?? 'Workflow Update');
        $phaseOwner = (string) ($phase['responsible'] ?? 'Assigned team');
        $phaseGates = collect((array) ($phase['gates'] ?? []))
            ->map(fn ($gate): string => trim((string) $gate))
            ->filter(fn (string $gate): bool => $gate !== '')
            ->values();
        $adminOrderUrl = route('admin.orders.show', $order);
    @endphp
    <body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:28px 14px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;">
                        <tr>
                            <td style="background:#0f172a;padding:22px 24px;color:#ffffff;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:top;">
                                            <img src="{{ $logoUrl }}" alt="Printbuka" width="150" style="display:inline-block;height:auto;">
                                        </td>
                                        <td style="text-align:right;vertical-align:top;color:#cbd5e1;font-size:11px;font-weight:700;letter-spacing:0.06em;">
                                            PHASE NOTIFICATION
                                        </td>
                                    </tr>
                                </table>
                                <h1 style="margin:14px 0 0;font-size:26px;line-height:1.2;">A Job Has Entered Your Phase</h1>
                                <p style="margin:10px 0 0;color:#cbd5e1;line-height:1.6;font-size:14px;">Order {{ $order->job_order_number ?? $order->displayNumber() }} is now in <strong style="color:#fbcfe8;">{{ $phaseName }}</strong>.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">Hello {{ $recipient->displayName() }},</p>
                                <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">A workflow transition requires action from your team. Please review and process this job promptly.</p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 18px;">
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Job Number</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:700;">{{ $order->job_order_number ?? $order->displayNumber() }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Client</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $order->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Phase</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $phaseName }} ({{ $phaseOwner }})</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Status Change</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $oldStatus }} &rarr; {{ $newStatus }}</td>
                                    </tr>
                                </table>

                                @if ($phaseGates->isNotEmpty())
                                    <div style="margin:0 0 20px;padding:14px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
                                        <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#9d174d;">Phase Checklist</p>
                                        <ul style="margin:0;padding-left:18px;color:#7f1d1d;font-size:13px;line-height:1.6;">
                                            @foreach ($phaseGates as $gate)
                                                <li>{{ $gate }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <a href="{{ $adminOrderUrl }}" style="display:inline-block;background:#db2777;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 18px;border-radius:8px;">Open Job in Admin Dashboard</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
