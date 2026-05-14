<?php

namespace Tests\Feature;

use App\Mail\PendingJobsReminderMail;
use App\Models\Order;
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

        $superAdmin = $this->staff('super_admin', 'super@example.com');
        $designer = $this->staff('designer', 'designer@example.com');

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

        $designer = $this->staff('designer', 'designer@example.com');
        $operationsManager = $this->staff('operations_manager', 'ops@example.com');
        $finance = $this->staff('finance', 'finance@example.com');

        $this->order([
            'assigned_designer_id' => $designer->id,
            'status' => 'Design / Artwork Preparation',
            'payment_status' => 'Invoice Settled (100%)',
        ]);

        $this->actingAs($operationsManager)
            ->post(route('admin.orders.todo-reminders.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'Todo reminder email(s) sent to 1 staff member(s).');

        $this->actingAs($finance)
            ->post(route('admin.orders.todo-reminders.send'))
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
