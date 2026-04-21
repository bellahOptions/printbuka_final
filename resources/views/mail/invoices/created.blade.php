<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>{{ $invoice->invoice_number }}</title>
        <style>
            .logo-dark {
                display: none !important;
            }
        </style>
    </head>
    @php
        $documentType = $invoice->documentTypeLabel();
        $documentTypeLower = strtolower($documentType);
        $lightLogoUrl = asset('logo-dark.svg');
        $darkLogoUrl = asset('logo-dark.svg');
        $settings = \App\Support\SiteSettings::all();
        $companyAccountName = trim((string) ($settings['company_account_name'] ?? ''));
        $companyAccountNumber = trim((string) ($settings['company_account_number'] ?? ''));
        $companyAccountBankName = trim((string) ($settings['company_account_bank_name'] ?? ''));
        $companyAccountNote = trim((string) ($settings['company_account_note'] ?? ''));
        $hasCompanyAccountDetails = $companyAccountName !== '' || $companyAccountNumber !== '' || $companyAccountBankName !== '' || $companyAccountNote !== '';
    @endphp
    <body class="email-body" style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;" class="email-body">
            <tr>
                <td align="center">
                    <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;" class="email-card">
                        <tr>
                            <td style="background:#0f172a;color:#ffffff;padding:24px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="vertical-align:top;">
                                            <img src="{{ $lightLogoUrl }}" alt="Printbuka" width="154" style="display:inline-block;height:auto;" class="logo-light">
                                            <img src="{{ $darkLogoUrl }}" alt="Printbuka dark" width="154" style="display:none;height:auto;" class="logo-dark">
                                        </td>
                                        <td style="text-align:right;vertical-align:top;color:#cbd5e1;font-size:12px;">
                                            {{ strtoupper($documentType) }}
                                        </td>
                                    </tr>
                                </table>
                                <h1 style="margin:16px 0 0;font-size:28px;line-height:1.2;">Your Printbuka {{ $documentTypeLower }} is ready</h1>
                                <p style="margin:10px 0 0;color:#cbd5e1;line-height:1.5;">{{ $documentType }} {{ $invoice->invoice_number }} for order {{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }} is attached as a PDF.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="email-content" style="padding:26px;">
                                <p style="margin:0 0 16px;">Hello {{ $invoice->order->customer_name }},</p>
                                <p style="margin:0 0 20px;line-height:1.6;">Thank you for placing your {{ $invoice->order->service_type }} order with Printbuka. Your {{ $documentTypeLower }} is attached, and our team will follow up on the next production steps.</p>

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
                                        <td style="padding:12px;border:1px solid #e2e8f0;">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Payment status</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #e2e8f0;font-weight:bold;">Estimated delivery</td>
                                        <td style="padding:12px;border:1px solid #e2e8f0;">{{ $invoice->order->estimated_delivery_at?->format('M d, Y h:i A') ?? 'To be confirmed' }}</td>
                                    </tr>
                                </table>

                                @if ($hasCompanyAccountDetails)
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px 0;">
                                        <tr>
                                            <td style="padding:14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;">
                                                <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:0.3px;">Company account details</p>
                                                @if ($companyAccountBankName !== '')
                                                    <p style="margin:0 0 4px;line-height:1.5;"><strong>Bank:</strong> {{ $companyAccountBankName }}</p>
                                                @endif
                                                @if ($companyAccountName !== '')
                                                    <p style="margin:0 0 4px;line-height:1.5;"><strong>Account name:</strong> {{ $companyAccountName }}</p>
                                                @endif
                                                @if ($companyAccountNumber !== '')
                                                    <p style="margin:0 0 4px;line-height:1.5;"><strong>Account number:</strong> {{ $companyAccountNumber }}</p>
                                                @endif
                                                @if ($companyAccountNote !== '')
                                                    <p style="margin:0;line-height:1.5;"><strong>Note:</strong> {{ $companyAccountNote }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                @endif

                                <p style="margin:0;line-height:1.6;">You can track your order with order number <strong>{{ $invoice->order->job_order_number ?? $invoice->order->displayNumber() }}</strong> and this email address.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
