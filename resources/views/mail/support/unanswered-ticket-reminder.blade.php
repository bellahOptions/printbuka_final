<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>Unanswered Ticket Reminder</title>
    </head>
    @php($logoUrl = asset('logo-dark.svg'))
    <body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:28px 14px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="680" cellpadding="0" cellspacing="0" style="max-width:680px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;">
                        <tr>
                            <td style="background:#0f172a;padding:22px 24px;color:#ffffff;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:top;"><img src="{{ $logoUrl }}" alt="Printbuka" width="150" style="display:inline-block;height:auto;"></td>
                                        <td style="text-align:right;vertical-align:top;color:#cbd5e1;font-size:11px;font-weight:700;letter-spacing:0.06em;">SUPPORT REMINDER</td>
                                    </tr>
                                </table>
                                <h1 style="margin:14px 0 0;font-size:25px;line-height:1.2;">Unanswered Support Tickets</h1>
                                <p style="margin:10px 0 0;color:#cbd5e1;line-height:1.6;font-size:14px;">{{ $tickets->count() }} ticket(s) have been awaiting response for at least {{ $thresholdHours }} hour(s).</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">Hello {{ $recipient->displayName() }},</p>
                                <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">Please review the unanswered support queue below and respond as soon as possible.</p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                    <tr>
                                        <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Ticket</th>
                                        <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Client</th>
                                        <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Priority</th>
                                        <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Waiting</th>
                                    </tr>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:700;">
                                                {{ $ticket->ticket_number }}<br>
                                                <span style="font-weight:600;color:#475569;">{{ Str::limit((string) $ticket->subject, 45) }}</span>
                                            </td>
                                            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->user?->displayName() ?? 'Unknown user' }}</td>
                                            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ strtoupper((string) $ticket->priority) }}</td>
                                            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ max(1, now()->diffInHours($ticket->updated_at)) }}h</td>
                                        </tr>
                                    @endforeach
                                </table>

                                <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#475569;">Open the admin support portal to respond and keep SLA compliance on track.</p>
                                <a href="{{ route('admin.support.index') }}" style="display:inline-block;margin-top:14px;background:#db2777;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 18px;border-radius:8px;">Open Support Queue</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
