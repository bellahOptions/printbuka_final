<?php

namespace App\Services;

use App\Models\Invoice;
use App\Support\SafeCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    public function enabled(): bool
    {
        return filled($this->secretKey());
    }

    /**
     * @return array{ok:bool,authorization_url?:string,reference?:string,message?:string}
     */
    public function initializeForInvoice(Invoice $invoice, array $metadata = [], ?string $callbackUrl = null): array
    {
        if (! $this->enabled()) {
            return [
                'ok' => false,
                'message' => 'Paystack is not configured.',
            ];
        }

        $invoice->loadMissing('order');
        $order = $invoice->order;
        $email = (string) ($order?->customer_email ?? '');

        if ($email === '') {
            return [
                'ok' => false,
                'message' => 'Customer email is missing for payment initialization.',
            ];
        }

        $reference = $invoice->payment_reference ?: $this->reference();
        $amountKobo = (int) round(((float) $invoice->total_amount) * 100);

        $invoice->forceFill([
            'payment_reference' => $reference,
            'payment_gateway' => 'paystack',
        ])->save();

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $amountKobo,
                'currency' => 'NGN',
                'reference' => $reference,
                'callback_url' => $callbackUrl ?: route('payments.paystack.callback'),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'order_id' => $invoice->order_id,
                    ...$metadata,
                ],
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?: 'Unable to initialize Paystack transaction.'),
                'reference' => $reference,
            ];
        }

        return [
            'ok' => true,
            'authorization_url' => (string) $response->json('data.authorization_url'),
            'reference' => $reference,
        ];
    }

    /**
     * Generic payment initializer — use for shop checkout and other non-invoice flows.
     *
     * @param  array<string, mixed>  $metadata
     * @return array{ok:bool,authorization_url?:string,reference?:string,message?:string}
     */
    public function initialize(string $email, int $amountKobo, string $reference, string $callbackUrl, array $metadata = []): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'message' => 'Paystack is not configured.'];
        }

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $amountKobo,
                'currency' => 'NGN',
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?: 'Unable to initialize payment.'),
            ];
        }

        return [
            'ok' => true,
            'authorization_url' => (string) $response->json('data.authorization_url'),
            'reference' => $reference,
        ];
    }

    /**
     * @return array{ok:bool,data?:array<string,mixed>,message?:string}
     */
    public function verifyReference(string $reference): array
    {
        if ($reference === '' || ! $this->enabled()) {
            return [
                'ok' => false,
                'message' => 'Payment verification cannot continue.',
            ];
        }

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->get('https://api.paystack.co/transaction/verify/'.$reference);

        if (! $response->successful() || ! $response->json('status')) {
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?: 'Could not verify Paystack transaction.'),
            ];
        }

        $data = (array) $response->json('data', []);

        return [
            'ok' => true,
            'data' => $data,
        ];
    }

    /**
     * @return array{ok:bool,data?:array<int,array<string,mixed>>,message?:string}
     */
    public function listBanks(): array
    {
        if (! $this->enabled()) {
            return [
                'ok' => false,
                'message' => 'Paystack is not configured.',
            ];
        }

        // Only successful lookups are cached — a transient Paystack failure
        // must not be remembered and served for the rest of the week.
        $banks = SafeCache::remember('paystack.banks.nigeria', now()->addWeek(), function (): ?array {
            $response = Http::withToken($this->secretKey())
                ->acceptJson()
                ->get('https://api.paystack.co/bank', [
                    'country' => 'nigeria',
                    'currency' => 'NGN',
                ]);

            if (! $response->successful() || ! $response->json('status')) {
                return null;
            }

            return (array) $response->json('data', []);
        });

        if ($banks === null) {
            SafeCache::forget('paystack.banks.nigeria');

            return [
                'ok' => false,
                'message' => 'Could not fetch the bank list.',
            ];
        }

        return [
            'ok' => true,
            'data' => $banks,
        ];
    }

    /**
     * @return array{ok:bool,data?:array<string,mixed>,message?:string}
     */
    public function resolveAccount(string $accountNumber, string $bankCode): array
    {
        if ($accountNumber === '' || $bankCode === '' || ! $this->enabled()) {
            return [
                'ok' => false,
                'message' => 'Account resolution cannot continue.',
            ];
        }

        $response = Http::withToken($this->secretKey())
            ->acceptJson()
            ->get('https://api.paystack.co/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?: 'Could not resolve this account number.'),
            ];
        }

        return [
            'ok' => true,
            'data' => (array) $response->json('data', []),
        ];
    }

    private function reference(): string
    {
        return 'PBK-PAY-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));
    }

    private function secretKey(): string
    {
        return (string) config('services.paystack.secret_key', '');
    }
}
