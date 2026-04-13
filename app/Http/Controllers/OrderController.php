<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
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
        ]);
    }

    public function store(Request $request, Product $product, InvoiceService $invoiceService): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:'.$product->moq],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $product->price;
        $batches = (int) ceil($quantity / max(1, $product->moq));

        $order = Order::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'service_type' => $this->serviceTypeFor($product),
            'unit_price' => $unitPrice,
            'total_price' => $batches * $unitPrice,
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
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
