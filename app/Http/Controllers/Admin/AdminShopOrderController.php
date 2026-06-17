<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminShopOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = ShopOrder::query()
            ->withCount('items')
            ->when($request->input('status'), fn ($q, $s) => $q->where('fulfillment_status', $s))
            ->when($request->input('payment'), fn ($q, $p) => $q->where('payment_status', $p))
            ->when($request->input('search'), fn ($q, $s) => $q->where(function ($q) use ($s): void {
                $q->where('reference', 'like', "%{$s}%")
                    ->orWhere('customer_name', 'like', "%{$s}%")
                    ->orWhere('customer_email', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.shop-orders.index', compact('orders'));
    }

    public function show(ShopOrder $shopOrder): View
    {
        $shopOrder->loadMissing(['items.selectedOptions', 'items.product']);

        return view('admin.shop-orders.show', ['order' => $shopOrder]);
    }

    public function updateStatus(Request $request, ShopOrder $shopOrder): RedirectResponse
    {
        $validated = $request->validate([
            'fulfillment_status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $shopOrder->update($validated);

        return back()->with('status', 'Order status updated to ' . $validated['fulfillment_status'] . '.');
    }
}
