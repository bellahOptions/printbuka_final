<?php

namespace Tests\Feature;

use App\Mail\DailyStaffActivitySummaryMail;
use App\Mail\StaffEmploymentStatusMail;
use App\Mail\StaffKycReviewMail;
use App\Mail\StaffQueryIssuedMail;
use App\Mail\StaffSignupAlertMail;
use App\Mail\TaskAssignedMail;
use App\Mail\TaskReviewOutcomeMail;
use App\Models\DailyTodo;
use App\Models\EmailTemplate;
use App\Models\StaffQuery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Lightweight regression check that the drag-and-drop email customizer
 * (App\Mail\Concerns\HasEditableTemplate) is correctly wired into the 7
 * Mailables covered in this pass: with no EmailTemplate row present each
 * mail renders its untouched default content, and with an EmailTemplate
 * row present for its key, the custom intro/outro blocks appear in the
 * rendered output.
 */
class AdminEmailTemplateWiringTest extends TestCase
{
    use RefreshDatabase;

    private function customTemplate(string $key): void
    {
        EmailTemplate::query()->create([
            'key' => $key,
            'name' => $key,
            'intro_blocks' => [
                ['id' => '1', 'type' => 'paragraph', 'text' => 'CUSTOM-INTRO-'.$key],
            ],
            'outro_blocks' => [
                ['id' => '2', 'type' => 'paragraph', 'text' => 'CUSTOM-OUTRO-'.$key],
            ],
        ]);
    }

    public function test_daily_staff_activity_summary_mail_supports_custom_blocks(): void
    {
        $recipient = User::factory()->make(['first_name' => 'Ada', 'last_name' => 'Admin']);
        $summary = ['total' => 0, 'by_staff' => collect(), 'by_route' => collect()];

        $mail = new DailyStaffActivitySummaryMail($recipient, now(), $summary);
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Ada Admin,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.daily_activity_summary');
        $mail = new DailyStaffActivitySummaryMail($recipient, now(), $summary);
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.daily_activity_summary', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.daily_activity_summary', $rendered);
    }

    public function test_staff_employment_status_mail_supports_custom_blocks(): void
    {
        $staff = User::factory()->make(['first_name' => 'Ben', 'last_name' => 'Baker']);

        $mail = new StaffEmploymentStatusMail($staff, 'suspended', 'Policy violation');
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Ben Baker,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.employment_status');
        $mail = new StaffEmploymentStatusMail($staff, 'suspended', 'Policy violation');
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.employment_status', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.employment_status', $rendered);
    }

    public function test_staff_kyc_review_mail_supports_custom_blocks(): void
    {
        $staff = User::factory()->make(['first_name' => 'Cara', 'last_name' => 'Cole']);

        $mail = new StaffKycReviewMail($staff, 'approved', null, 'HR Officer');
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Cara Cole,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.kyc_review');
        $mail = new StaffKycReviewMail($staff, 'approved', null, 'HR Officer');
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.kyc_review', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.kyc_review', $rendered);
    }

    public function test_staff_query_issued_mail_supports_custom_blocks(): void
    {
        $staff = User::factory()->create(['first_name' => 'Dara', 'last_name' => 'Dean']);
        $issuer = User::factory()->create();

        $query = StaffQuery::query()->create([
            'staff_id' => $staff->id,
            'issued_by_id' => $issuer->id,
            'query_number' => 'QRY-2026-0099',
            'query_date' => now(),
            'query_type' => 'written_warning',
            'subject' => 'Late submission',
            'description' => 'Repeated late submissions of job cards.',
            'status' => 'pending',
        ]);
        $query->load('staff');

        $mail = new StaffQueryIssuedMail($query);
        $rendered = $mail->render();
        $this->assertStringContainsString('Dara Dean', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.query_issued');
        $mail = new StaffQueryIssuedMail($query);
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.query_issued', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.query_issued', $rendered);
    }

    public function test_staff_signup_alert_mail_supports_custom_blocks(): void
    {
        $recipient = User::factory()->make(['first_name' => 'Eli', 'last_name' => 'Evans']);
        $staff = User::factory()->make(['first_name' => 'Fay', 'last_name' => 'Ford']);

        $mail = new StaffSignupAlertMail($recipient, $staff);
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Eli Evans,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.signup_alert');
        $mail = new StaffSignupAlertMail($recipient, $staff);
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.signup_alert', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.signup_alert', $rendered);
    }

    public function test_task_assigned_mail_supports_custom_blocks(): void
    {
        $recipient = User::factory()->create(['first_name' => 'Gia', 'last_name' => 'Green']);
        $assigner = User::factory()->create(['first_name' => 'Hal', 'last_name' => 'Hunt']);
        $todo = DailyTodo::query()->create([
            'user_id' => $recipient->id,
            'assigned_by_id' => $assigner->id,
            'task' => 'Prepare artwork proof',
            'due_date' => today(),
            'status' => 'pending',
        ]);

        $mail = new TaskAssignedMail($recipient, $todo, $assigner);
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Gia Green,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.task_assigned');
        $mail = new TaskAssignedMail($recipient, $todo, $assigner);
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.task_assigned', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.task_assigned', $rendered);
    }

    public function test_task_review_outcome_mail_supports_custom_blocks(): void
    {
        $recipient = User::factory()->create(['first_name' => 'Ivy', 'last_name' => 'Irwin']);
        $assigner = User::factory()->create();
        $todo = DailyTodo::query()->create([
            'user_id' => $recipient->id,
            'assigned_by_id' => $assigner->id,
            'task' => 'Finalize print-ready export',
            'due_date' => today(),
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $mail = new TaskReviewOutcomeMail($recipient, $todo, 5);
        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Ivy Irwin,', $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO', $rendered);

        $this->customTemplate('staff.task_review_outcome');
        $mail = new TaskReviewOutcomeMail($recipient, $todo, 5);
        $rendered = $mail->render();
        $this->assertStringContainsString('CUSTOM-INTRO-staff.task_review_outcome', $rendered);
        $this->assertStringContainsString('CUSTOM-OUTRO-staff.task_review_outcome', $rendered);
    }
}
