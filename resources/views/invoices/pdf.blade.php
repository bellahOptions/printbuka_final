<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        @php
            $order = $invoice->order;
            $documentType = strtoupper($invoice->documentTypeLabel());
            $settings = \App\Support\SiteSettings::all();
            $companyAddressLine1 = (string) ($settings['company_address_line_1'] ?? '63, Akeju Street, off Shipeolu St, Somolu, Lagos');
            $companyAddressLine2 = (string) ($settings['company_address_line_2'] ?? '100001, Lagos');
            $documentId = '#'.($invoice->id ?? preg_replace('/\D+/', '', (string) $invoice->invoice_number));
            $amountPaid = (float) ($order?->amount_paid ?? 0);
            $balanceDue = max(0, (float) $invoice->total_amount - $amountPaid);
            $breakdown = is_array($order?->pricing_breakdown) ? $order->pricing_breakdown : [];
            $lineItems = collect($breakdown['line_items'] ?? [])
                ->filter(fn ($item): bool => is_array($item))
                ->map(function (array $item): array {
                    $description = trim((string) ($item['description'] ?? ''));
                    $quantity = max(1, (int) ($item['quantity'] ?? 0));
                    $rate = max(0, (float) ($item['rate'] ?? 0));
                    $amount = isset($item['amount']) ? (float) $item['amount'] : ($quantity * $rate);

                    return [
                        'description' => $description,
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'amount' => $amount,
                    ];
                })
                ->filter(fn (array $item): bool => $item['description'] !== '')
                ->values();

            if ($lineItems->isEmpty()) {
                $lineItems = collect([[
                    'description' => $order?->product?->name ?? ($order?->job_type ?? 'Custom order'),
                    'quantity' => max(1, (int) ($order?->quantity ?? 1)),
                    'rate' => max(0, (float) ($order?->unit_price ?? 0)),
                    'amount' => max(0, (float) $invoice->subtotal),
                ]]);
            }
        @endphp
        <title>{{ $documentType }} {{ $invoice->invoice_number }}</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                color: #2f2f2f;
                font-size: 12px;
                margin: 28px;
            }

            .top {
                width: 100%;
                border-collapse: collapse;
            }

            .top td {
                vertical-align: top;
                border: 0;
                padding: 0;
            }

            .doc-title {
                font-size: 46px;
                font-weight: 500;
                letter-spacing: 1px;
                margin: 0;
                text-align: right;
                color: #3a3a3a;
            }

            .doc-number {
                font-size: 22px;
                margin-top: 4px;
                text-align: right;
                color: #565656;
            }

            .address {
                margin-top: 16px;
                font-size: 22px;
                font-weight: 600;
                line-height: 1.35;
            }

            .meta {
                width: 100%;
                margin-top: 26px;
                border-collapse: collapse;
            }

            .meta td {
                border: 0;
                padding: 3px 0;
                vertical-align: top;
            }

            .label {
                color: #777777;
            }

            .value {
                font-weight: 700;
                color: #3a3a3a;
            }

            .summary-strip {
                width: 50%;
                margin-left: auto;
                margin-top: 16px;
                background: #f1f1f1;
                border-radius: 4px;
                border-collapse: collapse;
            }

            .summary-strip td {
                border: 0;
                padding: 10px 14px;
                font-size: 15px;
                font-weight: 700;
            }

            .summary-strip td:first-child {
                text-align: right;
                color: #444444;
            }

            .summary-strip td:last-child {
                text-align: right;
                color: #2b2b2b;
            }

            .items {
                width: 100%;
                margin-top: 22px;
                border-collapse: separate;
                border-spacing: 0;
            }

            .items thead th {
                background: #333333;
                color: #ffffff;
                padding: 9px 12px;
                text-align: left;
                font-size: 12px;
                font-weight: 500;
                border: 0;
            }

            .items thead th:first-child {
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px;
            }

            .items thead th:last-child {
                border-top-right-radius: 4px;
                border-bottom-right-radius: 4px;
                text-align: right;
            }

            .items tbody td {
                border: 0;
                padding: 12px;
                font-size: 22px;
                color: #4a4a4a;
                vertical-align: top;
            }

            .items tbody td:nth-child(2),
            .items tbody td:nth-child(3),
            .items tbody td:nth-child(4) {
                white-space: nowrap;
            }

            .items tbody td:nth-child(2),
            .items tbody td:nth-child(3) {
                text-align: left;
            }

            .items tbody td:last-child {
                text-align: right;
            }

            .items tbody td:first-child {
                font-weight: 700;
            }

            .totals {
                width: 45%;
                margin-top: 28px;
                margin-left: auto;
                border-collapse: collapse;
            }

            .totals td {
                border: 0;
                padding: 7px 0;
                font-size: 26px;
                color: #777777;
            }

            .totals td:first-child {
                text-align: left;
                padding-right: 26px;
            }

            .totals td:last-child {
                text-align: right;
                color: #4a4a4a;
            }

            .totals .grand td {
                font-weight: 700;
                color: #3a3a3a;
            }
        </style>
    </head>
    <body>
        @php
            $logoPath = public_path('logo.png');
            $logo = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
        @endphp

        <table class="top">
            <tr>
                <td style="width:60%;">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="Printbuka" style="height:54px;width:auto;">
                    @else
                        <div style="font-size:34px;font-weight:700;">{{ $settings['site_name'] ?? config('app.name', 'Printbuka') }}</div>
                    @endif
                    <div class="address">
                        {{ $companyAddressLine1 }}<br>
                        {{ $companyAddressLine2 }}
                    </div>
                </td>
                <td style="width:40%;">
                    <p class="doc-title">{{ $documentType }}</p>
                    <p class="doc-number">{{ $documentId }}</p>
                </td>
            </tr>
        </table>

        <table class="meta">
            <tr>
                <td style="width:50%;">
                    <span class="label">Bill To:</span><br>
                    <span class="value">{{ $order?->customer_name ?? 'Client' }}</span>
                </td>
                <td style="width:25%; text-align:right;">
                    <span class="label">Date:</span>
                </td>
                <td style="width:25%; text-align:right;">
                    <span>{{ $invoice->issued_at?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align:right;">
                    <span class="label">Due Date:</span>
                </td>
                <td style="text-align:right;">
                    <span>{{ $invoice->due_at?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
                </td>
            </tr>
        </table>

        <table class="summary-strip">
            <tr>
                <td>Balance Due:</td>
                <td>NGN {{ number_format($balanceDue, 2) }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:57%;">Item</th>
                    <th style="width:16%;">Quantity</th>
                    <th style="width:14%;">Rate</th>
                    <th style="width:13%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lineItems as $lineItem)
                    <tr>
                        <td>{{ $lineItem['description'] }}</td>
                        <td>{{ number_format((float) $lineItem['quantity'], 0) }}</td>
                        <td>NGN {{ number_format((float) $lineItem['rate'], 2) }}</td>
                        <td>NGN {{ number_format((float) $lineItem['amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal:</td>
                <td>NGN {{ number_format((float) $invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax (0%):</td>
                <td>NGN {{ number_format((float) $invoice->tax_amount, 2) }}</td>
            </tr>
            @if ((float) $invoice->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td>- NGN {{ number_format((float) $invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand">
                <td>Total:</td>
                <td>NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </body>
</html>
