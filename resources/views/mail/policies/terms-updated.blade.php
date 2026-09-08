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
                    @include('mail.partials.header', [
                        'headerTitle' => 'Terms & Conditions Updated',
                        'headerSubtitle' => 'Please review the latest terms for using Printbuka services.',
                    ])
                    <tr>
                        <td style="padding:28px;">
                            {!! $introHtml ?? '' !!}
                            <p style="margin:0 0 16px;">Hello {{ $customer->displayName() }},</p>
                            <p style="margin:0 0 16px;line-height:1.6;">
                                We have updated our Terms & Conditions. Please review the latest version to stay informed on current order and service rules.
                            </p>
                            <p style="margin:0 0 20px;line-height:1.6;">
                                <strong>Updated on:</strong>
                                {{ optional($terms->updated_at)->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}
                            </p>
                            <a href="{{ $termsUrl }}" style="display:inline-block;background:#EC268F;color:#ffffff;padding:12px 18px;border-radius:6px;font-weight:700;text-decoration:none;">
                                Review Terms & Conditions
                            </a>
                            {!! $outroHtml ?? '' !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

