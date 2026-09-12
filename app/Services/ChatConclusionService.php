<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\Ticket;
use App\Models\User;

class ChatConclusionService
{
    public function __construct(
        private readonly SupportTicketNotificationService $notifications,
    ) {
    }

    public function conclude(ChatSession $session): ?Ticket
    {
        if ($session->ticket_id) {
            return $session->ticket;
        }

        $session->loadMissing('messages');

        if (! $session->user_id || $session->messages->where('sender', 'user')->isEmpty()) {
            $session->update(['status' => 'abandoned', 'ended_at' => now()]);

            return null;
        }

        $ticket = Ticket::create([
            'user_id' => $session->user_id,
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => 'Chatbot conversation — '.$session->contactName(),
            'category' => 'chatbot',
            'priority' => $session->escalation_requested ? 'high' : 'normal',
            'message' => $this->transcript($session),
            'status' => 'open',
            'assigned_to' => $this->resolveAssigneeId(),
        ]);

        $session->update([
            'status' => 'concluded',
            'ended_at' => now(),
            'ticket_id' => $ticket->id,
        ]);

        $this->notifications->notifyTicketRaised($ticket, $session->user_id);

        return $ticket;
    }

    private function transcript(ChatSession $session): string
    {
        return $session->messages
            ->map(fn ($message) => sprintf(
                '[%s] %s: %s',
                $message->created_at?->format('H:i') ?? '',
                match ($message->sender) {
                    'user' => 'Customer',
                    'bot' => 'Bot',
                    default => 'Staff',
                },
                $message->message,
            ))
            ->implode("\n");
    }

    private function resolveAssigneeId(): ?int
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', ['super_admin', 'managing_director'])
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END")
            ->value('id');
    }
}
