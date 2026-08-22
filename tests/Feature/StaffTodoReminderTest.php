<?php

namespace Tests\Feature;

use App\Mail\PendingJobsReminderMail;
use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StaffTodoReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_todos_include_paid_pending_jobs_for_assigned_staff(): void
    {
        Mail::fake();

        // 'designer' role slug no longer exists in config/printbuka_admin.php
        // (roles were consolidated); 'personal_assistant' is the current equivalent.
        $superAdmin = $this->staff('super_admin', 'super@example.com');
        $designer = $this->staff('personal_assistant', 'designer@example.com');

        $paidPending = $this->order([
            'assigned_designer_id' => $designer->id,
            'status' => 'Design / Artwork Preparation',
            'payment_status' => 'Invoice Settled (70%)',
        ]);
        $this->order([
            'assigned_designer_id' => $designer->id,
            'status' => 'Design / Artwork Preparation',
            'payment_status' => 'Invoice Issued',
        ]);
        $this->order([
            'assigned_designer_id' => $designer->id,
            'status' => 'Delivered',
            'payment_status' => 'Invoice Settled (100%)',
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.orders.todo-reminders.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'Todo reminder email(s) sent to 1 staff member(s).');

        Mail::assertSent(PendingJobsReminderMail::class, function (PendingJobsReminderMail $mail) use ($designer, $paidPending): bool {
            return $mail->hasTo($designer->email)
                && count($mail->items) === 1
                && $mail->items[0]['order']->is($paidPending);
        });
    }

    public function test_operations_manager_can_send_todo_reminders_but_other_staff_cannot(): void
    {
        Mail::fake();

        // 'designer'/'finance' role slugs no longer exist in
        // config/printbuka_admin.php (roles were consolidated); 'personal_assistant'
        // and 'customer_service' are current-config equivalents.
        $designer = $this->staff('personal_assistant', 'designer@example.com');
        $operationsManager = $this->staff('operations_manager', 'ops@example.com');
        $finance = $this->staff('customer_service', 'finance@example.com');

        $this->order([
            'assigned_designer_id' => $designer->id,
            'status' => 'Design / Artwork Preparation',
            'payment_status' => 'Invoice Settled (100%)',
        ]);

        $this->actingAs($operationsManager)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.orders.todo-reminders.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'Todo reminder email(s) sent to 1 staff member(s).');

        $this->actingAs($finance)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.orders.todo-reminders.send'))
            ->assertForbidden();
    }

    private function staff(string $role, string $email): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'email' => $email,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        if (! in_array($role, ['super_admin', 'managing_director', 'hr'], true)) {
            StaffProfile::query()->create([
                'user_id' => $user->id,
                'kyc_status' => 'approved',
            ]);
        }

        return $user;
    }

    private function order(array $attributes): Order
    {
        return Order::query()->create(array_replace([
            'service_type' => 'print',
            'quantity' => 10,
            'unit_price' => 1000,
            'total_price' => 10000,
            'customer_name' => 'Client Example',
            'customer_email' => 'client@example.com',
            'customer_phone' => '08022223333',
            'job_order_number' => 'JOB-20260514-'.strtoupper(fake()->bothify('??###')),
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
        ], $attributes));
    }
}
