<?php

namespace Tests\Feature;

use App\Mail\DailyStaffActivitySummaryMail;
use App\Mail\StaffSignupAlertMail;
use App\Models\StaffActivity;
use App\Models\User;
use App\Services\StaffActivitySummaryService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StaffGovernanceAndSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_signup_cannot_self_assign_role_or_department(): void
    {
        Mail::fake();
        Notification::fake();
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'super-admin@example.com',
        ]);

        $this->post(route('staff.register.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => '08030000000',
            'address' => 'Yaba, Lagos',
            'date_of_birth' => '1997-05-02',
            'email' => 'staff-new@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'super_admin',
            'department' => 'Management',
            'requested_role' => 'operations_manager',
            'other_role' => 'Ops Lead',
        ])->assertRedirect(route('staff.login'));

        $staff = User::query()->where('email', 'staff-new@example.com')->firstOrFail();

        $this->assertSame('staff_pending', $staff->role);
        $this->assertNull($staff->department);
        $this->assertNull($staff->requested_role);
        $this->assertNull($staff->other_role);
        $this->assertFalse((bool) $staff->is_active);

        Notification::assertSentTo($staff, VerifyEmail::class);
        Mail::assertSent(StaffSignupAlertMail::class, function (StaffSignupAlertMail $mail) use ($superAdmin, $staff): bool {
            return $mail->hasTo($superAdmin->email)
                && $mail->staff->is($staff);
        });
    }

    public function test_daily_staff_activity_summary_is_sent_to_active_hr_users(): void
    {
        Mail::fake();

        config()->set('app.business_timezone', 'Africa/Lagos');

        $hr = User::factory()->create([
            'role' => 'hr',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'hr@example.com',
        ]);

        $actor = User::factory()->create([
            'role' => 'super_admin',
            'department' => 'IT',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $today = now('Africa/Lagos');
        $firstActivityTime = $today->copy()->setTime(9, 15)->setTimezone('UTC');
        $secondActivityTime = $today->copy()->setTime(14, 45)->setTimezone('UTC');

        StaffActivity::query()->create([
            'user_id' => $actor->id,
            'role' => $actor->role,
            'department' => $actor->department,
            'action' => 'GET admin dashboard',
            'subject_type' => 'order',
            'subject_id' => 1,
            'ip_address' => '127.0.0.1',
            'route_name' => 'admin.dashboard',
            'created_at' => $firstActivityTime,
            'updated_at' => $firstActivityTime,
        ]);

        StaffActivity::query()->create([
            'user_id' => $actor->id,
            'role' => $actor->role,
            'department' => $actor->department,
            'action' => 'PUT admin orders update',
            'subject_type' => 'order',
            'subject_id' => 2,
            'ip_address' => '127.0.0.1',
            'route_name' => 'admin.orders.update',
            'created_at' => $secondActivityTime,
            'updated_at' => $secondActivityTime,
        ]);

        $sent = app(StaffActivitySummaryService::class)->sendDailySummary($today);

        $this->assertSame(1, $sent);

        Mail::assertSent(DailyStaffActivitySummaryMail::class, function (DailyStaffActivitySummaryMail $mail) use ($hr): bool {
            return $mail->hasTo($hr->email)
                && (($mail->summary['total'] ?? 0) === 2);
        });
    }
}
