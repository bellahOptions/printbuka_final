<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class StaffNotificationController extends Controller
{
    /**
     * Paginated notification inbox — newest first.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(25);

        return response()->json([
            'data' => $notifications->map(fn (DatabaseNotification $n) => [
                'id'         => $n->id,
                'title'      => $n->data['title'] ?? '',
                'body'       => $n->data['body'] ?? '',
                'type'       => $n->data['type'] ?? 'general',
                'data'       => $n->data['data'] ?? [],
                'read'       => ! is_null($n->read_at),
                'created_at' => $n->created_at->toISOString(),
            ]),
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'next_page_url'=> $notifications->nextPageUrl(),
        ]);
    }

    /**
     * Number of unread notifications — used for the badge count on the app icon.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['message' => 'Marked as read.']);
    }

    /**
     * Mark all notifications as read in one call.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Hard-delete a single notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()
            ->notifications()
            ->findOrFail($id)
            ->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    /**
     * Hard-delete all read notifications — useful for keeping the inbox clean.
     */
    public function clearRead(Request $request): JsonResponse
    {
        $request->user()->readNotifications()->delete();

        return response()->json(['message' => 'Read notifications cleared.']);
    }
}
