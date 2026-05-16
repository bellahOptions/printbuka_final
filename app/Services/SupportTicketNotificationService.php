<?php

namespace App\Services;

use App\Mail\SupportTicketRaisedAlertMail;
use App\Mail\SupportTicketUnansweredReminderMail;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use App\Support\SiteSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SupportTicketNotificationService
{
    public function notifyTicketRaised(Ticket $ticket, ?int $raisedByUserId = null): int
    {
        $ticket->loadMissing('user');

        $recipients = $this->adminRecipients($raisedByUserId);

        if ($recipients->isEmpty()) {
            return 0;
        }

        Notification::send($recipients, new AdminBroadcastNotification(
            (string) Str::uuid(),
            'New support ticket: '.$ticket->ticket_number,
            sprintf(
                'A %s-priority ticket (%s) was raised by %s. Subject: %s.',
                strtoupper((string) $ticket->priority),
                $ticket->ticket_number,
                $ticket->user?->displayName() ?? 'Unknown user',
                $ticket->subject
            ),
            $ticket->priority === 'urgent' ? 'warning' : 'warning',
            route('admin.support.show', $ticket),
        ));

        $sent = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new SupportTicketRaisedAlertMail($recipient, $ticket));
                $sent++;
            } catch (\Throwable $exception) {
                Log::error('Support ticket raised email failed.', [
                    'ticket_id' => $ticket->id,
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $sent;
    }

    /**
     * @return int Number of emails sent
     */
    public function sendUnansweredReminders(): int
    {
        $thresholdHours = max(1, (int) SiteSettings::get('support_ticket_unanswered_reminder_hours', 24));
        $cooldownHours = max(1, (int) SiteSettings::get('support_ticket_unanswered_reminder_cooldown_hours', 12));
        $staleCutoff = now()->subHours($thresholdHours);
        $cooldownCutoff = now()->subHours($cooldownHours);

        $tickets = Ticket::query()
            ->with('user')
            ->whereIn('status', ['open', 'in_progress'])
            ->where('updated_at', '<=', $staleCutoff)
            ->where(function ($query) use ($cooldownCutoff): void {
                $query
                    ->whereNull('last_unanswered_reminder_at')
                    ->orWhere('last_unanswered_reminder_at', '<=', $cooldownCutoff);
            })
            ->get()
            ->filter(fn (Ticket $ticket): bool => $this->isUnanswered($ticket))
            ->values();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return 0;
        }

        Notification::send($recipients, new AdminBroadcastNotification(
            (string) Str::uuid(),
            'Unanswered support ticket reminder',
            $tickets->count().' support ticket(s) have no staff response beyond '.$thresholdHours.' hour(s).',
            'warning',
            route('admin.support.index'),
        ));

        $sent = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new SupportTicketUnansweredReminderMail($recipient, $tickets, $thresholdHours));
                $sent++;
            } catch (\Throwable $exception) {
                Log::error('Unanswered support ticket reminder failed.', [
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($sent > 0) {
            Ticket::query()
                ->whereKey($tickets->pluck('id')->all())
                ->update(['last_unanswered_reminder_at' => now()]);
        }

        return $sent;
    }

    private function isUnanswered(Ticket $ticket): bool
    {
        $latestReply = $ticket->replies()->latest('id')->first();

        if (! $latestReply) {
            return true;
        }

        return ! (bool) $latestReply->is_staff_reply;
    }

    /**
     * @return Collection<int, User>
     */
    private function adminRecipients(?int $excludeUserId = null): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereNotIn('role', ['customer', 'staff_pending'])
            ->whereNotNull('email')
            ->when($excludeUserId, fn ($query) => $query->whereKeyNot($excludeUserId))
            ->get()
            ->filter(fn (User $user): bool => $user->canAdmin('admin.view') || $user->canAdmin('*'))
            ->values();
    }
}
