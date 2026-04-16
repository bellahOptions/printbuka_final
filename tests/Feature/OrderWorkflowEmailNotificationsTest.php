<?php

namespace Tests\Feature;

use App\Mail\JobAssignedDesignerMail;
use App\Mail\JobPhaseRoleAlertMail;
use App\Mail\JobStatusAdvancedCustomerMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderWorkflowEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_and_assignment_updates_send_expected_emails(): void
    {
        Mail::fake();

        $workflowAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $designer = User::factory()->create([
            'role' => 'designer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'designer@example.com',
        ]);

        $order = Order::query()->create([
            'service_type' => 'print',
            'quantity' => 20,
            'unit_price' => 500,
            'total_price' => 10000,
            'customer_name' => 'Client Example',
            'customer_email' => 'client@example.com',
            'customer_phone' => '08022223333',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-XYZ123',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $this->actingAs($workflowAdmin)
            ->put(route('admin.orders.update', $order), [
                'assigned_designer_id' => $designer->id,
                'status' => 'Design / Artwork Preparation',
                'internal_notes' => 'Please start immediately.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        Mail::assertSent(JobAssignedDesignerMail::class, function (JobAssignedDesignerMail $mail): bool {
            return $mail->hasTo('designer@example.com');
        });

        Mail::assertSent(JobStatusAdvancedCustomerMail::class, function (JobStatusAdvancedCustomerMail $mail): bool {
            return $mail->hasTo('client@example.com');
        });

        Mail::assertSent(JobPhaseRoleAlertMail::class, function (JobPhaseRoleAlertMail $mail): bool {
            return $mail->hasTo('designer@example.com');
        });
    }
}
