<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Printbuka Job Finance Summary</title>
    </head>
    <body style="font-family: Arial, sans-serif; color: #111827; margin: 0; padding: 24px;">
        <div style="max-width: 680px; margin: 0 auto;">
            <h1 style="font-size: 24px; margin-bottom: 16px;">Printbuka Job Finance Summary</h1>
            <p style="margin-bottom: 16px;">Hello {{ $order->customer_name }},</p>
            <p style="margin-bottom: 16px;">Your job <strong>{{ $order->job_order_number }}</strong> has been marked as delivered. Attached is the finance summary for this job, including the expense entries recorded against it.</p>
            <p style="margin-bottom: 16px;">If you have any questions about this job or the attached finance record, feel free to reply to this email.</p>
            <p style="margin-bottom: 16px;">Thank you for choosing Printbuka.</p>
            <p style="margin-bottom: 0;">Regards,<br>Printbuka Team</p>
        </div>
    </body>
</html>
