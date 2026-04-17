<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Payment confirmed</title>
    </head>
    @php
        $documentType = $invoice->documentTypeLabel();
    @endphp
    <body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:8px;overflow:hidden;">
                        <tr>
                            <td style="background:#0f172a;color:#ffffff;padding:28px;">
                                <h1 style="margin:0;font-size:28px;">Payment confirmed</h1>
                                <p style="margin:12px 0 0;color:#cbd5e1;">{{ $documentType }} {{ $invoice->invoice_number }} has been marked as paid.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:28px;">
                                <p style="margin:0 0 16px;">Hello {{ $invoice->order->customer_name }},</p>
                                <p style="margin:0 0 16px;line-height:1.6;">We have successfully confirmed your payment. Your receipt is attached as a PDF in this email.</p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:24px 0;">
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Order</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Total Paid</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Paid At</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->paid_at?->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Estimated delivery</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->estimated_delivery_at?->format('M d, Y h:i A') ?? 'To be confirmed' }}</td>
                                    </tr>
                                </table>
                                <p style="margin:0;line-height:1.6;">Thank you for choosing Printbuka.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
