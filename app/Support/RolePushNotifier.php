<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\StaffPushNotification;
use Illuminate\Support\Facades\Log;

/**
 * Sends a StaffPushNotification (bell + push) to every active staff member
 * who holds a given admin permission (or the '*' wildcard) — the same
 * recipient-resolution pattern used ad hoc in JobWorkflowNotificationService,
 * pulled out so Finance and Attendance triggers don't duplicate it.
 */
class RolePushNotifier
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function send(string $permission, string $title, string $body, string $type, array $data = [], ?int $excludeUserId = null): void
    {
        $recipients = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->when($excludeUserId, fn ($query) => $query->where('id', '!=', $excludeUserId))
            ->get()
            ->filter(fn (User $user): bool => $user->canAdmin($permission) || $user->canAdmin('*'));

        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new StaffPushNotification(
                    title: $title,
                    body: $body,
                    type: $type,
                    data: $data,
                ));
            } catch (\Throwable $exception) {
                Log::error('Role-based push notification failed.', [
                    'permission' => $permission,
                    'recipient_id' => $recipient->id,
                    'type' => $type,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
