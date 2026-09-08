<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Printbuka Job Finance Summary</title>
    </head>
    <body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#111827;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
            @include('mail.partials.header', [
                'headerTitle' => 'Printbuka Job Finance Summary',
            ])
            <tr>
                <td style="padding:24px;">
                    {!! $introHtml ?? '' !!}
                    <p style="margin-bottom: 16px;">Hello {{ $order->customer_name }},</p>
                    <p style="margin-bottom: 16px;">Your job <strong>{{ $order->job_order_number }}</strong> has been marked as delivered. Attached is the finance summary for this job, including the expense entries recorded against it.</p>
                    <p style="margin-bottom: 16px;">If you have any questions about this job or the attached finance record, feel free to reply to this email.</p>
                    <p style="margin-bottom: 16px;">Thank you for choosing Printbuka.</p>
                    {!! $outroHtml ?? '' !!}
                    <p style="margin-bottom: 0;">Regards,<br>Printbuka Team</p>
                </td>
            </tr>
        </table>
    </body>
</html>
