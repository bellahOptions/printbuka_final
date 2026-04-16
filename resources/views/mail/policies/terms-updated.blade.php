<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Terms & Conditions Updated</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:28px;">
                            <h1 style="margin:0;font-size:28px;">Terms & Conditions Updated</h1>
                            <p style="margin:12px 0 0;color:#cbd5e1;">Please review the latest terms for using Printbuka services.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px;">Hello {{ $customer->displayName() }},</p>
                            <p style="margin:0 0 16px;line-height:1.6;">
                                We have updated our Terms & Conditions. Please review the latest version to stay informed on current order and service rules.
                            </p>
                            <p style="margin:0 0 20px;line-height:1.6;">
                                <strong>Updated on:</strong>
                                {{ optional($terms->updated_at)->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}
                            </p>
                            <a href="{{ $termsUrl }}" style="display:inline-block;background:#db2777;color:#ffffff;padding:12px 18px;border-radius:6px;font-weight:700;text-decoration:none;">
                                Review Terms & Conditions
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

