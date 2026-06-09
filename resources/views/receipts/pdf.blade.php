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

            $embedFont = static function (array $paths): ?string {
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        return 'data:font/ttf;base64,'.base64_encode(file_get_contents($path));
                    }
                }
                return null;
            };

            $openSansRegular = $embedFont([
                public_path('fonts/OpenSans-Regular.ttf'),
                public_path('fonts/open-sans/OpenSans-Regular.ttf'),
                public_path('fonts/open-sans/static/OpenSans-Regular.ttf'),
            ]);
            $openSansSemiBold = $embedFont([
                public_path('fonts/OpenSans-SemiBold.ttf'),
                public_path('fonts/open-sans/OpenSans-SemiBold.ttf'),
                public_path('fonts/open-sans/static/OpenSans-SemiBold.ttf'),
            ]);
            $openSansBold = $embedFont([
                public_path('fonts/OpenSans-Bold.ttf'),
                public_path('fonts/open-sans/OpenSans-Bold.ttf'),
                public_path('fonts/open-sans/static/OpenSans-Bold.ttf'),
            ]);

            $issuedAt = $invoice->issued_at ?? now();
            $paidAt = $invoice->paid_at ?? now();

            $paymentMethod = (string) ($invoice->payment_gateway ?? 'bank_transfer');
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
            @if ($openSansRegular !== null)
                @font-face {
                    font-family: 'Open Sans';
                    font-style: normal;
                    font-weight: 400;
                    src: url('{{ $openSansRegular }}') format('truetype');
                }
            @endif
            @if ($openSansSemiBold !== null)
                @font-face {
                    font-family: 'Open Sans';
                    font-style: normal;
                    font-weight: 600;
                    src: url('{{ $openSansSemiBold }}') format('truetype');
                }
            @endif
            @if ($openSansBold !== null)
                @font-face {
                    font-family: 'Open Sans';
                    font-style: normal;
                    font-weight: 700;
                    src: url('{{ $openSansBold }}') format('truetype');
                }
            @endif

            * { box-sizing: border-box; }

            body {
                margin: 0;
                padding: 20px;
                font-family: 'Open Sans', 'DejaVu Sans', Arial, sans-serif;
                background: #f1f5f9;
                color: #13203a;
                font-size: 12px;
                line-height: 1.45;
            }

            .wrap {
                background: #ffffff;
                border-radius: 24px;
                padding: 48px;
            }

            .lt {
                width: 100%;
                border-collapse: collapse;
            }
            .lt td {
                border: 0;
                padding: 0;
                vertical-align: top;
            }

            .doc-title {
                font-size: 22px;
                font-weight: 600;
                color: #13203a;
                margin: 0;
            }

            .logo-img { height: 46px; width: auto; }
            .logo-fallback { font-size: 26px; font-weight: 700; color: #13203a; }

            hr {
                border: 0;
                border-top: 1px solid #fbcfe8;
                margin: 12px 0;
            }

            .addr-label { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
            .addr-body { font-size: 12px; color: #3e5068; line-height: 1.7; margin-top: 4px; }

            .items-tbl {
                width: 100%;
                border-collapse: collapse;
                margin-top: 56px;
                margin-bottom: 12px;
            }

            .items-tbl thead tr { background: #be185d; }

            .items-tbl th {
                padding: 16px;
                text-align: center;
                color: #ffffff;
                font-size: 13px;
                font-weight: 600;
                text-transform: uppercase;
                border-right: 2px solid #ffffff;
            }
            .items-tbl th:last-child { border-right: 0; }
            .items-tbl th.col-desc { text-align: left; }

            .items-tbl td {
                padding: 20px;
                font-size: 13px;
                font-weight: 500;
                text-align: center;
                border-right: 2px solid #ffffff;
                color: #13203a;
            }
            .items-tbl td:last-child { border-right: 0; }
            .items-tbl td.col-desc { text-align: left; font-weight: 600; }

            .totals-cell {
                padding: 4px 0;
                text-align: right;
                border: 0;
                background: #ffffff;
                font-size: 13px;
            }
            .totals-lbl { padding-right: 48px; font-weight: 700; }
            .totals-grand { font-size: 15px; font-weight: 700; }

            .pay-title { font-size: 14px; font-weight: 700; margin-bottom: 8px; }
            .pay-row { font-size: 12px; color: #3e5068; line-height: 1.8; margin-top: 8px; }

            .contact-link {
                font-size: 11px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #13203a;
                display: block;
                margin-top: 4px;
            }

            .fine-print {
                margin-top: 14px;
                font-size: 9px;
                color: #7a8aa3;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="wrap">

            {{-- Header: Document title left, logo right --}}
            <table class="lt">
                <tr>
                    <td style="vertical-align: middle;">
                        <h2 class="doc-title">{{ $documentType }}</h2>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        @if ($lightLogo || $darkLogo)
                            @if ($lightLogo)
                                <img src="{{ $lightLogo }}" alt="{{ $companyName }}" class="logo-img">
                            @elseif ($darkLogo)
                                <img src="{{ $darkLogo }}" alt="{{ $companyName }}" class="logo-img">
                            @endif
                        @else
                            <div class="logo-fallback">{{ $companyName }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <hr>

            {{-- Date + Receipt No --}}
            <table class="lt">
                <tr>
                    <td style="font-size: 13px;"><strong>Date:</strong> {{ $issuedAt->format('d/m/Y') }}</td>
                    <td style="font-size: 13px; text-align: right;"><strong>Receipt No:</strong> {{ $invoice->invoice_number }}</td>
                </tr>
            </table>

            <hr>

            {{-- Addresses: Receipt To (left) + Pay To (right) --}}
            <table class="lt" style="margin-top: 16px;">
                <tr>
                    <td style="width: 50%;">
                        <div class="addr-label">Receipt To:</div>
                        <div class="addr-body">
                            <strong>{{ $billToName }}</strong><br>
                            {{ $billToEmail }}<br>
                            {{ $billToPhone }}
                            @if ($billToAddress !== '')
                                <br>{{ $billToAddress }}
                            @endif
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div class="addr-label">Pay To:</div>
                        <div class="addr-body" style="text-align: right;">
                            <strong>{{ $companyName }}</strong><br>
                            {{ $companyAddressLine1 }}<br>
                            {{ $companyAddressLine2 }}<br>
                            {{ $companyEmail }}<br>
                            {{ $companyPhone }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Items table --}}
            <table class="items-tbl">
                <thead>
                    <tr>
                        <th class="col-desc">Item Description</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lineItems as $i => $lineItem)
                        <tr style="background: {{ $i % 2 === 0 ? '#ffffff' : '#fdf2f8' }};">
                            <td class="col-desc">{{ $lineItem['description'] }}</td>
                            <td>NGN {{ number_format((float) $lineItem['rate'], 2) }}</td>
                            <td>{{ number_format((float) $lineItem['quantity'], 0) }}</td>
                            <td>NGN {{ number_format((float) $lineItem['amount'], 2) }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="4" class="totals-cell" style="padding-top: 16px;">
                            <span class="totals-lbl">Sub Total:</span>NGN {{ number_format((float) $invoice->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="totals-cell">
                            <span class="totals-lbl">Tax:</span>NGN {{ number_format((float) $invoice->tax_amount, 2) }}
                        </td>
                    </tr>
                    @if ((float) $invoice->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="totals-cell">
                                <span class="totals-lbl">Discount:</span>- NGN {{ number_format((float) $invoice->discount_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="totals-cell totals-grand" style="padding-bottom: 12px;">
                            <span class="totals-lbl">Total Paid:</span>NGN {{ number_format((float) $invoice->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Footer: Payment info left, contact right --}}
            <table class="lt" style="margin-top: 112px;">
                <tr>
                    <td style="width: 55%; vertical-align: top;">
                        <div class="pay-title">Payment Info:</div>
                        <div class="pay-row">
                            Method: <strong>{{ $paymentLabel }}</strong><br>
                            Transaction ID: <strong>{{ $paymentReference }}</strong><br>
                            Settled on: <strong>{{ $paidAt->format('M d, Y') }}</strong>
                            @if ($hasCompanyAccountDetails)
                                @if ($companyAccountNumber !== '')
                                    <br>Account No: <strong>{{ $companyAccountNumber }}</strong>
                                @endif
                                @if ($companyAccountName !== '')
                                    <br>Name: <strong>{{ $companyAccountName }}</strong>
                                @endif
                                @if ($companyAccountBankName !== '')
                                    <br>Bank: <strong>{{ $companyAccountBankName }}</strong>
                                @endif
                                @if ($companyAccountNote !== '')
                                    <br>Note: <strong>{{ $companyAccountNote }}</strong>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td style="width: 45%; vertical-align: bottom; text-align: right;">
                        <span class="contact-link">{{ $companyEmail }}</span>
                        <span class="contact-link">{{ $companyPhone }}</span>
                    </td>
                </tr>
            </table>

            <div class="fine-print">
                This is a combined invoice reference and official receipt generated by {{ $companyName }}.
            </div>
        </div>
    </body>
</html>
