<?php

namespace Tests\Feature;

use App\Mail\TaskAssignedMail;
use App\Mail\TaskReviewOutcomeMail;
use App\Models\DailyTodo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DailyTodoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_operations_manager_can_assign_one_task_to_multiple_staff_and_staff_get_emails(): void
    {
        Mail::fake();

        $operationsManager = $this->staff('operations_manager', 'ops.manager@example.com');
        $designer = $this->staff('designer', 'designer.staff@example.com');
        $finance = $this->staff('finance', 'finance.staff@example.com');

        $this->actingAs($operationsManager)
            ->post(route('admin.tasks.store'), [
                'user_ids' => [$designer->id, $finance->id],
                'task' => 'Reconcile order handoff notes',
                'due_date' => today()->toDateString(),
                'notes' => 'Please complete before close of business.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Task assigned to 2 staff member(s).');

        $this->assertDatabaseCount('daily_todos', 2);

        Mail::assertSent(TaskAssignedMail::class, function (TaskAssignedMail $mail) use ($designer): bool {
            return $mail->hasTo($designer->email) && $mail->todo->task === 'Reconcile order handoff notes';
        });

        Mail::assertSent(TaskAssignedMail::class, function (TaskAssignedMail $mail) use ($finance): bool {
            return $mail->hasTo($finance->email) && $mail->todo->task === 'Reconcile order handoff notes';
        });
    }

    public function test_task_cannot_be_reopened_or_marked_done_again_after_completion(): void
    {
        $assignee = $this->staff('designer', 'assignee@example.com');
        $assigner = $this->staff('operations_manager', 'assigner@example.com');

        $todo = DailyTodo::query()->create([
            'user_id' => $assignee->id,
            'assigned_by_id' => $assigner->id,
            'task' => 'Prepare artwork proof',
            'due_date' => today(),
            'status' => 'pending',
        ]);

        $this->actingAs($assignee)
            ->patch(route('admin.tasks.mark-done', $todo))
            ->assertRedirect();

        $todo->refresh();
        $this->assertSame('completed', $todo->status);
        $this->assertNotNull($todo->completed_at);

        $this->actingAs($assignee)
            ->patch(route('admin.tasks.mark-working', $todo))
            ->assertForbidden();

        $this->actingAs($assignee)
            ->patch(route('admin.tasks.mark-done', $todo))
            ->assertForbidden();
    }

    public function test_reviewer_submits_one_time_rating_and_sends_appraisal_or_warning_emails(): void
    {
        Mail::fake();

        $assignee = $this->staff('designer', 'assignee.review@example.com');
        $assigner = $this->staff('operations_manager', 'assigner.review@example.com');
        $reviewer = $this->staff('super_admin', 'reviewer@example.com');

        $highRatedTodo = DailyTodo::query()->create([
            'user_id' => $assignee->id,
            'assigned_by_id' => $assigner->id,
            'task' => 'Finalize print-ready export',
            'due_date' => today(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $warningTodo = DailyTodo::query()->create([
            'user_id' => $assignee->id,
            'assigned_by_id' => $assigner->id,
            'task' => 'Update stock audit sheet',
            'due_date' => today(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->actingAs($reviewer)
            ->patch(route('admin.tasks.approve', $highRatedTodo), [
                'review_rating' => 5,
                'review_comments' => 'Excellent execution and speed.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Task review submitted successfully.');

        $highRatedTodo->refresh();
        $this->assertSame('reviewed', $highRatedTodo->status);
        $this->assertSame(5, $highRatedTodo->review_rating);

        $this->actingAs($reviewer)
            ->patch(route('admin.tasks.approve', $warningTodo), [
                'review_rating' => 1,
                'review_comments' => 'Missed critical checklist items.',
            ])
            ->assertRedirect();

        $warningTodo->refresh();
        $this->assertSame('reviewed', $warningTodo->status);
        $this->assertSame(1, $warningTodo->review_rating);

        Mail::assertSent(TaskReviewOutcomeMail::class, function (TaskReviewOutcomeMail $mail) use ($assignee): bool {
            return $mail->hasTo($assignee->email) && $mail->rating === 5;
        });

        Mail::assertSent(TaskReviewOutcomeMail::class, function (TaskReviewOutcomeMail $mail) use ($assignee): bool {
            return $mail->hasTo($assignee->email) && $mail->rating === 1;
        });

        $this->actingAs($reviewer)
            ->patch(route('admin.tasks.approve', $highRatedTodo), [
                'review_rating' => 4,
            ])
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
}

