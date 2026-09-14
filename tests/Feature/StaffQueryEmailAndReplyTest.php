<?php

namespace Tests\Feature;

use App\Mail\StaffQueryClosedMail;
use App\Mail\StaffQueryIssuedMail;
use App\Models\StaffProfile;
use App\Models\StaffQuery;
use App\Models\StaffQueryComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StaffQueryEmailAndReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_issuing_a_query_emails_staff_with_a_link_straight_to_the_query(): void
    {
        Mail::fake();

        $hr = $this->adminUser('hr', 'hr@example.com');
        $staff = $this->adminUser('designer', 'designer@example.com');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.store'), [
                'staff_id' => $staff->id,
                'query_date' => now()->toDateString(),
                'query_type' => 'written_warning',
                'subject' => 'Late attendance',
                'description' => 'You have been late three times this week.',
            ])
            ->assertRedirect();

        $query = StaffQuery::query()->latest('id')->firstOrFail();

        Mail::assertSent(StaffQueryIssuedMail::class, function (StaffQueryIssuedMail $mail) use ($query, $staff) {
            $rendered = $mail->render();

            return $mail->hasTo($staff->email)
                && $mail->query->is($query)
                && str_contains($rendered, route('admin.staff-queries.show', $query));
        });
    }

    public function test_staff_can_reply_more_than_once_instead_of_being_locked_out_after_first_response(): void
    {
        $hr = $this->adminUser('hr', 'hr2@example.com');
        $staff = $this->adminUser('designer', 'designer2@example.com');

        $query = $this->makeQuery($staff, $hr);

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.respond', $query), [
                'staff_response' => 'My first formal response.',
            ])
            ->assertRedirect();

        $query->refresh();
        $this->assertSame('My first formal response.', $query->staff_response);
        $this->assertSame('responded', $query->status);

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.respond', $query), [
                'staff_response' => 'A follow-up reply.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('staff_query_comments', [
            'staff_query_id' => $query->id,
            'user_id' => $staff->id,
            'comment' => 'A follow-up reply.',
            'visible_to_staff' => 1,
        ]);

        $this->assertSame(
            2,
            $query->conversationThread()->count(),
            'Expected the formal response plus the follow-up reply to both appear in the thread.'
        );
    }

    public function test_staff_cannot_reply_after_query_is_closed(): void
    {
        $hr = $this->adminUser('hr', 'hr3@example.com');
        $staff = $this->adminUser('designer', 'designer3@example.com');

        $query = $this->makeQuery($staff, $hr);
        $query->forceFill(['status' => 'closed'])->save();

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.respond', $query), [
                'staff_response' => 'Too late.',
            ])
            ->assertStatus(422);
    }

    public function test_closing_a_query_emails_the_full_thread_to_staff_issuer_and_cc(): void
    {
        Mail::fake();

        $hr = $this->adminUser('hr', 'hr4@example.com');
        $staff = $this->adminUser('designer', 'designer4@example.com');

        $query = $this->makeQuery($staff, $hr, ccEmails: 'auditor@example.com');
        $query->forceFill([
            'staff_response' => 'Here is my explanation.',
            'staff_responded_at' => now(),
            'status' => 'responded',
        ])->save();

        StaffQueryComment::query()->create([
            'staff_query_id' => $query->id,
            'user_id' => $hr->id,
            'comment' => 'Thanks, closing this out.',
            'visible_to_staff' => true,
        ]);

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.close', $query), [
                'resolution_notes' => 'No further action required.',
            ])
            ->assertRedirect();

        Mail::assertSent(StaffQueryClosedMail::class, function (StaffQueryClosedMail $mail) use ($query, $staff, $hr) {
            $rendered = $mail->render();

            return $mail->hasTo($staff->email)
                && $mail->hasCc($hr->email)
                && $mail->hasCc('auditor@example.com')
                && str_contains($rendered, 'Here is my explanation.')
                && str_contains($rendered, 'Thanks, closing this out.')
                && str_contains($rendered, 'No further action required.');
        });
    }

    private function makeQuery(User $staff, User $hr, ?string $ccEmails = null): StaffQuery
    {
        return StaffQuery::query()->create([
            'staff_id' => $staff->id,
            'issued_by_id' => $hr->id,
            'query_number' => 'QRY-'.now()->year.'-'.random_int(1000, 9999),
            'query_date' => now()->toDateString(),
            'query_type' => 'written_warning',
            'subject' => 'Late attendance',
            'description' => 'You have been late three times this week.',
            'cc_emails' => $ccEmails,
            'status' => 'pending',
        ]);
    }

    private function adminUser(string $role, string $email): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'email' => $email,
        ]);

        if (! in_array($role, ['super_admin', 'managing_director', 'hr'], true)) {
            StaffProfile::query()->create([
                'user_id' => $user->id,
                'kyc_status' => 'approved',
            ]);
        }

        return $user;
    }
}
