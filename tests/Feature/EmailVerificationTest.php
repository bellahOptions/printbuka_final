<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_sends_verification_email_and_does_not_authenticate_user(): void
    {
        Notification::fake();

        $response = $this->post(route('register.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08000000000',
            'companyName' => 'Acme Inc',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::query()->where('email', 'john@example.com')->firstOrFail();

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_login_until_email_is_verified(): void
    {
        $customer = User::factory()->unverified()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => 'customer@example.com',
            'password' => 'Password123',
        ]);

        $admin = User::factory()->unverified()->create([
            'role' => 'admin',
            'is_active' => true,
            'department' => 'Management',
            'email' => 'admin@example.com',
            'password' => 'Password123',
        ]);

        $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'Password123',
        ])->assertRedirect(route('verification.notice', ['email' => $customer->email]));

        $this->assertGuest();

        $this->post(route('staff.login.store'), [
            'email' => $admin->email,
            'password' => 'Password123',
        ])->assertRedirect(route('verification.notice', ['email' => $admin->email]));

        $this->assertGuest();
    }

    public function test_user_can_verify_email_from_signed_link_without_being_logged_in(): void
    {
        $user = User::factory()->unverified()->create([
            'is_active' => true,
            'role' => 'customer',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ],
            absolute: false
        );

        $this->get($verificationUrl)->assertRedirect(route('login'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_guest_can_resend_verification_link_using_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'is_active' => true,
            'role' => 'customer',
            'email' => 'resend@example.com',
        ]);

        $this->post(route('verification.send'), [
            'email' => $user->email,
        ])->assertSessionHasNoErrors()->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_authenticated_session_is_logged_out_from_protected_routes(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
