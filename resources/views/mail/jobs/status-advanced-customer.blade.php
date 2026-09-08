<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        @include('mail.partials.header', [
            'headerTitle' => 'Order Status Update',
        ])
        <tr>
            <td style="padding:24px;line-height:1.6;">
                {!! $introHtml ?? '' !!}
                <p>Hello {{ $order->customer_name }},</p>
                <p>Your order has advanced to a new stage.</p>
                <p><strong>Order:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
                <p><strong>Previous Status:</strong> {{ $oldStatus }}</p>
                <p><strong>Current Status:</strong> {{ $newStatus }}</p>
                <p>We will keep notifying you as your job progresses.</p>
                {!! $outroHtml ?? '' !!}
            </td>
        </tr>
    </table>
</body>
</html>
