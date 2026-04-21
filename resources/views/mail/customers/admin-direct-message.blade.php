<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Message from Printbuka Admin' }}</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:20px 24px;background:#0f172a;color:#ffffff;">
                            <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;opacity:0.8;">Direct Admin Message</p>
                            <h1 style="margin:8px 0 0 0;font-size:24px;line-height:1.25;">Hello {{ $recipientName }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px 0;font-size:15px;line-height:1.7;color:#334155;">You received a direct message from Printbuka administration.</p>
                            <div style="margin:0 0 18px 0;padding:16px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">
                                {!! nl2br(e($messageBody)) !!}
                            </div>
                            <p style="margin:0;font-size:14px;line-height:1.7;color:#475569;">
                                Sender: <strong>{{ $senderName }}</strong>
                            </p>
                            <p style="margin:6px 0 0 0;font-size:13px;line-height:1.7;color:#64748b;">
                                Reply directly to this email to reach the sender.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
