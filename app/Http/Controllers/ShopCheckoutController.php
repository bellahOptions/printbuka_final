<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderItemOption;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopCheckoutController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $cartItems = ShopCartController::resolveCartItems();

        if (empty($cartItems)) {
            return redirect()->route('shop.cart')->with('warning', 'Your cart is empty.');
        }

        $subtotal = collect($cartItems)->sum('line_total');
        $user = auth()->user();

        return view('shop.checkout', compact('cartItems', 'subtotal', 'user'));
    }

    public function store(Request $request, PaystackService $paystack): RedirectResponse
    {
        $cartItems = ShopCartController::resolveCartItems();

        if (empty($cartItems)) {
            return redirect()->route('shop.cart')->with('warning', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Always compute totals server-side — never trust client-submitted amounts
        $subtotal = round(collect($cartItems)->sum('line_total'), 2);
        $total = $subtotal;

        $reference = 'PBK-SHOP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $order = ShopOrder::create([
            ...$validated,
            'reference' => $reference,
            'user_id' => auth()->id(),
            'subtotal' => $subtotal,
            'total' => $total,
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending',
        ]);

        foreach ($cartItems as $cartItem) {
            $item = ShopOrderItem::create([
                'shop_order_id' => $order->id,
                'shop_product_id' => $cartItem['product']->id,
                'product_name' => $cartItem['product']->name,
                'unit_price' => $cartItem['unit_price'],
                'quantity' => $cartItem['quantity'],
                'line_total' => $cartItem['line_total'],
            ]);

            foreach ($cartItem['selected_options'] as $option) {
                ShopOrderItemOption::create([
                    'shop_order_item_id' => $item->id,
                    'group_name' => $option->group?->name ?? 'Option',
                    'option_name' => $option->name,
                    'price_modifier' => $option->price_modifier,
                ]);
            }
        }

        $amountKobo = (int) round($total * 100);

        $init = $paystack->initialize(
            email: $validated['customer_email'],
            amountKobo: $amountKobo,
            reference: $reference,
            callbackUrl: route('shop.checkout.callback'),
            metadata: [
                'shop_order_id' => $order->id,
                'customer_name' => $validated['customer_name'],
            ]
        );

        if (! $init['ok']) {
            $order->update(['payment_status' => 'failed']);

            return back()
                ->with('error', 'Payment could not be initialized. ' . ($init['message'] ?? 'Please try again.'))
                ->withInput();
        }

        // Clear cart only after successful Paystack initialization
        session()->forget('shop.cart');

        return redirect()->away($init['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack): RedirectResponse
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()->route('shop.index')->with('warning', 'Payment callback is missing a reference.');
        }

        $order = ShopOrder::where('reference', $reference)->first();

        if (! $order) {
            return redirect()->route('shop.index')->with('warning', 'Order not found.');
        }

        // Idempotent — already confirmed
        if ($order->payment_status === 'paid') {
            return redirect()->route('shop.orders.confirmation', $order->reference);
        }

        $verification = $paystack->verifyReference($reference);

        if (! $verification['ok']) {
            return redirect()->route('shop.checkout')
                ->with('error', 'Payment verification failed. Please contact support if funds were deducted.');
        }

        $data = (array) ($verification['data'] ?? []);
        $status = strtolower((string) ($data['status'] ?? ''));

        // Verify amount to guard against partial-payment attacks
        $expectedKobo = (int) round((float) $order->total * 100);
        $paidKobo = (int) ($data['amount'] ?? 0);

        if ($status === 'success' && $paidKobo >= $expectedKobo) {
            $order->update([
                'payment_status' => 'paid',
                'paystack_reference' => $reference,
                'paystack_data' => $data,
            ]);

            return redirect()->route('shop.orders.confirmation', $order->reference)
                ->with('status', 'Payment confirmed! Your order is being processed.');
        }

        $order->update(['payment_status' => 'failed', 'paystack_data' => $data]);

        return redirect()->route('shop.checkout')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    public function confirmation(string $reference): View|RedirectResponse
    {
        $order = ShopOrder::with('items.selectedOptions')->where('reference', $reference)->first();

        if (! $order) {
            return redirect()->route('shop.index');
        }

        return view('shop.confirmation', compact('order'));
    }
}
