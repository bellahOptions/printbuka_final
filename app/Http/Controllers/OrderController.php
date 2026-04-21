<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\OrderFulfillmentService;
use App\Services\PaystackService;
use App\Support\ExternalAssetLinks;
use App\Support\JobAssetUpload;
use App\Support\ProductOptionPricing;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Product $product, OrderFulfillmentService $orderFulfillmentService): View
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
            'expressSurcharge' => $orderFulfillmentService->expressSurcharge(),
            'sampleSurcharge' => $orderFulfillmentService->sampleSurcharge(),
        ]);
    }

    public function store(
        Request $request,
        Product $product,
        InvoiceService $invoiceService,
        PaystackService $paystackService,
        OrderFulfillmentService $orderFulfillmentService
    ): RedirectResponse {
        $customer = Auth::user();
        $isSampleRequested = $request->boolean('is_sample');

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
            'quantity' => [
                'required',
                'integer',
                Rule::when(
                    $isSampleRequested,
                    ['min:1', 'max:2'],
                    ['min:'.$product->moq]
                ),
            ],
            'size_format' => ['nullable', 'string', 'max:255'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'paper_density' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'string', 'max:255'],
            'is_express' => ['nullable', 'boolean'],
            'is_sample' => ['nullable', 'boolean'],
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
            'asset_drive_links' => ['nullable', 'string', 'max:4000'],
            'job_asset_files' => ['nullable', 'array', 'max:5'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
            'job_asset_image_paths' => ['nullable', 'array', 'max:5'],
            'job_asset_image_paths.*' => ['string', 'max:255'],
        ]);
        $invalidLinks = ExternalAssetLinks::invalidLinks($validated['asset_drive_links'] ?? null);

        if ($invalidLinks !== []) {
            throw ValidationException::withMessages([
                'asset_drive_links' => 'Use valid external links from Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, or Mega only.',
            ]);
        }

        $validated['artwork_notes'] = ExternalAssetLinks::appendToNotes(
            $validated['artwork_notes'] ?? null,
            $validated['asset_drive_links'] ?? null
        );

        unset($validated['asset_drive_links']);
        unset($validated['job_asset_files']);
        unset($validated['job_asset_image_paths']);

        $uploadedFileCount = count((array) $request->file('job_asset_files'));
        $uploadedPathCount = count((array) $request->input('job_asset_image_paths'));

        if (($uploadedFileCount + $uploadedPathCount) > 5) {
            throw ValidationException::withMessages([
                'job_asset_files' => 'You can upload at most 5 artwork images.',
            ]);
        }

        $isSample = (bool) ($validated['is_sample'] ?? false);
        $isExpress = $isSample || (bool) ($validated['is_express'] ?? false);
        unset($validated['is_express'], $validated['is_sample']);

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
        $productionTotal = $isSample
            ? ($quantity * $productionUnitPrice)
            : ($batches * $productionUnitPrice);
        $pricingAdjustments = $orderFulfillmentService->pricingAdjustments($isExpress, $isSample);
        $total = $productionTotal + $deliveryPrice + $pricingAdjustments['total_adjustment'];
        $serviceType = $this->serviceTypeFor($product);

        $order = Order::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'service_type' => $serviceType,
            'job_order_number' => ReferenceCode::jobOrderNumber($serviceType),
            'channel' => 'Online',
            'job_type' => $product->name,
            'priority' => $isExpress ? '🔴 Urgent' : '🟡 Normal',
            'is_express' => $isExpress,
            'is_sample' => $isSample,
            'brief_received_at' => now(),
            'assigned_designer_id' => Order::autoAssignableDesignerId(),
            'estimated_delivery_at' => $orderFulfillmentService->estimateForNewOrder($isExpress, now()),
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
                'express_fee' => $pricingAdjustments['express_fee'],
                'sample_fee' => $pricingAdjustments['sample_fee'],
                'moq_batches' => $batches,
                'pricing_mode' => $isSample ? 'sample_per_unit' : 'moq_batches',
                'production_unit_price' => $productionUnitPrice,
                'production_total' => $productionTotal,
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
        if (filled($product->service_type)) {
            return (string) $product->service_type;
        }

        $name = strtolower($product->name.' '.$product->short_description.' '.$product->description);

        return str_contains($name, 'gift') || str_contains($name, 'mug') || str_contains($name, 'shirt') || str_contains($name, 'tote')
            ? 'gift'
            : 'print';
    }
}
