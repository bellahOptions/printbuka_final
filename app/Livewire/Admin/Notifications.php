<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Notifications\AdminBroadcastNotification;
use Livewire\Component;

class Notifications extends Component
{
    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class)
            ->whereKey($notificationId)
            ->first();

        $notification?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class)
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $settings = SiteSetting::query()
            ->whereIn('key', ['notification_message', 'announcement'])
            ->pluck('value', 'key');

        $notifications = auth()->user()
            ->unreadNotifications()
            ->where('type', AdminBroadcastNotification::class)
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin.notifications', [
            'settings' => $settings,
            'notifications' => $notifications,
            'unreadCount' => auth()->user()->unreadNotifications()->where('type', AdminBroadcastNotification::class)->count(),
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }
}
