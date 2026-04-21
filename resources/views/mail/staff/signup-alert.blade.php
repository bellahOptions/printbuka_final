<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>New Staff Signup</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;color:#0f172a;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:24px;border-bottom:1px solid #e2e8f0;background:#111827;color:#ffffff;">
                <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#f9a8d4;">Printbuka Staff Governance</p>
                <h1 style="margin:8px 0 0;font-size:22px;line-height:1.3;">New staff signup awaiting approval</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $recipient->displayName() }},</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">A new staff registration was submitted and requires Super Admin review.</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Name</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->displayName() }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Email</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Phone</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->phone }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Submitted</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->created_at?->format('F j, Y g:i A') }}</td>
                    </tr>
                </table>

                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Please sign in to the admin dashboard and review the pending staff account.</p>
            </td>
        </tr>
    </table>
</body>
</html>
