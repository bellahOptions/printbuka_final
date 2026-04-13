<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $invoice->invoice_number }}</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
            .header { background: #0f172a; color: #ffffff; padding: 24px; }
            .section { padding: 22px 0; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
            th { background: #f8fafc; }
            .total { font-size: 18px; font-weight: bold; color: #be185d; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1 style="margin:0;">Printbuka Invoice</h1>
            <p style="margin:8px 0 0;">{{ $invoice->invoice_number }}</p>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Order</th>
                    <td>{{ $invoice->order->displayNumber() }}</td>
                    <th>Issued</th>
                    <td>{{ $invoice->issued_at?->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td>{{ $invoice->order->customer_name }}</td>
                    <th>Due</th>
                    <td>{{ $invoice->due_at?->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $invoice->order->customer_email }}</td>
                    <th>Status</th>
                    <td>{{ ucfirst($invoice->status) }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit/MOQ price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $invoice->order->product?->name ?? 'Custom order' }}</td>
                        <td>{{ $invoice->order->quantity }}</td>
                        <td>NGN {{ number_format($invoice->order->unit_price, 2) }}</td>
                        <td>NGN {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Subtotal</th>
                    <td>NGN {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td>NGN {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td>NGN {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td class="total">NGN {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>
