<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ShopOrderStatusUpdateMail;
use App\Models\ShopOrder;
use App\Models\User;
use App\Notifications\StaffPushNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminShopOrderController extends Controller
{
    public const FULFILLMENT_STATUSES = ['order_received', 'processing', 'dispatched', 'delivered'];

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

        $stats = [
            'total'          => ShopOrder::count(),
            'order_received' => ShopOrder::where('fulfillment_status', 'order_received')->count(),
            'processing'     => ShopOrder::where('fulfillment_status', 'processing')->count(),
            'dispatched'     => ShopOrder::where('fulfillment_status', 'dispatched')->count(),
            'delivered'      => ShopOrder::where('fulfillment_status', 'delivered')->count(),
            'paid'           => ShopOrder::where('payment_status', 'paid')->count(),
        ];

        return view('admin.shop-orders.index', compact('orders', 'stats'));
    }

    public function show(ShopOrder $shopOrder): View
    {
        $shopOrder->loadMissing(['items.selectedOptions', 'items.product']);

        return view('admin.shop-orders.show', ['order' => $shopOrder]);
    }

    public function updateStatus(Request $request, ShopOrder $shopOrder): RedirectResponse
    {
        $validated = $request->validate([
            'fulfillment_status' => ['required', 'string', 'in:order_received,processing,dispatched,delivered'],
        ]);

        $oldStatus = $shopOrder->fulfillment_status;
        $newStatus = $validated['fulfillment_status'];

        if ($oldStatus === $newStatus) {
            return back()->with('status', 'Status is already set to that value.');
        }

        $shopOrder->update($validated);

        $this->notifyCustomer($shopOrder, $newStatus);

        $statusLabel = match ($newStatus) {
            'order_received' => 'Order Received',
            'processing'     => 'Processing',
            'dispatched'     => 'Dispatched',
            'delivered'      => 'Delivered',
            default          => ucfirst($newStatus),
        };

        return back()->with('status', "Order status updated to \"{$statusLabel}\".");
    }

    private function notifyCustomer(ShopOrder $shopOrder, string $newStatus): void
    {
        if (! filled($shopOrder->customer_email)) {
            return;
        }

        try {
            Mail::to($shopOrder->customer_email)->queue(new ShopOrderStatusUpdateMail($shopOrder, $newStatus));
        } catch (\Throwable $e) {
            Log::error('Shop order status email failed.', [
                'order_id' => $shopOrder->id,
                'status'   => $newStatus,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public static function notifyStaffNewOrder(ShopOrder $order): void
    {
        $recipients = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $u): bool => $u->canAdmin('shop-orders.view') || $u->canAdmin('*'));

        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new StaffPushNotification(
                    title: 'New Shop Order',
                    body: "Order {$order->reference} — NGN " . number_format((float) $order->total, 0) . ' from ' . $order->customer_name,
                    type: 'shop_order_new',
                    data: [
                        'order_id'  => $order->id,
                        'reference' => $order->reference,
                        'total'     => (float) $order->total,
                    ],
                ));
            } catch (\Throwable $e) {
                Log::error('Shop order staff push notification failed.', [
                    'order_id'     => $order->id,
                    'recipient_id' => $recipient->id,
                    'error'        => $e->getMessage(),
                ]);
            }
        }
    }
}
