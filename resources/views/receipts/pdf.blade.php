<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Receipt {{ $invoice->invoice_number }}</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
            .heading { margin-bottom: 20px; }
            .heading h1 { margin: 0; font-size: 22px; }
            .heading p { margin: 4px 0 0; color: #475569; }
            table { width: 100%; border-collapse: collapse; margin-top: 16px; }
            td, th { border: 1px solid #cbd5e1; padding: 10px; }
            th { background: #f1f5f9; text-align: left; }
            .totals td:first-child { font-weight: bold; width: 60%; }
            .amount { font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="heading">
            <h1>Payment Receipt</h1>
            <p>Printbuka</p>
            <p>Receipt Ref: {{ $invoice->invoice_number }}</p>
        </div>

        <table>
            <tbody>
                <tr>
                    <th>Customer</th>
                    <td>{{ $invoice->order->customer_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $invoice->order->customer_email }}</td>
                </tr>
                <tr>
                    <th>Order</th>
                    <td>{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td>{{ $invoice->order->product?->name ?? ($invoice->order->job_type ?? 'Custom order') }}</td>
                </tr>
                <tr>
                    <th>Payment Date</th>
                    <td>{{ $invoice->paid_at?->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}</td>
                </tr>
                @if($invoice->payment_reference)
                    <tr>
                        <th>Payment Reference</th>
                        <td>{{ $invoice->payment_reference }}</td>
                    </tr>
                @endif
                @if($invoice->payment_gateway)
                    <tr>
                        <th>Gateway</th>
                        <td>{{ strtoupper($invoice->payment_gateway) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <table class="totals">
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td>NGN {{ number_format((float) $invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td>NGN {{ number_format((float) $invoice->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td>NGN {{ number_format((float) $invoice->discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Paid</td>
                    <td class="amount">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
