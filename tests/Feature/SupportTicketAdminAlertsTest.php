<?php

namespace Tests\Feature;

use App\Mail\SupportTicketRaisedAlertMail;
use App\Mail\SupportTicketUnansweredReminderMail;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use App\Services\SupportTicketNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketAdminAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_ticket_creation_notifies_admin_staff_in_system_and_email(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $customerCare = $this->adminUser('customer_service', 'care@example.com');
        $management = $this->adminUser('management', 'management@example.com');
        $it = $this->adminUser('it', 'it@example.com');

        $this->actingAs($customer)
            ->post(route('support.store'), [
                'subject' => 'Need urgent invoice correction',
                'category' => 'billing',
                'priority' => 'high',
                'message' => 'The invoice total appears incorrect and needs immediate correction.',
            ])
            ->assertRedirect();

        $ticket = Ticket::query()->latest('id')->firstOrFail();

        $this->assertTrue(DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->where('data->title', 'New support ticket: '.$ticket->ticket_number)
            ->exists());

        Mail::assertSent(SupportTicketRaisedAlertMail::class, 3);
        Mail::assertSent(SupportTicketRaisedAlertMail::class, fn (SupportTicketRaisedAlertMail $mail): bool => $mail->hasTo($customerCare->email));
        Mail::assertSent(SupportTicketRaisedAlertMail::class, fn (SupportTicketRaisedAlertMail $mail): bool => $mail->hasTo($management->email));
        Mail::assertSent(SupportTicketRaisedAlertMail::class, fn (SupportTicketRaisedAlertMail $mail): bool => $mail->hasTo($it->email));
    }

    public function test_unanswered_ticket_reminder_targets_only_unanswered_stale_tickets_and_respects_cooldown(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $customerCare = $this->adminUser('customer_service', 'care-reminder@example.com');
        $it = $this->adminUser('it', 'it-reminder@example.com');

        $unansweredTicket = Ticket::query()->create([
            'user_id' => $customer->id,
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => 'Order not yet delivered',
            'category' => 'order',
            'priority' => 'normal',
            'message' => 'Please confirm the delivery ETA for my order.',
            'status' => 'open',
        ]);
        $unansweredTicket->forceFill([
            'updated_at' => now()->subHours(30),
            'created_at' => now()->subHours(30),
        ])->save();

        $answeredTicket = Ticket::query()->create([
            'user_id' => $customer->id,
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => 'Need design file update',
            'category' => 'design',
            'priority' => 'normal',
            'message' => 'Please update the design file version attached to my order.',
            'status' => 'open',
        ]);
        $answeredTicket->forceFill([
            'updated_at' => now()->subHours(30),
            'created_at' => now()->subHours(30),
        ])->save();

        TicketReply::query()->create([
            'ticket_id' => $answeredTicket->id,
            'user_id' => $customerCare->id,
            'message' => 'We have received this and started review.',
            'is_staff_reply' => true,
        ]);

        $sent = app(SupportTicketNotificationService::class)->sendUnansweredReminders();

        $this->assertSame(2, $sent);
        Mail::assertSent(SupportTicketUnansweredReminderMail::class, 2);
        Mail::assertSent(SupportTicketUnansweredReminderMail::class, function (SupportTicketUnansweredReminderMail $mail) use ($customerCare, $unansweredTicket): bool {
            return $mail->hasTo($customerCare->email)
                && $mail->tickets->pluck('id')->all() === [$unansweredTicket->id];
        });
        Mail::assertSent(SupportTicketUnansweredReminderMail::class, function (SupportTicketUnansweredReminderMail $mail) use ($it, $unansweredTicket): bool {
            return $mail->hasTo($it->email)
                && $mail->tickets->pluck('id')->all() === [$unansweredTicket->id];
        });

        $this->assertNotNull($unansweredTicket->fresh()->last_unanswered_reminder_at);
        $this->assertNull($answeredTicket->fresh()->last_unanswered_reminder_at);

        $this->assertTrue(DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->where('data->title', 'Unanswered support ticket reminder')
            ->exists());

        $this->assertSame(0, app(SupportTicketNotificationService::class)->sendUnansweredReminders());
    }

    private function adminUser(string $role, string $email): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => $email,
        ]);
    }
}
