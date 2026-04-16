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
        @php
            $logoPath = public_path('logo-dark.png');
            $logo = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
        @endphp
        <div class="header">
            <table style="border:0;">
                <tr>
                    <td style="border:0;padding:0;">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="Printbuka" style="height:48px;width:auto;background:#ffffff;border-radius:4px;padding:4px;">
                        @endif
                    </td>
                    <td style="border:0;padding:0;text-align:right;">
                        <h1 style="margin:0;">Printbuka Invoice</h1>
                        <p style="margin:8px 0 0;">{{ $invoice->invoice_number }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Order</th>
                    <td>{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</td>
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
                    <td>{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
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
                    @if ($invoice->order->size_format || $invoice->order->material_substrate || $invoice->order->finish_lamination || $invoice->order->delivery_method)
                        <tr>
                            <td colspan="4">
                                <strong>Selected options:</strong>
                                {{ collect([$invoice->order->size_format, $invoice->order->material_substrate, $invoice->order->finish_lamination, $invoice->order->delivery_method])->filter()->implode(' · ') }}
                            </td>
                        </tr>
                    @endif
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
                <tr>
                    <th>Amount Paid</th>
                    <td>NGN {{ number_format((float) $invoice->order->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>
