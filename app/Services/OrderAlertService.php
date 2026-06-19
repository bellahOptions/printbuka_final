<?php

namespace App\Services;

use App\Mail\OrderAlertMail;
use App\Models\DailyTodo;
use App\Models\Order;
use App\Models\ShopOrder;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderAlertService
{
    /**
     * Roles that must be notified of every new shop order and quote request.
     * Roles from config: 'super_admin', 'managing_director', 'customer_service'
     */
    private const ALERT_ROLES = ['super_admin', 'managing_director', 'customer_service'];

    public function notifyShopOrder(ShopOrder $order): void
    {
        $order->loadMissing('items');

        $recipients = $this->recipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $title   = '🛍️ New Shop Order: ' . $order->reference;
        $message = sprintf(
            '%s ordered %d item(s) — NGN %s. Payment: %s.',
            $order->customer_name,
            $order->items->count(),
            number_format((float) $order->total, 0),
            ucfirst($order->payment_status),
        );
        $actionUrl = route('admin.shop-orders.show', $order);
        $taskText  = sprintf(
            'New shop order received: %s — %s — NGN %s',
            $order->reference,
            $order->customer_name,
            number_format((float) $order->total, 0),
        );
        $taskNotes = sprintf(
            "Customer: %s\nEmail: %s\nPhone: %s\nDelivery: %s, %s\nItems: %s",
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone ?? 'N/A',
            $order->shipping_city,
            $order->shipping_state,
            $order->items->map(fn ($i) => $i->quantity . '× ' . $i->product_name)->implode(', '),
        );

        $this->dispatch($recipients, $order, $title, $message, $actionUrl, $taskText, $taskNotes);
    }

    public function notifyQuoteRequest(Order $order): void
    {
        $recipients = $this->recipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $title   = '📋 New Quote Request: ' . $order->job_order_number;
        $message = sprintf(
            '%s requested a quote for %s — Qty %s%s.',
            $order->customer_name,
            $order->job_type ?? 'a job',
            number_format((int) $order->quantity),
            $order->quote_budget ? ' (Budget: NGN ' . number_format((float) $order->quote_budget, 0) . ')' : '',
        );
        $actionUrl = route('admin.orders.show', $order);
        $taskText  = sprintf(
            'New quote request: %s — %s — %s',
            $order->job_order_number,
            $order->customer_name,
            $order->job_type ?? 'Custom Job',
        );
        $taskNotes = sprintf(
            "Customer: %s\nEmail: %s\nPhone: %s\nJob Type: %s\nQty: %s\nBudget: %s\nDelivery: %s",
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone ?? 'N/A',
            $order->job_type ?? 'N/A',
            number_format((int) $order->quantity),
            $order->quote_budget ? 'NGN ' . number_format((float) $order->quote_budget, 0) : 'Not specified',
            $order->delivery_city ?? 'N/A',
        );

        $this->dispatch($recipients, $order, $title, $message, $actionUrl, $taskText, $taskNotes, orderId: $order->id);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, User>  $recipients
     */
    private function dispatch(
        \Illuminate\Database\Eloquent\Collection $recipients,
        ShopOrder|Order $order,
        string $title,
        string $message,
        string $actionUrl,
        string $taskText,
        string $taskNotes,
        ?int $orderId = null,
    ): void {
        // ── 1. In-app dashboard notification (bell + notifications panel) ──
        try {
            Notification::send($recipients, new AdminBroadcastNotification(
                broadcastId: (string) Str::uuid(),
                title:       $title,
                message:     $message,
                type:        'warning',
                actionUrl:   $actionUrl,
            ));
        } catch (\Throwable $e) {
            Log::error('OrderAlertService: dashboard notification failed.', [
                'title'   => $title,
                'message' => $e->getMessage(),
            ]);
        }

        foreach ($recipients as $recipient) {
            // ── 2. Email alert ──
            try {
                Mail::to((string) $recipient->email)->send(new OrderAlertMail($recipient, $order));
            } catch (\Throwable $e) {
                Log::error('OrderAlertService: email failed.', [
                    'recipient_id' => $recipient->id,
                    'title'        => $title,
                    'message'      => $e->getMessage(),
                ]);
            }

            // ── 3. Dashboard task (DailyTodo) ──
            try {
                DailyTodo::create([
                    'user_id'        => $recipient->id,
                    'assigned_by_id' => null,
                    'order_id'       => $orderId,
                    'task'           => $taskText,
                    'notes'          => $taskNotes,
                    'priority'       => 1,   // urgent
                    'due_date'       => today(),
                    'status'         => 'pending',
                ]);
            } catch (\Throwable $e) {
                Log::error('OrderAlertService: task creation failed.', [
                    'recipient_id' => $recipient->id,
                    'title'        => $title,
                    'message'      => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    private function recipients(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->whereIn('role', self::ALERT_ROLES)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();
    }
}
