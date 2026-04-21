<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSupportTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_internal_support_ticket_and_auto_assign_to_super_admin(): void
    {
        $staff = $this->adminUser('operations', 'ops@example.com');
        $superAdmin = $this->adminUser('super_admin', 'sa@example.com');

        $this->actingAs($staff)
            ->post(route('admin.support.store'), [
                'subject' => 'POS printer not responding',
                'category' => 'technical',
                'priority' => 'high',
                'message' => 'Printer queue is hanging for all job tickets in production.',
            ])
            ->assertRedirect();

        $ticket = Ticket::query()->latest('id')->firstOrFail();

        $this->assertSame($staff->id, $ticket->user_id);
        $this->assertSame($superAdmin->id, $ticket->assigned_to);
        $this->assertSame('open', $ticket->status);
    }

    public function test_super_admin_can_open_staff_ticket_on_admin_support_portal(): void
    {
        $staff = $this->adminUser('operations', 'ops2@example.com');
        $superAdmin = $this->adminUser('super_admin', 'sa2@example.com');

        $ticket = Ticket::query()->create([
            'user_id' => $staff->id,
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject' => 'Catalog sync issue',
            'category' => 'technical',
            'priority' => 'normal',
            'message' => 'Catalog sync fails on product import.',
            'status' => 'open',
            'assigned_to' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.support.show', $ticket))
            ->assertOk()
            ->assertSeeText('Catalog sync issue');
    }

    public function test_customer_cannot_access_admin_support_portal(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('admin.support.index'))
            ->assertForbidden();
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
