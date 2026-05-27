<?php

namespace Tests\Feature;

use App\Mail\JobCompletedAppreciationMail;
use App\Mail\JobConclusionSummaryMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderConclusionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_operations_manager_can_conclude_job_and_trigger_client_and_md_emails(): void
    {
        Mail::fake();

        $operationsManager = $this->staff('operations_manager', 'ops.manager@example.com');
        $managingDirector = $this->staff('managing_director', 'md@example.com');
        $order = $this->order([
            'status' => 'Delivery In Progress',
            'actual_delivery_at' => null,
        ]);

        $this->actingAs($operationsManager)
            ->patch(route('admin.orders.conclude', $order))
            ->assertRedirect()
            ->assertSessionHas('status', 'Job concluded successfully. Invoice auto-settled and job locked from further edits.');

        $order->refresh();
        $this->assertTrue((bool) $order->is_concluded);
        $this->assertNotNull($order->concluded_at);
        $this->assertSame($operationsManager->id, $order->concluded_by_id);
        $this->assertSame('Delivered', $order->status);
        $this->assertNotNull($order->actual_delivery_at);

        Mail::assertSent(JobCompletedAppreciationMail::class, function (JobCompletedAppreciationMail $mail) use ($order): bool {
            return $mail->hasTo($order->customer_email);
        });

        Mail::assertSent(JobConclusionSummaryMail::class, function (JobConclusionSummaryMail $mail) use ($managingDirector, $order): bool {
            return $mail->hasTo($managingDirector->email) && $mail->order->is($order);
        });
    }

    public function test_concluded_job_cannot_be_edited_or_moved_forward(): void
    {
        $superAdmin = $this->staff('super_admin', 'super.admin@example.com');
        $order = $this->order([
            'status' => 'Delivery In Progress',
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('admin.orders.conclude', $order))
            ->assertRedirect();

        $order->refresh();
        $this->assertTrue((bool) $order->is_concluded);

        $this->actingAs($superAdmin)
            ->put(route('admin.orders.update', $order), [
                'internal_notes' => 'Attempted post-conclusion edit',
            ])
            ->assertRedirect()
            ->assertSessionHas('warning', 'This job has been concluded and is locked from further edits.');

        $this->actingAs($superAdmin)
            ->post(route('admin.orders.move-forward', $order))
            ->assertRedirect()
            ->assertSessionHas('warning', 'This job has been concluded and is locked from further edits.');
    }

    public function test_only_operations_manager_or_super_admin_can_conclude_job(): void
    {
        $designer = $this->staff('designer', 'designer@example.com');
        $order = $this->order();

        $this->actingAs($designer)
            ->patch(route('admin.orders.conclude', $order))
            ->assertForbidden();
    }

    private function staff(string $role, string $email): User
    {
        return User::factory()->create([
            'role' => $role,
            'email' => $email,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function order(array $attributes = []): Order
    {
        return Order::query()->create(array_replace([
            'service_type' => 'print',
            'quantity' => 5,
            'unit_price' => 1000,
            'total_price' => 5000,
            'customer_name' => 'Client Example',
            'customer_email' => 'client@example.com',
            'customer_phone' => '08011112222',
            'job_order_number' => 'JOB-20260523-'.strtoupper(fake()->bothify('??###')),
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Settled (100%)',
        ], $attributes));
    }
}

