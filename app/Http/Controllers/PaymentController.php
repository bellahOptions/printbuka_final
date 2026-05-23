<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceLifecycleService;
use App\Services\PaystackService;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function process(Request $request, Invoice $invoice, PaystackService $paystackService): RedirectResponse
    {
        $invoice->loadMissing('order');
        $order = $invoice->order;
        $user = $request->user();

        if (! $order || ! $user || (int) $order->user_id !== (int) $user->id) {
            return redirect()
                ->route('user.invoices.index')
                ->with('error', 'You do not have permission to pay this invoice.');
        }

        if (in_array((string) $invoice->status, ['paid', 'cancelled'], true)) {
            return redirect()
                ->route('user.invoices.show', $invoice)
                ->with('status', 'This invoice is not available for payment.');
        }

        $paymentInit = $paystackService->initializeForInvoice($invoice, [
            'payment_context' => 'invoice_retry',
            'initiated_from' => 'invoice_portal',
        ]);

        if (($paymentInit['ok'] ?? false) && filled($paymentInit['authorization_url'] ?? null)) {
            return redirect()->away((string) $paymentInit['authorization_url']);
        }

        return redirect()
            ->route('user.invoices.show', $invoice)
            ->with('warning', $paymentInit['message'] ?? 'Unable to initialize payment right now. Please try again.');
    }

    public function paystackCallback(Request $request, PaystackService $paystackService, InvoiceLifecycleService $invoiceLifecycleService): RedirectResponse
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()->route('home')->with('warning', 'Payment callback is missing a reference.');
        }

        $verification = $paystackService->verifyReference($reference);

        if (! $verification['ok']) {
            return redirect()->route('home')->with('warning', $verification['message'] ?? 'Payment verification failed.');
        }

        $data = (array) ($verification['data'] ?? []);
        $transactionStatus = strtolower((string) ($data['status'] ?? ''));
        $invoiceId = (int) data_get($data, 'metadata.invoice_id', 0);

        $invoice = Invoice::query()
            ->where('payment_reference', $reference)
            ->when($invoiceId > 0, fn ($query) => $query->orWhere('id', $invoiceId))
            ->first();

        if (! $invoice) {
            return redirect()->route('home')->with('warning', 'Payment completed, but invoice record was not found.');
        }

        $order = $invoice->order()->first();

        if (! $order) {
            return redirect()->route('home')->with('warning', 'Payment completed, but order record was not found.');
        }

        if ($transactionStatus !== 'success') {
            return redirect()
                ->to($this->successRouteForOrder($order))
                ->with('warning', 'Payment was not completed. Please try again.');
        }

        $previousStatus = (string) $invoice->status;

        $invoice->forceFill([
            'status' => 'paid',
            'payment_gateway' => 'paystack',
            'payment_reference' => $reference,
            'paid_at' => now(),
        ])->save();

        $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), $previousStatus);

        session()->put('tracked_orders.'.$order->id, true);

        return redirect()
            ->to($this->successRouteForOrder($order))
            ->with('status', 'Payment confirmed successfully. A receipt has been sent to your email.');
    }

    private function successRouteForOrder(Order $order): string
    {
        $serviceSlug = ServiceCatalog::slugFromServiceType($order->service_type);

        if ($serviceSlug) {
            return route('services.orders.success', [
                'service' => $serviceSlug,
                'order' => $order,
            ]);
        }

        return route('orders.success', $order);
    }
}
