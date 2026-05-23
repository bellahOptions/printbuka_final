<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You from Printbuka</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;color:#111827;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:24px;border-bottom:1px solid #e2e8f0;background:#111827;color:#ffffff;">
                <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#67e8f9;">Printbuka</p>
                <h1 style="margin:8px 0 0;font-size:22px;line-height:1.3;">Your Job Has Been Completed</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $order->customer_name }},</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Your job <strong>{{ $order->job_order_number }}</strong> has been concluded by our operations team.</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Thank you for trusting Printbuka. We appreciate your business and look forward to serving you again.</p>
                <p style="margin:0;font-size:14px;line-height:1.7;">Regards,<br>Printbuka Team</p>
            </td>
        </tr>
    </table>
</body>
</html>

