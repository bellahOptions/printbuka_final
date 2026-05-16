<?php

namespace App\Livewire;

use App\Notifications\AdminBroadcastNotification;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        $notification = auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class)
            ->whereKey($notificationId)
            ->first();

        $notification?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        if (! auth()->check()) {
            return;
        }

        auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class)
            ->update(['read_at' => now()]);
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

        $query = auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class);
        $notifications = (clone $query)->latest()->limit(8)->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'surfaceNotifications' => $notifications,
            'count' => (clone $query)->count(),
        ]);
    }
}
