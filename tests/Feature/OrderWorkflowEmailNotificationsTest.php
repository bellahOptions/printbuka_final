<?php

namespace Tests\Feature;

use App\Mail\JobAssignedDesignerMail;
use App\Mail\JobPhaseRoleAlertMail;
use App\Mail\JobStatusAdvancedCustomerMail;
use App\Models\Order;
use App\Models\User;
use App\Support\LivewireSecureUploads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
            'two_factor_confirmed_at' => now(),
        ]);

        // Note: 'designer' role slug no longer exists in config/printbuka_admin.php
        // (roles were consolidated); 'personal_assistant' is the current role that
        // both (a) is eligible for Order::autoAssignableDesignerId()'s designer pool
        // and (b) holds the 'design.update' permission needed to receive the
        // JobPhaseRoleAlertMail for the Design phase. Also, assigned_designer_id is
        // an immutable field on admin.orders.update (see
        // AdminOrderController::rejectImmutableFieldEdits) — assignment only happens
        // automatically via the receive-brief action, not by passing it to update().
        $designer = User::factory()->create([
            'role' => 'personal_assistant',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'designer@example.com',
        ]);

        // Designer assignment only happens automatically via Order creation
        // (Order::autoAssignableDesignerId(), triggered from
        // AdminOrderController::store()/handleOrderCreated()) — it's an immutable
        // field on admin.orders.update, and receive-brief doesn't fire any
        // notification. So we create the order through the real store endpoint to
        // legitimately exercise the assignment-mail path.
        $this->actingAs($workflowAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.orders.store'), [
                'channel' => 'Manual',
                'job_type' => 'Business Cards',
                'priority' => '🟡 Normal',
                'delivery_preference' => 'pickup',
                'customer_name' => 'Client Example',
                'customer_email' => 'client@example.com',
                'customer_phone' => '08022223333',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();
        $this->assertSame($designer->id, $order->assigned_designer_id);

        Mail::assertSent(JobAssignedDesignerMail::class, function (JobAssignedDesignerMail $mail): bool {
            return $mail->hasTo('designer@example.com');
        });

        // Bump payment status so the phase-1 payment gate doesn't block the move to
        // the Design phase, then advance status to trigger the customer + phase
        // alert emails.
        $order->forceFill(['payment_status' => 'Invoice Settled (70%)'])->save();

        $this->actingAs($workflowAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
                'internal_notes' => 'Please start immediately.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        Mail::assertSent(JobStatusAdvancedCustomerMail::class, function (JobStatusAdvancedCustomerMail $mail): bool {
            return $mail->hasTo('client@example.com');
        });

        Mail::assertSent(JobPhaseRoleAlertMail::class, function (JobPhaseRoleAlertMail $mail): bool {
            return $mail->hasTo('designer@example.com');
        });
    }

    public function test_admin_can_upload_final_design_image_via_secure_livewire_path(): void
    {
        Mail::fake();
        Storage::fake('public');

        $workflowAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
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
            'job_order_number' => 'JOB-20260416-XYZ124',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $designImagePath = 'designs/images/final-artwork.jpg';
        Storage::disk('public')->put($designImagePath, 'final-artwork');

        $this->actingAs($workflowAdmin)
            ->withSession([
                LivewireSecureUploads::SESSION_KEY => [$designImagePath],
                'staff_2fa_verified' => true,
            ])
            ->put(route('admin.orders.update', $order), [
                'design_image_path' => $designImagePath,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame($designImagePath, $order->fresh()->final_design_path);
    }

    public function test_admin_order_update_rejects_tampered_final_design_image_path(): void
    {
        Mail::fake();
        Storage::fake('public');

        $workflowAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
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
            'job_order_number' => 'JOB-20260416-XYZ125',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $tamperedPath = 'designs/images/tampered-artwork.jpg';
        Storage::disk('public')->put($tamperedPath, 'tampered-artwork');

        $this->actingAs($workflowAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), [
                'design_image_path' => $tamperedPath,
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('design_image_path');
    }
}
