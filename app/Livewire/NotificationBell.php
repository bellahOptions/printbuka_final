<?php

namespace App\Livewire;

use App\Notifications\AdminBroadcastNotification;
use App\Notifications\StaffPushNotification;
use Livewire\Component;

class NotificationBell extends Component
{
    /** @var array<int, string> */
    private const VISIBLE_TYPES = [
        AdminBroadcastNotification::class,
        StaffPushNotification::class,
    ];

    /**
     * Unread count as of the last render — persisted across the component's
     * poll round-trips (see wire:poll.15s on the view) via Livewire's normal
     * property hydration. Set once in mount() so the very first render never
     * treats pre-existing unread notifications as "new" and chimes on load.
     */
    public int $previousCount = 0;

    public function mount(): void
    {
        $this->previousCount = $this->unreadCount();
    }

    private function unreadCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return auth()->user()
            ->unreadNotifications()
            ->whereIn('type', self::VISIBLE_TYPES)
            ->count();
    }

    public function markAsReadAndOpen(string $notificationId, string $url): void
    {
        $this->markAsRead($notificationId);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, ['http', 'https'], true)) {
            return;
        }
        $this->redirect($url, navigate: true);
    }

    public function markAsRead(string $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        $notification = auth()->user()
            ->unreadNotifications()
            ->whereIn('type', self::VISIBLE_TYPES)
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
            ->whereIn('type', self::VISIBLE_TYPES)
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        if (! auth()->check()) {
            return view('livewire.notification-bell', [
                'notifications' => collect(),
                'count' => 0,
            ]);
        }

        $query = auth()->user()
            ->unreadNotifications()
            ->whereIn('type', self::VISIBLE_TYPES);

        $count = (clone $query)->count();

        if ($count > $this->previousCount) {
            $this->dispatch('play-notification-sound');
        }

        $this->previousCount = $count;

        return view('livewire.notification-bell', [
            'notifications' => (clone $query)->latest()->limit(8)->get(),
            'count' => $count,
        ]);
    }
}
