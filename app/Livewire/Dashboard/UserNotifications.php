<?php

namespace App\Livewire\Dashboard;

use App\Models\AppNotification;
use App\Models\SiteSetting;
use Livewire\Component;

class UserNotifications extends Component
{
    public function render()
    {
        $user = auth()->user();
        $settings = SiteSetting::query()
            ->whereIn('key', ['notification_message', 'announcement'])
            ->pluck('value', 'key');

        $notifications = AppNotification::query()
            ->where(function ($query) use ($user): void {
                $query->where('audience', 'all')
                    ->orWhere('audience', 'users')
                    ->orWhere('user_id', $user->id);
            })
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.dashboard.user-notifications', [
            'settings' => $settings,
            'notifications' => $notifications,
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }
}
