<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Generic staff push notification — persistent (database) + real-time (Web Push).
 *
 * Usage:
 *   $user->notify(new StaffPushNotification(
 *       title: 'New Task Assigned',
 *       body:  'Design mockup for Order #1042',
 *       type:  'task_assigned',
 *       data:  ['task_id' => 5, 'priority' => 'high'],
 *   ));
 */
class StaffPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly string $type = 'general',
        public readonly array $data = [],
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Only add WebPush if this user has subscribed a browser tab
        if ($notifiable->webPushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    /**
     * Stored in the `notifications` table — fetched by the mobile app and by
     * the web notification bell. `body`/`message` are kept as duplicate keys
     * since the mobile app reads `body` while the bell UI reads `message`.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'      => $this->title,
            'body'       => $this->body,
            'message'    => $this->body,
            'type'       => $this->type,
            'action_url' => $this->data['action_url'] ?? null,
            'data'       => $this->data,
        ];
    }

    /**
     * Delivered to any browser tab that subscribed via the Push API — shown
     * as an OS-level notification by staff-sw.js even if no tab is open.
     */
    public function toWebPush(object $_notifiable, object $_notification): WebPushMessage
    {
        return (new WebPushMessage())
            ->title($this->title)
            ->body($this->body)
            ->icon('/android-icon-192x192.png')
            ->tag($this->type)
            ->data([
                'type' => $this->type,
                'action_url' => $this->data['action_url'] ?? null,
            ]);
    }
}
