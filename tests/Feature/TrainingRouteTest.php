<?php

namespace Tests\Feature;

use App\Mail\TrainingApplicationConfirmationMail;
use App\Mail\TrainingApplicationSubmittedMail;
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
            'motivation' => 'I want to build practical design skills for client work.',
        ]);

        $response->assertRedirect(route('training.apply'));
        $response->assertSessionHas('closed');

        $this->assertDatabaseMissing('trainings', [
            'email' => 'ada@example.com',
        ]);

        Mail::assertNothingSent();
    }
}
