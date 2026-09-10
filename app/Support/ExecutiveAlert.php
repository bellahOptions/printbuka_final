<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\StaffPushNotification;
use Illuminate\Support\Facades\Log;

/**
 * Sends a StaffPushNotification (bell + push) to Super Admin and Managing
 * Director specifically — the fixed "executives always want to know about
 * this" audience, distinct from permission-based recipient lists elsewhere.
 */
class ExecutiveAlert
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function send(string $title, string $body, string $type, array $data = [], ?int $excludeUserId = null): void
    {
        $executives = User::query()
            ->whereIn('role', ['super_admin', 'managing_director'])
            ->where('is_active', true)
            ->when($excludeUserId, fn ($query) => $query->where('id', '!=', $excludeUserId))
            ->get();

        foreach ($executives as $executive) {
            try {
                $executive->notify(new StaffPushNotification(
                    title: $title,
                    body: $body,
                    type: $type,
                    data: $data,
                ));
            } catch (\Throwable $exception) {
                Log::error('Executive alert notification failed.', [
                    'recipient_id' => $executive->id,
                    'type' => $type,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
