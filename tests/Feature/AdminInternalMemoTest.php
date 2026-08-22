<?php

namespace Tests\Feature;

use App\Mail\InternalMemoMail;
use App\Models\InternalMemo;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminInternalMemoTest extends TestCase
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

    private function memoBlocks(): string
    {
        return json_encode([
            ['id' => '1', 'type' => 'heading', 'text' => 'Hello {{staff_name}}'],
            ['id' => '2', 'type' => 'paragraph', 'text' => 'This is a memo.'],
        ]);
    }

    public function test_super_admin_can_view_memo_pages(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.memos.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.memos.create'))
            ->assertOk();
    }

    public function test_non_super_admin_cannot_access_memos(): void
    {
        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.memos.index'))
            ->assertForbidden();
    }

    public function test_memo_can_be_sent_to_all_staff(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $staffOne = $this->makeStaff('office_assistant');
        $staffTwo = $this->makeStaff('hr');

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.memos.store'), [
                'subject' => 'Company update',
                'blocks' => $this->memoBlocks(),
                'recipient_type' => 'all',
            ]);

        $memo = InternalMemo::query()->firstOrFail();
        $response->assertRedirect(route('admin.memos.show', $memo));

        $this->assertSame(3, $memo->recipient_count);
        $this->assertSame(3, $memo->emails_sent);
        $this->assertNotNull($memo->sent_at);

        // 3 recipients: the sending super_admin plus the two other staff — "all staff" includes every active non-customer.
        Mail::assertSent(InternalMemoMail::class, 3);
        Mail::assertSent(InternalMemoMail::class, fn (InternalMemoMail $mail) => $mail->recipient->id === $staffOne->id);
        Mail::assertSent(InternalMemoMail::class, fn (InternalMemoMail $mail) => $mail->recipient->id === $staffTwo->id);
    }

    public function test_memo_can_target_a_specific_role(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $hrStaff = $this->makeStaff('hr');
        $this->makeStaff('office_assistant');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.memos.store'), [
                'subject' => 'HR only update',
                'blocks' => $this->memoBlocks(),
                'recipient_type' => 'role',
                'recipient_roles' => ['hr'],
            ])
            ->assertRedirect();

        $memo = InternalMemo::query()->firstOrFail();
        $this->assertSame(1, $memo->recipient_count);

        Mail::assertSent(InternalMemoMail::class, fn (InternalMemoMail $mail) => $mail->recipient->id === $hrStaff->id);
    }

    public function test_memo_can_target_specific_individuals(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $target = $this->makeStaff('office_assistant');
        $this->makeStaff('hr');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.memos.store'), [
                'subject' => 'Just for you',
                'blocks' => $this->memoBlocks(),
                'recipient_type' => 'individual',
                'recipient_user_ids' => [$target->id],
            ])
            ->assertRedirect();

        $memo = InternalMemo::query()->firstOrFail();
        $this->assertSame(1, $memo->recipient_count);

        Mail::assertSent(InternalMemoMail::class, 1);
    }

    public function test_memo_content_renders_with_the_recipients_name(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $target = $this->makeStaff('office_assistant');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.memos.store'), [
                'subject' => 'Personalized memo',
                'blocks' => $this->memoBlocks(),
                'recipient_type' => 'individual',
                'recipient_user_ids' => [$target->id],
            ]);

        Mail::assertSent(InternalMemoMail::class, function (InternalMemoMail $mail) use ($target): bool {
            $html = $mail->render();

            return str_contains($html, 'Hello '.$target->displayName());
        });
    }
}
