<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        @php
            $order = $invoice->order;
            $settings = \App\Support\SiteSettings::all();

            $documentType = strtoupper($invoice->documentTypeLabel());
            $documentId = '#'.($invoice->id ?? preg_replace('/\D+/', '', (string) $invoice->invoice_number));

            $companyName = (string) ($settings['site_name'] ?? config('app.name', 'Printbuka'));
            $companyAddressLine1 = (string) ($settings['company_address_line_1'] ?? '63, Akeju Street, off Shipeolu St, Somolu, Lagos');
            $companyAddressLine2 = (string) ($settings['company_address_line_2'] ?? '100001, Lagos');
            $companyEmail = (string) ($settings['contact_email'] ?? 'sales@printbuka.com.ng');
            $companyPhone = (string) ($settings['contact_phone'] ?? '08035245784, 09054784526');
            $payToAccount = $invoice->resolvedCompanyAccount();
            $companyAccountName = trim((string) ($payToAccount?->account_name ?? $settings['company_account_name'] ?? ''));
            $companyAccountNumber = trim((string) ($payToAccount?->account_number ?? $settings['company_account_number'] ?? ''));
            $companyAccountBankName = trim((string) ($payToAccount?->bank_name ?? $settings['company_account_bank_name'] ?? ''));
            $companyAccountNote = trim((string) ($payToAccount?->note ?? $settings['company_account_note'] ?? ''));
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
            $dueAt = $invoice->due_at ?? now();
            $paidAt = $invoice->paid_at;

            $amountPaid = (float) ($order?->amount_paid ?? 0);
            $balanceDue = max(0, (float) $invoice->total_amount - $amountPaid);
            $statusLabel = str((string) ($invoice->status ?? 'pending'))->replace('_', ' ')->title()->value();
            $isPaid = $paidAt !== null || in_array(strtolower((string) $invoice->status), ['paid', 'settled', 'completed'], true) || $balanceDue <= 0.01;
            $effectivePaidAt = $paidAt ?? now();

            $paymentMethod = (string) ($invoice->payment_gateway ?? 'bank_transfer');
            $paymentLabel = str($paymentMethod)->replace('_', ' ')->upper()->value();
            $paymentReference = (string) ($invoice->payment_reference ?: 'Pending');

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

            @page { margin: 20mm 20mm 25mm 20mm; }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                padding: 0;
                font-family: 'Open Sans', 'DejaVu Sans', Arial, sans-serif;
                background: #ffffff;
                color: #333333;
                font-size: 12px;
                line-height: 1.5;
            }
            /* ₦ pinned to DejaVu Sans — the embedded Open Sans glyph set lacks it */
            .naira { font-family: 'DejaVu Sans', sans-serif; font-weight: bold; font-size: inherit; }

            .wrap { width: 100%; background: #ffffff; }

            .header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
            .header-tbl td { border: 0; padding: 0; vertical-align: top; }
            .header-left { width: 60%; }
            .logo-img { height: 40px; width: auto; margin-bottom: 8px; }
            .logo-fallback { font-size: 20px; font-weight: 700; color: #13203a; margin-bottom: 8px; }
            .company-info { line-height: 1.5; font-size: 11px; color: #444444; }
            .invoice-meta { width: 40%; text-align: right; font-size: 11px; }
            .invoice-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #13203a; }

            .recipient { margin-bottom: 20px; font-size: 11px; line-height: 1.6; }

            .items-tbl { width: 100%; border-collapse: collapse; border-bottom: 1px solid #dddddd; margin-bottom: 16px; }
            .items-tbl th, .items-tbl td { border-top: 1px solid #dddddd; padding: 7px 6px; text-align: left; vertical-align: top; font-size: 10.5px; }
            .items-tbl th { background: #f5f5f5; font-weight: 700; color: #333333; }

            .totals-wrap { width: 45%; float: right; margin-bottom: 4px; }
            .totals-tbl { width: 100%; border-collapse: collapse; }
            .totals-tbl td { padding: 4px 0; font-size: 11px; border: 0; }
            .totals-grand td { font-size: 13px; font-weight: 700; padding-top: 6px; }
            .clearfix { clear: both; }

            .notes { clear: both; text-align: center; margin: 22px 0; font-size: 10.5px; color: #555555; white-space: pre-line; }
            .notes strong { display: block; margin-bottom: 4px; color: #13203a; font-size: 11px; }

            .bottom-tbl { width: 100%; border-collapse: collapse; margin-top: 22px; page-break-inside: avoid; }
            .bottom-tbl td { border: 0; padding: 0; vertical-align: top; font-size: 10px; line-height: 1.7; }
            .bank-info { width: 100%; text-align: right; }
            .pay-title { font-weight: 700; margin-bottom: 3px; color: #13203a; }

            .fine-print { margin-top: 20px; font-size: 8px; color: #7a8aa3; text-align: center; }
        </style>
    </head>
    <body>
        <div class="wrap">
            {!! $introHtml ?? '' !!}

            {{-- Header: company (left) + document title & meta (right) --}}
            <table class="header-tbl">
                <tr>
                    <td class="header-left">
                        @if ($lightLogo || $darkLogo)
                            @if ($lightLogo)
                                <img src="{{ $lightLogo }}" alt="{{ $companyName }}" class="logo-img">
                            @elseif ($darkLogo)
                                <img src="{{ $darkLogo }}" alt="{{ $companyName }}" class="logo-img">
                            @endif
                        @else
                            <div class="logo-fallback">{{ $companyName }}</div>
                        @endif
                        <div class="company-info">
                            {{ $companyAddressLine1 }}<br>
                            {{ $companyAddressLine2 }}<br>
                            {{ $companyEmail }}<br>
                            {{ $companyPhone }}
                        </div>
                    </td>
                    <td class="invoice-meta">
                        <div class="invoice-title">{{ $documentType }}</div>
                        <div><strong>{{ $documentType }} No.:</strong> {{ $invoice->invoice_number }}</div>
                        <div><strong>Date:</strong> {{ $issuedAt->format('d/m/Y') }}</div>
                        <div><strong>Due Date:</strong> {{ $dueAt->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>

            {{-- Recipient --}}
            <div class="recipient">
                <strong>{{ $documentType }} To:</strong><br>
                {{ $billToName }}<br>
                {{ $billToEmail }}<br>
                {{ $billToPhone }}
                @if ($billToAddress !== '')
                    <br>{{ $billToAddress }}
                @endif
            </div>

            {{-- Items table --}}
            <table class="items-tbl">
                <thead>
                    <tr>
                        <th style="width: 6%;">#</th>
                        <th>Description</th>
                        <th style="width: 10%; text-align: right;">Qty</th>
                        <th style="width: 17%; text-align: right;">Unit Price</th>
                        <th style="width: 17%; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lineItems as $i => $lineItem)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $lineItem['description'] }}</td>
                            <td style="text-align: right;">{{ number_format((float) $lineItem['quantity'], 0) }}</td>
                            <td style="text-align: right;"><span class="naira">₦</span>{{ number_format((float) $lineItem['rate'], 2) }}</td>
                            <td style="text-align: right;"><span class="naira">₦</span>{{ number_format((float) $lineItem['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="totals-wrap">
                <table class="totals-tbl">
                    <tr>
                        <td>Sub Total:</td>
                        <td style="text-align: right;"><span class="naira">₦</span>{{ number_format((float) $invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax:</td>
                        <td style="text-align: right;"><span class="naira">₦</span>{{ number_format((float) $invoice->tax_amount, 2) }}</td>
                    </tr>
                    @if ((float) $invoice->discount_amount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td style="text-align: right;">- <span class="naira">₦</span>{{ number_format((float) $invoice->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="totals-grand">
                        <td><strong>Total:</strong></td>
                        <td style="text-align: right;"><strong><span class="naira">₦</span>{{ number_format((float) $invoice->total_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>

            {{-- Notes --}}
            @if (filled($order?->artwork_notes))
                <div class="notes">
                    <strong>Additional Notes</strong>
                    {{ $order->artwork_notes }}
                </div>
            @else
                <div class="notes">It was a pleasure doing business with you.</div>
            @endif

            {{-- Bottom: Payment info --}}
            <table class="bottom-tbl">
                <tr>
                    <td class="bank-info">
                        @if ($hasCompanyAccountDetails)
                            <div class="pay-title">Payment Info:</div>
                            @if ($companyAccountNumber !== '')
                                Account No: <strong>{{ $companyAccountNumber }}</strong><br>
                            @endif
                            @if ($companyAccountName !== '')
                                Name: <strong>{{ $companyAccountName }}</strong><br>
                            @endif
                            @if ($companyAccountBankName !== '')
                                Bank: <strong>{{ $companyAccountBankName }}</strong>
                            @endif
                            @if ($companyAccountNote !== '')
                                <br>Note: <strong>{{ $companyAccountNote }}</strong>
                            @endif
                        @else
                            <div class="pay-title">Payment:</div>
                            {{ $isPaid ? $paymentLabel : 'Awaiting payment' }}
                            @if ($isPaid)
                                <br>Transaction ID: <strong>{{ $paymentReference }}</strong>
                            @else
                                <br>Due date: <strong>{{ $dueAt->format('M d, Y') }}</strong>
                            @endif
                        @endif
                        @if (filled($order?->payment_terms))
                            <div class="pay-title" style="margin-top: 8px;">Payment Terms:</div>
                            {{ config('printbuka_admin.payment_terms.'.$order->payment_terms) ?: str($order->payment_terms)->replace('_', ' ')->title() }}
                        @endif
                    </td>
                </tr>
            </table>

            <div class="fine-print">
                This document is generated by {{ $companyName }} and is valid without a signature.
            </div>
            {!! $outroHtml ?? '' !!}
        </div>
    </body>
</html>
