<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackOrderController extends Controller
{
    public function create(): View
    {
        return view('orders.track');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'customer_email' => ['required', 'email', 'max:255'],
        ]);

        $orderId = (int) preg_replace('/\D+/', '', $validated['order_number']);

        $order = Order::query()
            ->whereKey($orderId)
            ->where('customer_email', $validated['customer_email'])
            ->first();

        if (! $order) {
            return back()
                ->withErrors([
                    'order_number' => 'We could not find an order with those details.',
                ])
                ->onlyInput('order_number', 'customer_email');
        }

        session()->put('tracked_orders.'.$order->id, true);

        return redirect()->route('orders.track.show', $order);
    }

    public function show(Order $order): View|RedirectResponse
    {
        if (! session()->get('tracked_orders.'.$order->id)) {
            return redirect()
                ->route('orders.track')
                ->withErrors([
                    'order_number' => 'Please confirm your order number and email first.',
                ]);
        }

        return view('orders.tracking', [
            'order' => $order->load('product', 'invoice'),
        ]);
    }
}
