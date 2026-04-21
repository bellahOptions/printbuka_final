<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>New Support Ticket</title>
    </head>
    @php
        $logoUrl = asset('logo-dark.svg');
        $ticketUrl = route('admin.support.show', $ticket);
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
                                        <td style="text-align:right;vertical-align:top;color:#cbd5e1;font-size:11px;font-weight:700;letter-spacing:0.06em;">SUPPORT ALERT</td>
                                    </tr>
                                </table>
                                <h1 style="margin:14px 0 0;font-size:26px;line-height:1.2;">New Support Ticket Raised</h1>
                                <p style="margin:10px 0 0;color:#cbd5e1;line-height:1.6;font-size:14px;">Ticket {{ $ticket->ticket_number }} needs attention from the admin team.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">Hello {{ $recipient->displayName() }},</p>
                                <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">A new support ticket has been submitted. Please review and assign action quickly.</p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 18px;">
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Ticket Number</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:700;">{{ $ticket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Raised By</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->user?->displayName() ?? 'Unknown user' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Category</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ ucfirst((string) $ticket->category) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Priority</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ strtoupper((string) $ticket->priority) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Subject</td>
                                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->subject }}</td>
                                    </tr>
                                </table>

                                <div style="margin:0 0 18px;padding:14px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
                                    <p style="margin:0;font-size:13px;line-height:1.6;color:#7f1d1d;">{{ Str::limit((string) $ticket->message, 350) }}</p>
                                </div>

                                <a href="{{ $ticketUrl }}" style="display:inline-block;background:#db2777;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 18px;border-radius:8px;">Open Ticket</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
