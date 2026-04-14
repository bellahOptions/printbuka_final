<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Support\NotificationAudience;
use Livewire\Component;

class Notifications extends Component
{
    public function markAsRead(int $notificationId): void
    {
        $notification = NotificationAudience::visibleQuery(auth()->user())
            ->whereKey($notificationId)
            ->first();

        if ($notification) {
            NotificationAudience::markAsRead($notification, auth()->user(), request());
        }
    }

    public function markAllAsRead(): void
    {
        NotificationAudience::markAllAsRead(
            NotificationAudience::unreadQuery(auth()->user(), request()),
            auth()->user(),
            request()
        );
    }

    public function render()
    {
        $settings = SiteSetting::query()
            ->whereIn('key', ['notification_message', 'announcement'])
            ->pluck('value', 'key');

        $notifications = NotificationAudience::unreadQuery(auth()->user(), request())
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin.notifications', [
            'settings' => $settings,
            'notifications' => $notifications,
            'unreadCount' => NotificationAudience::unreadQuery(auth()->user(), request())->count(),
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }
}
