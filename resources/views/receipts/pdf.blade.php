<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        @php
            $order = $invoice->order;
            $settings = \App\Support\SiteSettings::all();

            $documentType = 'RECEIPT';
            $documentId = '#'.($invoice->id ?? preg_replace('/\D+/', '', (string) $invoice->invoice_number));

            $companyName = (string) ($settings['site_name'] ?? config('app.name', 'Printbuka'));
            $companyAddressLine1 = (string) ($settings['company_address_line_1'] ?? '63, Akeju Street, off Shipeolu St, Somolu, Lagos');
            $companyAddressLine2 = (string) ($settings['company_address_line_2'] ?? '100001, Lagos');
            $companyEmail = (string) ($settings['contact_email'] ?? 'sales@printbuka.com.ng');
            $companyPhone = (string) ($settings['contact_phone'] ?? '08035245784, 09054784526');
            $companyAccountName = trim((string) ($settings['company_account_name'] ?? ''));
            $companyAccountNumber = trim((string) ($settings['company_account_number'] ?? ''));
            $companyAccountBankName = trim((string) ($settings['company_account_bank_name'] ?? ''));
            $companyAccountNote = trim((string) ($settings['company_account_note'] ?? ''));
            $hasCompanyAccountDetails = $companyAccountName !== '' || $companyAccountNumber !== '' || $companyAccountBankName !== '' || $companyAccountNote !== '';

            $issuedAt = $invoice->issued_at ?? now();
            $paidAt = $invoice->paid_at ?? now();

            $paymentMethod = (string) ($invoice->payment_gateway ?? 'paystack');
            $paymentLabel = str($paymentMethod)->replace('_', ' ')->upper()->value();
            $paymentReference = (string) ($invoice->payment_reference ?: 'N/A');

            $billToName = (string) ($order?->customer_name ?: 'Client');
            $billToEmail = (string) ($order?->customer_email ?: 'N/A');
            $billToPhone = (string) ($order?->customer_phone ?: 'N/A');
            $billToAddress = trim(collect([
                $order?->delivery_address,
                $order?->delivery_city,
            ])->filter(fn ($value): bool => filled($value))->implode(', '));

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

            $lightLogoPath = public_path('logo.png');
            $darkLogoPath = public_path('logo-dark.svg');
            $lightLogo = file_exists($lightLogoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($lightLogoPath)) : null;
            $darkLogo = file_exists($darkLogoPath) ? 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($darkLogoPath)) : null;
        @endphp
        <title>{{ $documentType }} {{ $invoice->invoice_number }}</title>
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                padding: 20px;
                font-family: DejaVu Sans, sans-serif;
                background: #e9eff9;
                color: #13203a;
                font-size: 12px;
                line-height: 1.45;
            }

            .invoice-receipt-card {
                width: 100%;
                background: #ffffff;
                border-radius: 20px;
                border: 1px solid #dfe8f4;
                padding: 24px 22px;
            }

            .header-table,
            .info-grid-table,
            .receipt-footer-table {
                width: 100%;
                border-collapse: collapse;
            }

            .header-table td,
            .info-grid-table td,
            .receipt-footer-table td {
                vertical-align: top;
                border: 0;
                padding: 0;
            }

            .brand-cell {
                width: 55%;
            }

            .badge-cell {
                width: 45%;
                text-align: right;
            }

            .brand-logo {
                display: inline-block;
                height: 46px;
                width: auto;
            }

            .logo-dark {
                display: none;
            }

            .brand-fallback {
                font-size: 30px;
                font-weight: 700;
                letter-spacing: -0.02em;
                color: #13203a;
            }

            .brand-tagline {
                margin-top: 6px;
                color: #54657e;
                font-size: 11px;
            }

            .invoice-title {
                font-size: 34px;
                line-height: 1;
                font-weight: 800;
                color: #1e2b5e;
                margin: 0;
            }

            .receipt-badge {
                margin-top: 8px;
                display: inline-block;
                font-weight: 700;
                font-size: 11px;
                padding: 5px 12px;
                border-radius: 999px;
                border: 1px solid #b6dec2;
                background: #e7f3e9;
                color: #0e6b2b;
            }

            .paid-stamp {
                margin-top: 6px;
                display: inline-block;
                font-size: 11px;
                color: #3c6e4a;
                background: #f4fbf6;
                padding: 4px 10px;
                border-radius: 999px;
            }

            .info-grid-table {
                margin-top: 22px;
            }

            .info-grid-table td {
                width: 50%;
                padding-right: 16px;
            }

            .info-grid-table td:last-child {
                padding-right: 0;
                padding-left: 12px;
            }

            .section-title {
                margin: 0 0 10px;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.9px;
                color: #5b6f8c;
                border-bottom: 1px dashed #d0ddeb;
                padding-bottom: 6px;
            }

            .company-name {
                font-size: 16px;
                font-weight: 700;
                color: #13203a;
            }

            .address,
            .contact {
                margin-top: 6px;
                color: #3e5068;
                font-size: 12px;
                line-height: 1.55;
            }

            .details-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .details-table td {
                border: 0;
                padding: 3px 0;
                vertical-align: top;
            }

            .details-label {
                width: 120px;
                color: #4f658d;
                font-size: 11px;
            }

            .details-value {
                color: #13203a;
                font-weight: 700;
                font-size: 11px;
            }

            .status-paid-highlight {
                color: #0e6b2b;
                background: #e4f3e8;
                border-radius: 999px;
                padding: 2px 8px;
                display: inline-block;
            }

            .items-table-wrapper {
                margin-top: 22px;
                border-radius: 14px;
                border: 1px solid #e6ecf5;
                background: #fbfdff;
                overflow: hidden;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
            }

            .items-table th {
                text-align: left;
                padding: 12px 14px;
                font-weight: 700;
                color: #3b4e6b;
                border-bottom: 2px solid #dae2ed;
                font-size: 10px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            .items-table td {
                padding: 10px 14px;
                border-bottom: 1px solid #e9eef4;
                color: #1f2e48;
                font-size: 11px;
                vertical-align: top;
            }

            .items-table tbody tr:last-child td {
                border-bottom: 0;
            }

            .col-description {
                font-weight: 700;
                color: #0f1a2e;
                width: 55%;
            }

            .col-qty,
            .col-price,
            .col-total {
                text-align: right;
                white-space: nowrap;
            }

            .col-total {
                font-weight: 700;
                color: #13203a;
            }

            .totals-row {
                margin-top: 12px;
                text-align: right;
            }

            .totals-box {
                display: inline-block;
                width: 290px;
                max-width: 100%;
                text-align: left;
                background: #f4f8fe;
                border: 1px solid #dee9f2;
                border-radius: 14px;
                padding: 12px 14px;
            }

            .total-line {
                width: 100%;
                border-collapse: collapse;
            }

            .total-line td {
                border: 0;
                padding: 4px 0;
                font-size: 12px;
            }

            .total-line td:first-child {
                color: #3e5775;
            }

            .total-line td:last-child {
                text-align: right;
                color: #13203a;
                font-weight: 700;
            }

            .total-line.grand td {
                border-top: 1px dashed #b7c8dd;
                padding-top: 8px;
                font-size: 16px;
                font-weight: 800;
                color: #0f1f3a;
            }

            .paid-pill {
                margin-top: 10px;
                display: inline-block;
                background: #eaf1dd;
                color: #1e5620;
                border-radius: 999px;
                padding: 5px 10px;
                font-size: 10px;
                font-weight: 700;
            }

            .receipt-footer-table {
                margin-top: 18px;
                border-top: 1px solid #eef3fa;
                padding-top: 12px;
            }

            .payment-block {
                display: inline-block;
                background: #fafcff;
                border: 1px solid #e3eaf3;
                border-radius: 999px;
                padding: 9px 14px;
                color: #13294b;
                font-size: 11px;
                line-height: 1.4;
            }

            .payment-label {
                color: #687e9e;
                font-size: 9px;
                letter-spacing: 0.4px;
                text-transform: uppercase;
            }

            .payment-value {
                font-size: 12px;
                font-weight: 700;
                color: #13294b;
            }

            .account-block {
                margin-top: 10px;
                background: #f4f8fe;
                border: 1px solid #dee9f2;
                border-radius: 10px;
                padding: 8px 10px;
                color: #13294b;
                font-size: 10px;
                line-height: 1.45;
            }

            .account-title {
                font-size: 9px;
                letter-spacing: 0.4px;
                text-transform: uppercase;
                color: #687e9e;
                margin-bottom: 4px;
            }

            .thankyou-message {
                text-align: right;
            }

            .thankyou-message p {
                margin: 0 0 4px;
                color: #1d3857;
                font-size: 13px;
                font-weight: 600;
            }

            .receipt-id {
                display: inline-block;
                font-size: 10px;
                color: #52688a;
                background: #eef3fc;
                border-radius: 999px;
                padding: 4px 10px;
                font-weight: 600;
            }

            .fine-print {
                margin-top: 14px;
                font-size: 9px;
                color: #7a8aa3;
                text-align: center;
            }

            @media (prefers-color-scheme: dark) {
                .logo-light {
                    display: none !important;
                }

                .logo-dark {
                    display: inline-block !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-receipt-card">
            <table class="header-table">
                <tr>
                    <td class="brand-cell">
                        @if ($lightLogo || $darkLogo)
                            @if ($lightLogo)
                                <img src="{{ $lightLogo }}" alt="Printbuka light logo" class="brand-logo logo-light">
                            @endif
                            @if ($darkLogo)
                                <img src="{{ $darkLogo }}" alt="Printbuka dark logo" class="brand-logo logo-dark">
                            @endif
                        @else
                            <div class="brand-fallback">{{ $companyName }}</div>
                        @endif
                        <div class="brand-tagline">creative print studio</div>
                    </td>
                    <td class="badge-cell">
                        <p class="invoice-title">{{ $documentType }}</p>
                        <span class="receipt-badge">RECEIPT · PAID</span>
                        <div class="paid-stamp">Settled on {{ $paidAt->format('M d, Y') }}</div>
                    </td>
                </tr>
            </table>

            <table class="info-grid-table">
                <tr>
                    <td>
                        <h3 class="section-title">Bill From</h3>
                        <div class="company-name">{{ $companyName }}</div>
                        <div class="address">
                            {{ $companyAddressLine1 }}<br>
                            {{ $companyAddressLine2 }}
                        </div>
                        <div class="contact">
                            {{ $companyEmail }}<br>
                            {{ $companyPhone }}
                        </div>
                    </td>
                    <td>
                        <h3 class="section-title">Bill To & Details</h3>
                        <div class="company-name">{{ $billToName }}</div>
                        <div class="address">
                            {{ $billToEmail }}<br>
                            {{ $billToPhone }}
                            @if ($billToAddress !== '')
                                <br>{{ $billToAddress }}
                            @endif
                        </div>
                        <table class="details-table">
                            <tr>
                                <td class="details-label">Invoice No.</td>
                                <td class="details-value">{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Date issued</td>
                                <td class="details-value">{{ $issuedAt->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Paid date</td>
                                <td class="details-value">{{ $paidAt->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="details-label">Status</td>
                                <td class="details-value">
                                    <span class="status-paid-highlight">PAID · RECEIPT</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="col-qty">Qty</th>
                            <th class="col-price">Unit price</th>
                            <th class="col-total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lineItems as $lineItem)
                            <tr>
                                <td class="col-description">{{ $lineItem['description'] }}</td>
                                <td class="col-qty">{{ number_format((float) $lineItem['quantity'], 0) }}</td>
                                <td class="col-price">NGN {{ number_format((float) $lineItem['rate'], 2) }}</td>
                                <td class="col-total">NGN {{ number_format((float) $lineItem['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals-row">
                <div class="totals-box">
                    <table class="total-line">
                        <tr>
                            <td>Subtotal</td>
                            <td>NGN {{ number_format((float) $invoice->subtotal, 2) }}</td>
                        </tr>
                    </table>
                    <table class="total-line">
                        <tr>
                            <td>Tax</td>
                            <td>NGN {{ number_format((float) $invoice->tax_amount, 2) }}</td>
                        </tr>
                    </table>
                    @if ((float) $invoice->discount_amount > 0)
                        <table class="total-line">
                            <tr>
                                <td>Discount</td>
                                <td>- NGN {{ number_format((float) $invoice->discount_amount, 2) }}</td>
                            </tr>
                        </table>
                    @endif
                    <table class="total-line grand">
                        <tr>
                            <td>Total Paid (NGN)</td>
                            <td>NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                        </tr>
                    </table>
                    <span class="paid-pill">Paid in full · Receipt issued</span>
                </div>
            </div>

            <table class="receipt-footer-table">
                <tr>
                    <td style="width:58%;">
                        <div class="payment-block">
                            <div class="payment-label">Payment method</div>
                            <div class="payment-value">{{ $paymentLabel }}</div>
                            <div>Transaction ID: {{ $paymentReference }}</div>
                        </div>
                        @if ($hasCompanyAccountDetails)
                            <div class="account-block">
                                <div class="account-title">Company account details</div>
                                @if ($companyAccountBankName !== '')
                                    <div><strong>Bank:</strong> {{ $companyAccountBankName }}</div>
                                @endif
                                @if ($companyAccountName !== '')
                                    <div><strong>Account name:</strong> {{ $companyAccountName }}</div>
                                @endif
                                @if ($companyAccountNumber !== '')
                                    <div><strong>Account number:</strong> {{ $companyAccountNumber }}</div>
                                @endif
                                @if ($companyAccountNote !== '')
                                    <div><strong>Note:</strong> {{ $companyAccountNote }}</div>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td style="width:42%;" class="thankyou-message">
                        <p>Thank you for your business!</p>
                        <div class="receipt-id">Receipt {{ $documentId }} · {{ $paidAt->format('M d, Y') }}</div>
                    </td>
                </tr>
            </table>

            <div class="fine-print">
                This is a combined invoice reference and official receipt generated by {{ $companyName }}.
            </div>
        </div>
    </body>
</html>
