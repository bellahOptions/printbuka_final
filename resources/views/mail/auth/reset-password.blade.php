<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>Reset your password</title>
</head>
@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
@endphp
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

                @include('mail.partials.header', [
                    'headerBadge' => 'PASSWORD RESET',
                    'headerTitle' => 'Reset your password',
                    'headerSubtitle' => 'We received a request to reset the password for your account.',
                ])

                {{-- Body --}}
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $user->first_name ?? $user->displayName() }},</p>
                        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#475569;">
                            Someone recently requested a password reset for your {{ $siteName }} account.
                            If this was you, click the button below to set a new password.
                        </p>

                        {{-- CTA Button --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $resetUrl }}"
                                       style="display:inline-block;padding:14px 36px;background:#EC268F;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:.3px;">
                                        Reset Password &rarr;
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Expiry + security note --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;">
                                    <p style="margin:0 0 8px;font-size:13px;color:#374151;line-height:1.6;">
                                        <strong>Link expires in {{ $expiryMinutes }} minutes.</strong>
                                        After that you'll need to submit a new request.
                                    </p>
                                    <p style="margin:0;font-size:13px;color:#64748b;line-height:1.6;">
                                        If you didn't request a password reset, you can safely ignore this email.
                                        Your password will not be changed.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- Fallback URL --}}
                        <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;line-height:1.6;">
                            If the button above doesn't work, copy and paste this URL into your browser:
                        </p>
                        <p style="margin:0;font-size:11px;color:#64748b;word-break:break-all;line-height:1.7;">
                            {{ $resetUrl }}
                        </p>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 28px;">
                        <p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;line-height:1.6;">
                            For your security, this link can only be used once.
                        </p>
                        <p style="margin:8px 0 0;font-size:11px;color:#cbd5e1;text-align:center;">
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
