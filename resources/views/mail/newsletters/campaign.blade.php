<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $campaign->subject }}</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:28px;">
                            @if (filled($campaign->preheader))
                                <p style="margin:0 0 8px;font-size:12px;color:#cbd5e1;">{{ $campaign->preheader }}</p>
                            @endif
                            <h1 style="margin:0;font-size:28px;">
                                {{ $campaign->headline ?: 'Latest News from Printbuka' }}
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px;">Hello {{ $customer->displayName() }},</p>
                            <p style="margin:0 0 20px;line-height:1.7;color:#334155;">
                                {!! nl2br(e($campaign->message)) !!}
                            </p>

                            @if (filled($campaign->cta_label) && filled($campaign->cta_url))
                                <a href="{{ $campaign->cta_url }}" style="display:inline-block;background:#db2777;color:#ffffff;padding:12px 18px;border-radius:6px;font-weight:700;text-decoration:none;">
                                    {{ $campaign->cta_label }}
                                </a>
                            @endif

                            <p style="margin:24px 0 0;font-size:12px;line-height:1.6;color:#64748b;">
                                You are receiving this because you registered on Printbuka.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

