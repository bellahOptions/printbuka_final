<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>{{ $campaign->subject }}</title>
    </head>
    <body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        @if (filled($campaign->preheader))
            <span style="display:none;max-height:0;overflow:hidden;">{{ $campaign->preheader }}</span>
        @endif
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:28px 14px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
                        <tr>
                            <td style="background:#0f172a;padding:24px;color:#ffffff;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:top;">
                                            <img src="{{ $message->embed(public_path('logo-dark.svg')) }}" alt="Printbuka" width="150" style="display:inline-block;height:auto;">
                                        </td>
                                        <td style="text-align:right;vertical-align:top;color:#67e8f9;font-size:11px;font-weight:700;letter-spacing:0.08em;">NEWSLETTER</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:26px;">
                                <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#db2777;text-transform:uppercase;letter-spacing:0.06em;">{{ $campaign->subject }}</p>
                                <p style="margin:0 0 20px;font-size:12px;color:#94a3b8;">Sent {{ $campaign->sent_at?->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }} by Printbuka</p>
                                {!! $bodyHtml !!}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:16px 26px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;">
                                You are receiving this because you registered on Printbuka.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
