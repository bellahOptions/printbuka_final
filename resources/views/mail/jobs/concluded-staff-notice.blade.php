<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Concluded</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;color:#111827;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:24px;border-bottom:1px solid #e2e8f0;background:#0f172a;color:#ffffff;">
                <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#f472b6;">Printbuka Job Update</p>
                <h1 style="margin:8px 0 0;font-size:22px;line-height:1.3;">Job Concluded</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                {!! $introHtml ?? '' !!}
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $recipient->displayName() }},</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">
                    Job <strong>{{ $order->job_order_number }}</strong> for <strong>{{ $order->customer_name }}</strong> has been concluded and marked delivered.
                </p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Job Type</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $order->job_type ?? $order->product?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Concluded At</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $order->concluded_at?->format('F j, Y g:i A') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Concluded By</td>
                        <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $order->concludedBy?->displayName() ?? 'N/A' }}</td>
                    </tr>
                </table>

                <p style="margin:0;font-size:13px;line-height:1.7;color:#64748b;">This is a routine team notification — no action is required.</p>
                {!! $outroHtml ?? '' !!}
            </td>
        </tr>
    </table>
</body>
</html>
