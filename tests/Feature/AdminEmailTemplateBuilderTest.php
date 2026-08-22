<?php

namespace Tests\Feature;

use App\Mail\StaffKycReminderMail;
use App\Models\EmailTemplate;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmailTemplateBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    public function test_super_admin_can_view_and_edit_the_template_list(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.email-templates.index'))
            ->assertOk()
            ->assertSee('Staff KYC Reminder');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.email-templates.edit', 'staff.kyc_reminder'))
            ->assertOk();
    }

    public function test_non_super_admin_cannot_access_email_templates(): void
    {
        foreach (['hr', 'managing_director', 'operations_manager'] as $role) {
            $staff = $this->makeStaff($role);

            $this->actingAs($staff)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.email-templates.index'))
                ->assertForbidden();
        }
    }

    public function test_saving_a_template_overrides_the_live_email(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.email-templates.update', 'staff.kyc_reminder'), [
                'subject' => 'Custom subject for {{staff_name}}',
                'intro_blocks' => json_encode([
                    ['id' => '1', 'type' => 'paragraph', 'text' => 'Custom intro for {{staff_name}}.'],
                ]),
                'outro_blocks' => json_encode([
                    ['id' => '2', 'type' => 'paragraph', 'text' => 'Custom outro.'],
                ]),
            ])
            ->assertRedirect(route('admin.email-templates.index'));

        $this->assertDatabaseHas('email_templates', [
            'key' => 'staff.kyc_reminder',
            'subject' => 'Custom subject for {{staff_name}}',
        ]);

        $staff = User::factory()->make(['first_name' => 'Jane', 'last_name' => 'Doe']);
        $mail = new StaffKycReminderMail($staff);

        $this->assertSame('Custom subject for Jane Doe', $mail->envelope()->subject);

        $rendered = $mail->render();
        $this->assertStringContainsString('Custom intro for Jane Doe.', $rendered);
        $this->assertStringContainsString('Custom outro.', $rendered);
    }

    public function test_an_untouched_template_still_renders_its_default_content(): void
    {
        $staff = User::factory()->make(['first_name' => 'Jane', 'last_name' => 'Doe']);
        $mail = new StaffKycReminderMail($staff);

        $this->assertSame(
            'Action Required: Complete Your Staff Bio-Data Form — Printbuka',
            $mail->envelope()->subject
        );

        $rendered = $mail->render();
        $this->assertStringContainsString('Hello Jane Doe,', $rendered);
    }

    public function test_reverting_a_template_removes_the_override(): void
    {
        $admin = $this->makeStaff('super_admin');

        EmailTemplate::query()->create([
            'key' => 'staff.kyc_reminder',
            'name' => 'Staff KYC Reminder',
            'subject' => 'Overridden subject',
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.email-templates.reset', 'staff.kyc_reminder'))
            ->assertRedirect(route('admin.email-templates.index'));

        $this->assertDatabaseMissing('email_templates', ['key' => 'staff.kyc_reminder']);
    }
}
