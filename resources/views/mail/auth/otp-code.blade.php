<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>Your sign-in code</title>
</head>
@php
    $siteName = trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka'));
@endphp
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

                @include('mail.partials.header', [
                    'headerBadge' => 'SIGN-IN CODE',
                    'headerTitle' => 'Your verification code',
                    'headerSubtitle' => 'Use this code to finish signing in to your staff account.',
                ])

                {{-- Body --}}
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $user->first_name ?? $user->displayName() }},</p>
                        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#475569;">
                            Enter the code below to verify it's you. This code was requested for your {{ $siteName }} staff account.
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td align="center">
                                    <span style="display:inline-block;padding:16px 32px;background:#f1f5f9;border-radius:10px;font-size:32px;font-weight:700;letter-spacing:0.3em;color:#0f172a;">{{ $code }}</span>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:14px 16px;">
                                    <p style="margin:0;font-size:13px;color:#713f12;line-height:1.6;">
                                        <strong>Note:</strong> This code expires in <strong>{{ $expiryMinutes }} minutes</strong> and can only be used once.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                            If you didn't request this code, you can safely ignore this email — someone may have mistyped their email address while trying to sign in.
                        </p>
                    </td>
                </tr>

                {{-- Divider --}}
                <tr>
                    <td style="padding:0 28px;">
                        <hr style="border:none;border-top:1px solid #e2e8f0;margin:0;">
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 28px;">
                        <p style="margin:0;font-size:11px;color:#cbd5e1;text-align:center;">
                            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
