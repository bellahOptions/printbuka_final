<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\PaystackService;
use App\Support\JobAssetUpload;
use App\Support\ProductOptionPricing;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Product $product): View
    {
        $customer = Auth::user();
        $savedDeliveryAddresses = $customer?->deliveryAddresses()->get() ?? collect();

        $sizeOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'size_price_options',
            'default_size_price_options',
            $product->paper_size
        );
        $materialOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'material_price_options',
            'default_material_price_options',
            $product->paper_type
        );
        $densityOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'density_price_options',
            'default_density_price_options',
            $product->paper_density
        );
        $finishOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'finish_price_options',
            'default_finish_price_options',
            $product->finishing
        );
        $deliveryOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'delivery_price_options',
            'default_delivery_price_options',
            'Client Pickup'
        );

        return view('orders.create', [
            'product' => $product,
            'serviceType' => $this->serviceTypeFor($product),
            'sizeOptions' => $sizeOptions,
            'materialOptions' => $materialOptions,
            'densityOptions' => $densityOptions,
            'finishOptions' => $finishOptions,
            'deliveryOptions' => $deliveryOptions,
            'savedDeliveryAddresses' => $savedDeliveryAddresses,
        ]);
    }

    public function store(
        Request $request,
        Product $product,
        InvoiceService $invoiceService,
        PaystackService $paystackService
    ): RedirectResponse {
        $customer = Auth::user();

        $sizeOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'size_price_options',
            'default_size_price_options',
            $product->paper_size
        );
        $materialOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'material_price_options',
            'default_material_price_options',
            $product->paper_type
        );
        $densityOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'density_price_options',
            'default_density_price_options',
            $product->paper_density
        );
        $finishOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'finish_price_options',
            'default_finish_price_options',
            $product->finishing
        );
        $deliveryOptions = ProductOptionPricing::optionsForProductOrSetting(
            $product,
            'delivery_price_options',
            'default_delivery_price_options',
            'Client Pickup'
        );

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:'.$product->moq],
            'size_format' => ['nullable', 'string', 'max:255'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'paper_density' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'delivery_address_id' => [
                'nullable',
                'integer',
                Rule::exists('delivery_addresses', 'id')->where(
                    fn ($query) => $query->where('user_id', (int) (Auth::id() ?? 0))
                ),
            ],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'job_asset_files' => ['nullable', 'array', 'max:5'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
        ]);
        unset($validated['job_asset_files']);

        if ($customer && $customer->role === 'customer') {
            $validated['customer_name'] = $customer->displayName();
            $validated['customer_email'] = $customer->email;
            $validated['customer_phone'] = $customer->phone;
        }

        if ($customer && filled($validated['delivery_address_id'] ?? null)) {
            $selectedDeliveryAddress = $customer->deliveryAddresses()->whereKey($validated['delivery_address_id'])->first();

            if ($selectedDeliveryAddress) {
                $validated['delivery_city'] = $selectedDeliveryAddress->city;
                $validated['delivery_address'] = $selectedDeliveryAddress->address;
            }
        }

        unset($validated['delivery_address_id']);

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $product->price;
        $sizePrice = ProductOptionPricing::priceFromOptions($sizeOptions, $validated['size_format'] ?? null);
        $materialPrice = ProductOptionPricing::priceFromOptions($materialOptions, $validated['material_substrate'] ?? null);
        $densityPrice = ProductOptionPricing::priceFromOptions($densityOptions, $validated['paper_density'] ?? null);
        $finishPrice = ProductOptionPricing::priceFromOptions($finishOptions, $validated['finish_lamination'] ?? null);
        $deliveryPrice = ProductOptionPricing::priceFromOptions($deliveryOptions, $validated['delivery_method'] ?? null);
        $productionUnitPrice = $unitPrice + $sizePrice + $materialPrice + $densityPrice + $finishPrice;
        $batches = (int) ceil($quantity / max(1, $product->moq));
        $total = ($batches * $productionUnitPrice) + $deliveryPrice;
        $serviceType = $this->serviceTypeFor($product);

        $order = Order::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'service_type' => $serviceType,
            'job_order_number' => ReferenceCode::jobOrderNumber($serviceType),
            'channel' => 'Online',
            'job_type' => $product->name,
            'unit_price' => $productionUnitPrice,
            'total_price' => $total,
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
            'pricing_breakdown' => [
                'base_price' => $unitPrice,
                'size_price' => $sizePrice,
                'material_price' => $materialPrice,
                'density_price' => $densityPrice,
                'finish_price' => $finishPrice,
                'delivery_price' => $deliveryPrice,
                'moq_batches' => $batches,
                'production_unit_price' => $productionUnitPrice,
                'total' => $total,
            ],
            'job_image_assets' => JobAssetUpload::fromRequest($request),
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $sent = $invoiceService->sendInvoice($invoice);
        session()->put('tracked_orders.'.$order->id, true);

        $paymentInit = $paystackService->initializeForInvoice($invoice);

        if (($paymentInit['ok'] ?? false) && filled($paymentInit['authorization_url'] ?? null)) {
            return redirect()->away((string) $paymentInit['authorization_url']);
        }

        return redirect()
            ->route('orders.success', $order)
            ->with(
                ($sent && ! $paystackService->enabled()) ? 'status' : 'warning',
                $paystackService->enabled()
                    ? ($paymentInit['message'] ?? 'Order submitted. We could not redirect to Paystack right now.')
                    : ($sent
                        ? 'Your invoice has been emailed with a PDF attachment. Payment gateway is not yet configured.'
                        : 'Your invoice was created, but the email could not be sent. Our team will follow up.')
            );
    }

    public function success(Order $order): View
    {
        return view('orders.success', [
            'order' => $order->load('product', 'invoice'),
        ]);
    }

    private function serviceTypeFor(Product $product): string
    {
        $name = strtolower($product->name.' '.$product->short_description.' '.$product->description);

        return str_contains($name, 'gift') || str_contains($name, 'mug') || str_contains($name, 'shirt') || str_contains($name, 'tote')
            ? 'gift'
            : 'print';
    }
}
