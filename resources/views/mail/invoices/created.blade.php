<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $invoice->invoice_number }}</title>
    </head>
    <body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:8px;overflow:hidden;">
                        <tr>
                            <td style="background:#0f172a;color:#ffffff;padding:28px;">
                                <h1 style="margin:0;font-size:28px;">Your Printbuka invoice is ready</h1>
                                <p style="margin:12px 0 0;color:#cbd5e1;">Invoice {{ $invoice->invoice_number }} for order {{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }} is attached as a PDF.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:28px;">
                                <p style="margin:0 0 16px;">Hello {{ $invoice->order->customer_name }},</p>
                                <p style="margin:0 0 20px;line-height:1.6;">Thank you for placing your {{ $invoice->order->service_type }} order with Printbuka. Our team will review your artwork and delivery details, then follow up with the next production steps.</p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:24px 0;">
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Product</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->product?->name ?? 'Custom order' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Quantity</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Total</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">NGN {{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Payment status</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                                    </tr>
                                </table>

                                <p style="margin:0;line-height:1.6;">You can track your order with order number <strong>{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</strong> and this email address.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
