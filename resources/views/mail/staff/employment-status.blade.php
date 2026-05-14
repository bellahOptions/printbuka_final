<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $statusLabel }}</title>
    </head>
    <body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:10px;overflow:hidden;">
                        <tr>
                            <td style="background:#0f172a;color:#ffffff;padding:24px;">
                                <h1 style="margin:0;font-size:24px;line-height:1.25;">{{ $statusLabel }}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:26px;line-height:1.6;">
                                <p style="margin:0 0 16px;">Hello {{ $staff->displayName() }},</p>

                                @if ($status === 'terminated')
                                    <p style="margin:0 0 16px;">This is to notify you that your contract with Printbuka has been terminated. Your staff account access has been disabled immediately.</p>
                                @elseif ($status === 'suspended')
                                    <p style="margin:0 0 16px;">This is to notify you that your Printbuka staff account has been suspended indefinitely. Your access has been disabled until management restores it.</p>
                                @else
                                    <p style="margin:0 0 16px;">Your Printbuka staff account has been activated for onboarding. You can now sign in with your verified email address.</p>
                                @endif

                                @if (filled($reason))
                                    <p style="margin:0 0 16px;"><strong>Note:</strong> {{ $reason }}</p>
                                @endif

                                <p style="margin:0;">Regards,<br>Printbuka Management</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
