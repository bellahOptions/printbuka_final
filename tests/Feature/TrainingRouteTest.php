<?php

namespace Tests\Feature;

use App\Mail\TrainingApplicationConfirmationMail;
use App\Mail\TrainingApplicationDecisionMail;
use App\Mail\TrainingApplicationSubmittedMail;
use App\Models\Training;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TrainingRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_training_page_renders(): void
    {
        $this->get(route('training'))
            ->assertOk()
            ->assertSeeText('Start a career in print, design, and production.');
    }

    public function test_training_registration_page_renders(): void
    {
        Carbon::setTestNow('2026-05-14 12:00:00');

        $this->get(route('training.apply'))
            ->assertOk()
            ->assertSeeText('Apply for the next PGTP cohort.')
            ->assertSeeText('Registration form');
    }

    public function test_training_application_can_be_submitted(): void
    {
        Carbon::setTestNow('2026-05-14 12:00:00');
        Mail::fake();

        $response = $this->post(route('training.store'), [
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
            'has_laptop' => '1',
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
            'referral_source' => 'Instagram',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('trainings', [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'email' => 'ada@example.com',
            'desired_skill' => 'Graphic Design',
        ]);

        Mail::assertSent(TrainingApplicationConfirmationMail::class, function (TrainingApplicationConfirmationMail $mail): bool {
            return $mail->hasTo('ada@example.com');
        });

        Mail::assertSent(TrainingApplicationSubmittedMail::class, 2);
        Mail::assertSent(TrainingApplicationSubmittedMail::class, function (TrainingApplicationSubmittedMail $mail): bool {
            return $mail->hasTo('ahmed@printbuka.com.ng');
        });
        Mail::assertSent(TrainingApplicationSubmittedMail::class, function (TrainingApplicationSubmittedMail $mail): bool {
            return $mail->hasTo('tundealeiloart@gmail.com');
        });
    }

    public function test_duplicate_training_email_is_rejected(): void
    {
        Carbon::setTestNow('2026-05-14 12:00:00');
        Mail::fake();

        Training::create([
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'ada@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'has_laptop' => true,
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response = $this->post(route('training.store'), [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'ADA@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'has_laptop' => '1',
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(1, Training::query()->count());
        Mail::assertNothingSent();
    }

    public function test_duplicate_training_cleanup_keeps_first_submission(): void
    {
        $first = Training::create($this->applicationPayload([
            'email' => 'ada@example.com',
        ]));
        $duplicate = Training::create($this->applicationPayload([
            'first_name' => 'Duplicate',
            'email' => 'ADA@example.com',
        ]));

        $this->artisan('trainings:prune-duplicate-applications')
            ->expectsOutput('Duplicate training applications deleted: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('trainings', ['id' => $first->id]);
        $this->assertDatabaseMissing('trainings', ['id' => $duplicate->id]);
    }

    public function test_training_application_requires_social_link(): void
    {
        Carbon::setTestNow('2026-05-14 12:00:00');
        Mail::fake();

        $response = $this->post(route('training.store'), [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'ada@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'has_laptop' => '1',
            'availability' => 'Weekdays',
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response->assertSessionHasErrors('portfolio_url');
        $this->assertDatabaseMissing('trainings', ['email' => 'ada@example.com']);
        Mail::assertNothingSent();
    }

    public function test_employed_training_applicant_is_automatically_rejected_and_notified(): void
    {
        Carbon::setTestNow('2026-05-14 12:00:00');
        Mail::fake();

        $response = $this->post(route('training.store'), [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'employed@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'employment_status' => 'Employed',
            'has_laptop' => '1',
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Thank you for your interest in PGTP. This offer is only open to unemployed persons, so your application cannot proceed for this cohort.');

        $this->assertDatabaseHas('trainings', [
            'email' => 'employed@example.com',
            'employment_status' => 'Employed',
            'status' => Training::STATUS_REJECTED,
            'decision_note' => 'Thank you for your interest in PGTP. This offer is only open to unemployed persons, so your application cannot proceed for this cohort.',
        ]);

        Mail::assertSent(TrainingApplicationDecisionMail::class, function (TrainingApplicationDecisionMail $mail): bool {
            return $mail->hasTo('employed@example.com')
                && $mail->application->status === Training::STATUS_REJECTED
                && str_contains((string) $mail->application->decision_note, 'only open to unemployed persons');
        });
        Mail::assertNotSent(TrainingApplicationConfirmationMail::class);
        Mail::assertNotSent(TrainingApplicationSubmittedMail::class);
    }

    public function test_training_registration_closes_after_deadline(): void
    {
        Carbon::setTestNow('2026-05-30 00:00:00');
        Mail::fake();

        $this->get(route('training.apply'))
            ->assertOk()
            ->assertSeeText('PGTP registration has closed.')
            ->assertSeeText('This PGTP application window has ended.')
            ->assertDontSeeText('Submit Application');

        $response = $this->post(route('training.store'), [
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'ada@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'has_laptop' => '1',
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response->assertRedirect(route('training.apply'));
        $response->assertSessionHas('closed');

        $this->assertDatabaseMissing('trainings', [
            'email' => 'ada@example.com',
        ]);

        Mail::assertNothingSent();
    }

    private function applicationPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Ada',
            'last_name' => 'Okafor',
            'date_of_birth' => '2000-05-14',
            'phone_whatsapp' => '08030000000',
            'email' => 'ada@example.com',
            'contact_address' => '12 Sample Street',
            'city_state' => 'Lagos, Lagos State',
            'educational_qualification' => 'HND',
            'desired_skill' => 'Graphic Design',
            'has_laptop' => true,
            'availability' => 'Weekdays',
            'portfolio_url' => 'https://example.com/ada',
            'motivation' => 'I want to build practical design skills for client work.',
        ], $overrides);
    }
}
