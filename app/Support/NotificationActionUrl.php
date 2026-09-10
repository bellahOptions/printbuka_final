<?php

namespace App\Support;

use App\Models\Order;

/**
 * Notifications created before `action_url` was stored on them (or created
 * by an event that doesn't set one) have no direct link. Derive one from the
 * type-specific payload instead, so every notification in the bell can still
 * be opened rather than only offering "Dismiss".
 */
class NotificationActionUrl
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function resolve(array $data): ?string
    {
        if (filled($data['action_url'] ?? null)) {
            return $data['action_url'];
        }

        $inner = (array) ($data['data'] ?? []);

        if (filled($inner['action_url'] ?? null)) {
            return $inner['action_url'];
        }

        return match ($data['type'] ?? null) {
            'job_assigned', 'job_phase_changed', 'workflow_approval_requested' => self::orderUrl($inner['order_id'] ?? null),
            'shop_order_new' => self::shopOrderUrl($inner['order_id'] ?? null),
            'task_assigned', 'task_reviewed' => route('admin.tasks.index'),
            'staff_query_issued', 'staff_query_responded', 'staff_query_closed' => self::staffQueryUrl($inner['query_id'] ?? null),
            default => null,
        };
    }

    private static function orderUrl(mixed $orderId): ?string
    {
        if (! $orderId) {
            return null;
        }

        // Order's route key is job_order_number, not its id — resolve the
        // model so the link uses the right key instead of a raw numeric id.
        $order = Order::find($orderId);

        return $order ? route('admin.orders.show', $order) : null;
    }

    private static function shopOrderUrl(mixed $orderId): ?string
    {
        return $orderId ? route('admin.shop-orders.show', $orderId) : null;
    }

    private static function staffQueryUrl(mixed $queryId): ?string
    {
        return $queryId ? route('admin.staff-queries.show', $queryId) : null;
    }
}
