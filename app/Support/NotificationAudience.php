<?php

namespace App\Support;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationAudience
{
    public static function displayFormats(): array
    {
        return [
            'alert' => 'Alert',
            'toast' => 'Toast',
            'modal' => 'Modal',
            'flash' => 'Flash Banner',
            'marquee' => 'Marquee',
        ];
    }

    public static function types(): array
    {
        return [
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'urgent' => 'Urgent',
        ];
    }

    public static function visibleQuery(?User $user = null): Builder
    {
        return AppNotification::query()
            ->where(function ($query) use ($user): void {
                $query->where('audience', 'public');

                if ($user?->hasAdminAccess()) {
                    $query->orWhere('audience', 'staff')
                        ->orWhere('audience', 'admins')
                        ->orWhere('audience', 'all')
                        ->orWhere('audience', 'users')
                        ->orWhere('user_id', $user->id)
                        ->orWhere(function ($roleQuery) use ($user): void {
                            $roleQuery->where('audience', 'role')->where('role', $user->role);
                        });
                } elseif ($user) {
                    $query->orWhere('audience', 'users')
                        ->orWhere('audience', 'all')
                        ->orWhere('user_id', $user->id);
                } else {
                    $query->orWhere('audience', 'all');
                }
            })
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public static function unreadQuery(?User $user, Request $request): Builder
    {
        $query = self::visibleQuery($user);

        if (! self::readTrackingAvailable()) {
            return $query->whereNull('read_at');
        }

        $readerKey = self::readerKey($user, $request);

        return $query->whereDoesntHave('reads', function ($readQuery) use ($readerKey): void {
            $readQuery->where('reader_key', $readerKey);
        });
    }

    public static function markAsRead(AppNotification $notification, ?User $user, Request $request): void
    {
        if (! self::readTrackingAvailable()) {
            $notification->update(['read_at' => now()]);

            return;
        }

        AppNotificationRead::query()->updateOrCreate(
            [
                'app_notification_id' => $notification->id,
                'reader_key' => self::readerKey($user, $request),
            ],
            [
                'user_id' => $user?->id,
                'read_at' => now(),
            ]
        );
    }

    public static function markAllAsRead(Builder $query, ?User $user, Request $request): void
    {
        (clone $query)
            ->select('id')
            ->get()
            ->each(fn (AppNotification $notification) => self::markAsRead($notification, $user, $request));
    }

    public static function readerKey(?User $user, Request $request): string
    {
        if ($user) {
            return 'user:'.$user->id;
        }

        return 'session:'.$request->session()->getId();
    }

    public static function readTrackingAvailable(): bool
    {
        return Schema::hasTable('app_notification_reads');
    }
}
