<?php

namespace Tests\Feature;

use App\Mail\TrainingApplicationDecisionMail;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminTrainingApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_can_manage_training_applications(): void
    {
        $hr = $this->staff('hr');
        $application = $this->application();

        $this->actingAs($hr)
            ->get(route('admin.training.index'))
            ->assertOk()
            ->assertSeeText('Training submissions.')
            ->assertSeeText($application->fullName());

        $this->actingAs($hr)
            ->get(route('admin.training.show', $application))
            ->assertOk()
            ->assertSeeText($application->email)
            ->assertSeeText('Accept Applicant');
    }

    public function test_applicant_can_be_accepted_and_notified(): void
    {
        Mail::fake();

        $hr = $this->staff('hr');
        $application = $this->application();

        $this->actingAs($hr)
            ->patch(route('admin.training.decide', $application), [
                'status' => Training::STATUS_ACCEPTED,
                'decision_note' => 'Welcome to the cohort.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('trainings', [
            'id' => $application->id,
            'status' => Training::STATUS_ACCEPTED,
            'decision_note' => 'Welcome to the cohort.',
            'decided_by_id' => $hr->id,
        ]);

        Mail::assertSent(TrainingApplicationDecisionMail::class, function (TrainingApplicationDecisionMail $mail) use ($application): bool {
            return $mail->hasTo($application->email)
                && $mail->application->status === Training::STATUS_ACCEPTED;
        });
    }

    public function test_applicant_can_be_rejected_and_notified(): void
    {
        Mail::fake();

        $hr = $this->staff('hr');
        $application = $this->application();

        $this->actingAs($hr)
            ->patch(route('admin.training.decide', $application), [
                'status' => Training::STATUS_REJECTED,
                'decision_note' => 'Please apply again in a future cohort.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('trainings', [
            'id' => $application->id,
            'status' => Training::STATUS_REJECTED,
        ]);

        Mail::assertSent(TrainingApplicationDecisionMail::class, function (TrainingApplicationDecisionMail $mail) use ($application): bool {
            return $mail->hasTo($application->email)
                && $mail->application->status === Training::STATUS_REJECTED;
        });
    }

    public function test_unauthorized_staff_cannot_manage_training_applications(): void
    {
        $designer = $this->staff('designer');
        $application = $this->application();

        $this->actingAs($designer)
            ->get(route('admin.training.index'))
            ->assertForbidden();

        $this->actingAs($designer)
            ->patch(route('admin.training.decide', $application), [
                'status' => Training::STATUS_ACCEPTED,
            ])
            ->assertForbidden();
    }

    public function test_decided_application_cannot_be_decided_again(): void
    {
        Mail::fake();

        $hr = $this->staff('hr');
        $application = $this->application([
            'status' => Training::STATUS_ACCEPTED,
            'decision_note' => 'Already accepted.',
            'decided_at' => now(),
            'decided_by_id' => $hr->id,
        ]);

        $this->actingAs($hr)
            ->get(route('admin.training.show', $application))
            ->assertOk()
            ->assertSeeText('Decision locked')
            ->assertDontSeeText('Accept Applicant')
            ->assertDontSeeText('Reject Applicant');

        $this->actingAs($hr)
            ->patch(route('admin.training.decide', $application), [
                'status' => Training::STATUS_REJECTED,
                'decision_note' => 'Changing decision.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('trainings', [
            'id' => $application->id,
            'status' => Training::STATUS_ACCEPTED,
            'decision_note' => 'Already accepted.',
        ]);

        Mail::assertNothingSent();
    }

    public function test_training_applications_are_ordered_by_status(): void
    {
        $hr = $this->staff('hr');
        $accepted = $this->application(['first_name' => 'Accepted', 'email' => 'accepted@example.com', 'status' => Training::STATUS_ACCEPTED]);
        $rejected = $this->application(['first_name' => 'Rejected', 'email' => 'rejected@example.com', 'status' => Training::STATUS_REJECTED]);
        $pending = $this->application(['first_name' => 'Pending', 'email' => 'pending@example.com', 'status' => Training::STATUS_PENDING]);

        $this->actingAs($hr)
            ->get(route('admin.training.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $pending->fullName(),
                $accepted->fullName(),
                $rejected->fullName(),
            ]);
    }

    private function staff(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function application(array $overrides = []): Training
    {
        return Training::create(array_merge([
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'gender' => 'Female',
            'phone_whatsapp' => '08030000000',
            'email' => 'ada@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'employment_status' => 'Fresh graduate',
            'experience_level' => 'Beginner',
            'has_laptop' => true,
            'availability' => 'Weekdays',
            'motivation' => 'I want to build practical design skills for client work.',
            'status' => Training::STATUS_PENDING,
        ], $overrides));
    }
}
