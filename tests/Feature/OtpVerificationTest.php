<?php

namespace Tests\Feature;

use App\Mail\OtpCodeMail;
use App\Mail\OtpSystemToggledMail;
use App\Models\SiteSetting;
use App\Models\StaffOtpCode;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role, array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_non_totp_staff_is_redirected_to_otp_challenge(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.otp.challenge'));
    }

    public function test_visiting_challenge_sends_exactly_one_code_and_creates_a_row(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)->get(route('admin.otp.challenge'))->assertOk();

        Mail::assertSent(OtpCodeMail::class, 1);
        $this->assertDatabaseCount('staff_otp_codes', 1);
        $this->assertDatabaseHas('staff_otp_codes', ['user_id' => $staff->id]);
    }

    public function test_correct_code_verifies_and_grants_access(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)->get(route('admin.otp.challenge'));

        $sentCode = null;
        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$sentCode): bool {
            $sentCode = $mail->code;

            return true;
        });

        $this->actingAs($staff)
            ->post(route('admin.otp.verify'), ['code' => $sentCode])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(session('staff_2fa_verified'));
    }

    public function test_wrong_code_is_rejected_and_increments_attempts(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');
        $this->actingAs($staff)->get(route('admin.otp.challenge'));

        $this->actingAs($staff)
            ->post(route('admin.otp.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertFalse((bool) session('staff_2fa_verified'));
        $this->assertSame(1, StaffOtpCode::query()->where('user_id', $staff->id)->value('attempts'));
    }

    public function test_exceeding_max_attempts_locks_the_code_even_for_the_correct_code(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');
        $this->actingAs($staff)->get(route('admin.otp.challenge'));

        $sentCode = null;
        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$sentCode): bool {
            $sentCode = $mail->code;

            return true;
        });

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($staff)->post(route('admin.otp.verify'), ['code' => '000000']);
        }

        $this->actingAs($staff)
            ->post(route('admin.otp.verify'), ['code' => $sentCode])
            ->assertSessionHasErrors('code');
    }

    public function test_expired_code_is_rejected(): void
    {
        Mail::fake();

        $staff = $this->makeStaff('hr');
        $this->actingAs($staff)->get(route('admin.otp.challenge'));

        $sentCode = null;
        Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$sentCode): bool {
            $sentCode = $mail->code;

            return true;
        });

        StaffOtpCode::query()->where('user_id', $staff->id)->update(['expires_at' => now()->subMinute()]);

        $this->actingAs($staff)
            ->post(route('admin.otp.verify'), ['code' => $sentCode])
            ->assertSessionHasErrors('code');
    }

    public function test_totp_confirmed_staff_always_go_to_totp_challenge_regardless_of_toggle(): void
    {
        $staff = $this->makeStaff('hr', ['two_factor_confirmed_at' => now()]);

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.two-factor.challenge'));

        SiteSetting::query()->updateOrCreate(['key' => 'otp_enabled'], ['value' => '0']);
        SiteSettings::clearCache();

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.two-factor.challenge'));
    }

    public function test_disabling_otp_restores_forced_totp_setup(): void
    {
        SiteSetting::query()->updateOrCreate(['key' => 'otp_enabled'], ['value' => '0']);
        SiteSettings::clearCache();

        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.two-factor.setup'));
    }

    public function test_non_super_admin_cannot_toggle_otp(): void
    {
        // managing_director has wildcard admin permissions (can reach the
        // settings page) but is not super_admin, so it's the right role to
        // prove the controller-side role gate, not just the route permission.
        $manager = $this->makeStaff('managing_director', ['two_factor_confirmed_at' => now()]);

        $this->actingAs($manager)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.settings.update'), ['otp_enabled' => '0'])
            ->assertForbidden();

        $this->assertSame('1', SiteSettings::get('otp_enabled'));
    }

    public function test_non_super_admin_saving_unrelated_fields_leaves_otp_enabled_untouched(): void
    {
        $manager = $this->makeStaff('managing_director', ['two_factor_confirmed_at' => now()]);

        $this->actingAs($manager)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.settings.update'), ['site_name' => 'Printbuka Test'])
            ->assertRedirect();

        $this->assertDatabaseMissing('site_settings', ['key' => 'otp_enabled']);
        $this->assertSame('1', SiteSettings::get('otp_enabled'));
    }

    public function test_super_admin_toggling_otp_notifies_self_and_every_other_staff_account(): void
    {
        Mail::fake();

        $superAdmin = $this->makeStaff('super_admin', ['two_factor_confirmed_at' => now()]);
        $otherStaff = $this->makeStaff('hr', ['two_factor_confirmed_at' => now()]);
        $customer = $this->makeStaff('customer', ['two_factor_confirmed_at' => now()]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.settings.update'), ['otp_enabled' => '0'])
            ->assertRedirect();

        $this->assertSame('0', SiteSettings::get('otp_enabled'));

        Mail::assertSent(OtpSystemToggledMail::class, function (OtpSystemToggledMail $mail) use ($superAdmin): bool {
            return $mail->recipient->is($superAdmin) && $mail->isActingAdmin === true;
        });

        Mail::assertSent(OtpSystemToggledMail::class, function (OtpSystemToggledMail $mail) use ($otherStaff): bool {
            return $mail->recipient->is($otherStaff) && $mail->isActingAdmin === false;
        });

        Mail::assertNotSent(OtpSystemToggledMail::class, function (OtpSystemToggledMail $mail) use ($customer): bool {
            return $mail->recipient->is($customer);
        });
    }
}
