<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $invoice->documentTypeLabel() }} {{ $invoice->invoice_number }}</title>
        <style>
            body { margin: 0; padding: 28px; font-family: Arial, sans-serif; color: #111827; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
            .title { font-size: 28px; font-weight: 800; margin: 0; }
            .subtitle { font-size: 12px; color: #475569; margin: 4px 0 0; }
            .section { border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; margin-bottom: 20px; }
            .section h2 { font-size: 13px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: .08em; color: #475569; }
            .row { display: flex; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
            .detail { width: calc(50% - 10px); min-width: 190px; }
            .label { display: block; margin-bottom: 4px; font-size: 11px; font-weight: 700; color: #64748b; }
            .value { font-size: 14px; color: #111827; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { padding: 12px 10px; border: 1px solid #e2e8f0; text-align: left; }
            th { background: #f8fafc; text-transform: uppercase; letter-spacing: .05em; font-size: 11px; color: #475569; }
            .total-row td { font-weight: 800; }
            .amount { text-align: right; }
        </style>
    </head>
    <body>
        <div class="header">
            <div>
                <p class="title">{{ $invoice->documentTypeLabel() }}</p>
                <p class="subtitle">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="detail">
                <span class="label">Date</span>
                <div class="value">{{ $invoice->issued_at?->format('F j, Y') ?? now()->format('F j, Y') }}</div>
            </div>
        </div>

        <div class="section">
            <h2>Customer</h2>
            <div class="row">
                <div class="detail">
                    <span class="label">Name</span>
                    <div class="value">{{ $invoice->order?->customer_name ?? 'N/A' }}</div>
                </div>
                <div class="detail">
                    <span class="label">Email</span>
                    <div class="value">{{ $invoice->order?->customer_email ?? 'N/A' }}</div>
                </div>
                <div class="detail">
                    <span class="label">Phone</span>
                    <div class="value">{{ $invoice->order?->customer_phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Job details</h2>
            <div class="row">
                <div class="detail">
                    <span class="label">Job number</span>
                    <div class="value">{{ $invoice->order?->job_order_number ?? 'N/A' }}</div>
                </div>
                <div class="detail">
                    <span class="label">Order status</span>
                    <div class="value">{{ $invoice->order?->status ?? 'N/A' }}</div>
                </div>
                <div class="detail">
                    <span class="label">Payment status</span>
                    <div class="value">{{ $invoice->order?->payment_status ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Amounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Subtotal</td>
                        <td class="amount">₦{{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="amount">₦{{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td class="amount">₦{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total</td>
                        <td class="amount">₦{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Notes</h2>
            <div class="value">{{ $invoice->order?->internal_notes ?: 'No additional notes.' }}</div>
        </div>
    </body>
</html>
