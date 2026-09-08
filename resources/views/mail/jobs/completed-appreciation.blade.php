<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You from Printbuka</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;color:#111827;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        @include('mail.partials.header', [
            'headerBadge' => 'PRINTBUKA',
            'headerTitle' => 'Your Job Has Been Completed',
        ])
        <tr>
            <td style="padding:24px;">
                {!! $introHtml ?? '' !!}
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $order->customer_name }},</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Your job <strong>{{ $order->job_order_number }}</strong> has been concluded by our operations team.</p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Thank you for trusting Printbuka. We appreciate your business and look forward to serving you again.</p>
                {!! $outroHtml ?? '' !!}
                <p style="margin:0;font-size:14px;line-height:1.7;">Regards,<br>Printbuka Team</p>
            </td>
        </tr>
    </table>
</body>
</html>

