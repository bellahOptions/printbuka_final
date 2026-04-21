<?php

namespace Tests\Feature;

use App\Mail\AdminDirectCustomerMessageMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminCustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowed_management_roles_can_access_customer_management_page(): void
    {
        User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        foreach (['customer_service', 'hr', 'management'] as $role) {
            $admin = $this->adminUser($role);

            $this->actingAs($admin)
                ->get(route('admin.customers.index'))
                ->assertOk();
        }
    }

    public function test_unauthorized_admin_role_cannot_access_customer_management_page(): void
    {
        $admin = $this->adminUser('designer');

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertForbidden();
    }

    public function test_admin_can_send_direct_message_to_customer_with_sender_and_reply_to_admin_email(): void
    {
        Mail::fake();

        $admin = $this->adminUser('customer_service', 'care.agent@example.com', 'Care', 'Agent');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'client@example.com',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.customers.send-message', $customer), [
                'subject' => 'Order Clarification',
                'message' => 'Hello, please confirm your preferred delivery slot.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(AdminDirectCustomerMessageMail::class, function (AdminDirectCustomerMessageMail $mail) use ($admin, $customer): bool {
            $mail->build();

            $fromAddress = $mail->from[0]['address'] ?? null;
            $replyToAddress = $mail->replyTo[0]['address'] ?? null;

            return $mail->hasTo($customer->email)
                && $fromAddress === $admin->email
                && $replyToAddress === $admin->email;
        });
    }

    public function test_only_super_admin_can_delete_customer_data_and_action_is_logged(): void
    {
        $superAdmin = $this->adminUser('super_admin');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', [
            'id' => $customer->id,
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $superAdmin->id,
            'route_name' => 'admin.customers.destroy',
            'method' => 'DELETE',
            'subject_type' => 'customer',
            'subject_id' => $customer->id,
        ]);
    }

    public function test_non_super_admin_cannot_delete_customer_data(): void
    {
        $admin = $this->adminUser('customer_service');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.customers.destroy', $customer))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
        ]);
    }

    public function test_central_audit_log_page_is_super_admin_only(): void
    {
        $superAdmin = $this->adminUser('super_admin');
        $regularAdmin = $this->adminUser('customer_service');

        $this->actingAs($superAdmin)
            ->get(route('admin.activity-logs.index'))
            ->assertOk();

        $this->actingAs($regularAdmin)
            ->get(route('admin.activity-logs.index'))
            ->assertForbidden();
    }

    private function adminUser(
        string $role,
        ?string $email = null,
        ?string $firstName = null,
        ?string $lastName = null
    ): User {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => $email ?? fake()->unique()->safeEmail(),
            'first_name' => $firstName ?? fake()->firstName(),
            'last_name' => $lastName ?? fake()->lastName(),
        ]);
    }
}
