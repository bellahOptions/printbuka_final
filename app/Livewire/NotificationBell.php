<?php

namespace App\Livewire;

use App\Support\NotificationAudience;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(int $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! Schema::hasTable('app_notifications')) {
            return;
        }

        $notification = NotificationAudience::visibleQuery(auth()->user())
            ->whereKey($notificationId)
            ->first();

        if ($notification) {
            NotificationAudience::markAsRead($notification, auth()->user(), request());
        }
    }

    public function markAllAsRead(): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! Schema::hasTable('app_notifications')) {
            return;
        }

        NotificationAudience::markAllAsRead(
            NotificationAudience::unreadQuery(auth()->user(), request()),
            auth()->user(),
            request()
        );
    }

    public function render()
    {
        if (! auth()->check()) {
            return view('livewire.notification-bell', [
                'notifications' => collect(),
                'surfaceNotifications' => collect(),
                'count' => 0,
            ]);
        }

        if (! Schema::hasTable('app_notifications')) {
            return view('livewire.notification-bell', [
                'notifications' => collect(),
                'surfaceNotifications' => collect(),
                'count' => 0,
            ]);
        }

        $query = NotificationAudience::unreadQuery(auth()->user(), request());
        $notifications = (clone $query)->latest()->limit(8)->get();
        $surfaceNotifications = (clone $query)->latest()->limit(8)->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'surfaceNotifications' => $surfaceNotifications,
            'count' => (clone $query)->count(),
        ]);
    }
}
