<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderFulfillmentService;
use App\Services\PaystackService;
use App\Support\ReferenceCode;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceOrderController extends Controller
{
    public function store(
        Request $request,
        string $service,
        InvoiceService $invoiceService,
        PaystackService $paystackService,
        OrderFulfillmentService $orderFulfillmentService
    ): RedirectResponse {
        $serviceData = ServiceCatalog::find($service);
        abort_if(! $serviceData, 404);

        $customer = $request->user();

        if (in_array($service, ['uv-dtf', 'laser-engraving'], true)) {
            $redirectUrl = route('products.index').match ($service) {
                'uv-dtf' => '#uv-dtf-products',
                'laser-engraving' => '#laser-engraving-products',
                default => '#catalog',
            };

            return redirect()
                ->to($redirectUrl)
                ->with('warning', 'Please order this service from the products section.');
        }

        if (in_array($service, ['direct-image-printing', 'dtf'], true)) {
            if (! $customer) {
                return redirect()
                    ->route('login')
                    ->with('status', 'Please sign in to place this order.');
            }

            $serviceName = (string) ($serviceData['name'] ?? ucfirst(str_replace('-', ' ', $service)));

            return redirect()
                ->route('services.show', $service)
                ->with('warning', 'Use the '.$serviceName.' form on this page to continue.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'delivery_method' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_city' => ['nullable', 'required_unless:delivery_method,Client Pickup', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'required_unless:delivery_method,Client Pickup', 'string', 'max:500'],
            'artwork_notes' => ['required', 'string', 'max:2000'],
        ]);

        if ($customer && $customer->role === 'customer') {
            $validated['customer_name'] = $customer->displayName();
            $validated['customer_email'] = $customer->email;
            $validated['customer_phone'] = $customer->phone;
        }

        if (($validated['delivery_method'] ?? null) === 'Client Pickup') {
            $validated['delivery_city'] = null;
            $validated['delivery_address'] = null;
        }

        $quantity = (int) $validated['quantity'];
        $unitPrice = ServiceCatalog::priceForSlug($service);
        $total = $quantity * $unitPrice;

        $order = Order::query()->create([
            ...$validated,
            'product_id' => null,
            'user_id' => Auth::id(),
            'service_type' => ServiceCatalog::serviceTypeForSlug($service),
            'channel' => 'Online',
            'job_type' => (string) ($serviceData['name'] ?? ucfirst(str_replace('-', ' ', $service))),
            'job_order_number' => ReferenceCode::jobOrderNumber('print'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $total,
            'priority' => '🟡 Normal',
            'brief_received_at' => now(),
            'estimated_delivery_at' => $orderFulfillmentService->estimateForNewOrder(false, now()),
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
            'pricing_breakdown' => [
                'service_price' => $unitPrice,
                'quantity' => $quantity,
                'total' => $total,
            ],
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $sent = $invoiceService->sendInvoice($invoice);

        session()->put('tracked_orders.'.$order->id, true);

        $paymentInit = $paystackService->initializeForInvoice($invoice, [
            'payment_context' => 'service_order',
            'service_slug' => $service,
        ]);

        if (($paymentInit['ok'] ?? false) && filled($paymentInit['authorization_url'] ?? null)) {
            return redirect()->away((string) $paymentInit['authorization_url']);
        }

        return redirect()
            ->route('services.orders.success', ['service' => $service, 'order' => $order])
            ->with(
                ($sent && ! $paystackService->enabled()) ? 'status' : 'warning',
                $paystackService->enabled()
                    ? ($paymentInit['message'] ?? 'Service order submitted. We could not redirect to Paystack right now.')
                    : ($sent
                        ? 'Service order saved. Invoice has been emailed. Payment gateway is not configured yet.'
                        : 'Service order saved, but invoice email could not be sent. Our team will follow up.')
            );
    }

    public function success(string $service, Order $order): View
    {
        $serviceData = ServiceCatalog::find($service);
        abort_if(! $serviceData, 404);
        abort_if($order->service_type !== ServiceCatalog::serviceTypeForSlug($service), 404);

        return view('services.order-success', [
            'service' => $serviceData,
            'order' => $order->load('invoice'),
        ]);
    }
}
