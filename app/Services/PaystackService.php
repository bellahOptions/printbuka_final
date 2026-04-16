<?php

namespace App\Services;

use App\Models\Invoice;
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

    private function reference(): string
    {
        return 'PBK-PAY-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));
    }

    private function secretKey(): string
    {
        return (string) config('services.paystack.secret_key', '');
    }
}
