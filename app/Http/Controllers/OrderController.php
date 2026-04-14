<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Support\JobAssetUpload;
use App\Support\ProductOptionPricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Product $product): View
    {
        return view('orders.create', [
            'product' => $product,
            'serviceType' => $this->serviceTypeFor($product),
            'sizeOptions' => $product->size_price_options ?: [['label' => $product->paper_size, 'price' => 0]],
            'materialOptions' => $product->material_price_options ?: [['label' => $product->paper_type, 'price' => 0]],
            'finishOptions' => $product->finish_price_options ?: [['label' => $product->finishing, 'price' => 0]],
            'deliveryOptions' => $product->delivery_price_options ?: [
                ['label' => 'Pickup', 'price' => 0],
                ['label' => 'Deliver to address', 'price' => 0],
            ],
        ]);
    }

    public function store(Request $request, Product $product, InvoiceService $invoiceService): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:'.$product->moq],
            'size_format' => ['nullable', 'string', 'max:255'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,svg,zip', 'max:20480'],
        ]);
        unset($validated['job_asset_files']);

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $product->price;
        $sizePrice = ProductOptionPricing::priceFor($product, 'size_price_options', $validated['size_format'] ?? null);
        $materialPrice = ProductOptionPricing::priceFor($product, 'material_price_options', $validated['material_substrate'] ?? null);
        $finishPrice = ProductOptionPricing::priceFor($product, 'finish_price_options', $validated['finish_lamination'] ?? null);
        $deliveryPrice = ProductOptionPricing::priceFor($product, 'delivery_price_options', $validated['delivery_method'] ?? null);
        $productionUnitPrice = $unitPrice + $sizePrice + $materialPrice + $finishPrice;
        $batches = (int) ceil($quantity / max(1, $product->moq));
        $total = ($batches * $productionUnitPrice) + $deliveryPrice;

        $order = Order::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'service_type' => $this->serviceTypeFor($product),
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
                'finish_price' => $finishPrice,
                'delivery_price' => $deliveryPrice,
                'moq_batches' => $batches,
                'production_unit_price' => $productionUnitPrice,
                'total' => $total,
            ],
            'job_image_assets' => JobAssetUpload::fromRequest($request),
        ]);
        $order->update([
            'job_order_number' => 'PB-'.now()->format('Y').'-'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT),
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $sent = $invoiceService->sendInvoice($invoice);
        session()->put('tracked_orders.'.$order->id, true);

        return redirect()
            ->route('orders.success', $order)
            ->with(
                $sent ? 'status' : 'warning',
                $sent
                    ? 'Your invoice has been emailed with a PDF attachment.'
                    : 'Your invoice was created, but the email could not be sent. Our team will follow up.'
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
