<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ShopOrder;
use App\Services\InvoiceLifecycleService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaystackWebhookController extends Controller
{
    /**
     * Server-to-server payment confirmation from Paystack, independent of the
     * customer's browser. Covers the case where a customer pays but never lands
     * back on the callback URL (closed tab, network drop, etc.) — without this,
     * that payment would never get confirmed.
     */
    public function handle(
        Request $request,
        PaystackService $paystack,
        InvoiceLifecycleService $invoiceLifecycleService,
        ShopCheckoutController $shopCheckoutController,
    ): Response {
        if (! $this->hasValidSignature($request)) {
            Log::warning('Paystack webhook: invalid signature.', ['ip' => $request->ip()]);

            return response('Invalid signature', 400);
        }

        if ((string) $request->input('event') !== 'charge.success') {
            // Ack anything we don't act on so Paystack stops retrying it.
            return response('ok', 200);
        }

        $reference = (string) $request->input('data.reference', '');

        if ($reference === '') {
            return response('ok', 200);
        }

        // Never trust the webhook payload's amount/status directly — re-verify
        // the transaction against Paystack's API, per their own recommendation.
        $verification = $paystack->verifyReference($reference);

        if (! $verification['ok'] || strtolower((string) data_get($verification, 'data.status')) !== 'success') {
            Log::info('Paystack webhook: reference did not verify as successful.', ['reference' => $reference]);

            return response('ok', 200);
        }

        $data = (array) ($verification['data'] ?? []);

        $invoice = Invoice::query()->where('payment_reference', $reference)->first();

        if ($invoice) {
            $invoiceLifecycleService->confirmPaystackPayment($invoice, $reference);

            return response('ok', 200);
        }

        $shopOrder = ShopOrder::query()->where('reference', $reference)->first();

        if ($shopOrder) {
            $expectedKobo = (int) round((float) $shopOrder->total * 100);
            $paidKobo = (int) ($data['amount'] ?? 0);

            if ($paidKobo >= $expectedKobo) {
                $shopCheckoutController->confirmPaidOrder($shopOrder, $data, $reference);
            }

            return response('ok', 200);
        }

        Log::info('Paystack webhook: no matching invoice or shop order for reference.', ['reference' => $reference]);

        return response('ok', 200);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = (string) config('services.paystack.secret_key', '');
        $signature = (string) $request->header('X-Paystack-Signature', '');

        if ($secret === '' || $signature === '') {
            return false;
        }

        $computed = hash_hmac('sha512', $request->getContent(), $secret);

        return hash_equals($computed, $signature);
    }
}
