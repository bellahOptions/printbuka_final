<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>@yield('title', trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka')))</title>
        <style>
            @media screen and (max-width: 640px) {
                .email-wrapper { padding: 20px 8px !important; }
                .email-card { width: 100% !important; max-width: 100% !important; border-radius: 10px !important; }
                .email-content, .email-footer { padding-left: 18px !important; padding-right: 18px !important; }
            }
        </style>
    </head>
    <body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        @hasSection('preheader')
            <span style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">@yield('preheader')</span>
        @endif
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;">
            <tr>
                <td align="center" class="email-wrapper" style="padding:28px 14px;">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" class="email-card" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
                        @include('mail.partials.header')
                        <tr>
                            <td class="email-content" style="padding:26px;">
                                @yield('content')
                            </td>
                        </tr>
                        @include('mail.partials.footer')
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
