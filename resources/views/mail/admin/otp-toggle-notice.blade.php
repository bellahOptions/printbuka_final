<!DOCTYPE html>
<html lang="en" style="color-scheme:light;">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <title>OTP verification setting changed</title>
</head>
@php
    $siteName = trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka'));
    $state = $enabled ? 'turned ON' : 'turned OFF';
@endphp
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

                @include('mail.partials.header', [
                    'headerBadge' => 'SECURITY SETTING',
                    'headerTitle' => 'Email OTP verification '.$state,
                    'headerSubtitle' => 'This affects how staff without an authenticator app sign in.',
                ])

                {{-- Body --}}
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $recipient->first_name ?? $recipient->displayName() }},</p>

                        @if($isActingAdmin)
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                                You have {{ $state === 'turned ON' ? 'turned on' : 'turned off' }} email OTP verification for staff and admin logins on {{ $siteName }}.
                            </p>
                        @else
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                                {{ $actor->displayName() }} has {{ $state === 'turned ON' ? 'turned on' : 'turned off' }} email OTP verification for staff and admin logins on {{ $siteName }}.
                            </p>
                        @endif

                        @if($enabled)
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                                Staff who have not set up an authenticator app will now be sent a one-time code by email each time they sign in. Staff who already use an authenticator app are unaffected.
                            </p>
                        @else
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
                                Staff who have not set up an authenticator app will now be required to set one up on their next login, as before this feature existed. Staff who already use an authenticator app are unaffected.
                            </p>
                        @endif

                        <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                            If you did not expect this change, contact a super admin immediately.
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
