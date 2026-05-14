<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Unpaid {{ $invoice->documentTypeLabel() }} Reminder</title>
    </head>
    <body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:10px;overflow:hidden;">
                        <tr>
                            <td style="background:#0f172a;color:#ffffff;padding:24px;">
                                <h1 style="margin:0;font-size:24px;line-height:1.25;">Payment reminder</h1>
                                <p style="margin:8px 0 0;color:#cbd5e1;">{{ $invoice->documentTypeLabel() }} {{ $invoice->invoice_number }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:26px;line-height:1.6;">
                                <p style="margin:0 0 16px;">Hello {{ $invoice->order?->customer_name ?? 'there' }},</p>
                                <p style="margin:0 0 16px;">This is a friendly reminder that your Printbuka {{ strtolower($invoice->documentTypeLabel()) }} of NGN {{ number_format((float) $invoice->total_amount, 2) }} is still unpaid.</p>
                                <p style="margin:0 0 16px;">Due date: <strong>{{ $invoice->due_at?->format('M d, Y h:i A') ?? 'To be confirmed' }}</strong></p>
                                <p style="margin:0;">Please use {{ $invoice->invoice_number }} as your payment reference.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
