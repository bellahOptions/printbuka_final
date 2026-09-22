<?php

namespace Tests\Feature;

use App\Models\StaffProfile;
use App\Models\SystemBackup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSystemBackupTest extends TestCase
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

    public function test_super_admin_can_view_the_backups_page(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.system-backups.index'))
            ->assertOk()
            ->assertSee('System Backups');
    }

    public function test_non_super_admin_cannot_access_system_backups(): void
    {
        // 'hr' can reach every other admin.permission gate in this suite's
        // fixtures, but the backups module is locked to the literal
        // super_admin role regardless of any permission it's granted.
        $staff = $this->makeStaff('hr');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.system-backups.index'))
            ->assertForbidden();
    }

    public function test_running_a_backup_creates_a_running_row_and_launches_a_background_process(): void
    {
        Process::fake();

        $admin = $this->makeStaff('super_admin');

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.system-backups.run'));

        $response->assertRedirect(route('admin.system-backups.index'));

        $backup = SystemBackup::query()->firstOrFail();
        $this->assertSame(SystemBackup::STATUS_RUNNING, $backup->status);
        $this->assertSame('manual', $backup->type);
        $this->assertSame($admin->id, $backup->triggered_by);

        Process::assertRan(function ($process) use ($backup): bool {
            $command = implode(' ', $process->command);

            return str_contains($command, 'system-backup:run')
                && str_contains($command, '--id='.$backup->id);
        });
    }

    public function test_a_second_backup_cannot_be_started_while_one_is_running(): void
    {
        Process::fake();

        $admin = $this->makeStaff('super_admin');

        SystemBackup::query()->create([
            'type' => 'manual',
            'status' => SystemBackup::STATUS_RUNNING,
            'disk' => 'backups',
            'started_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.system-backups.run'))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, SystemBackup::query()->count());
        Process::assertNothingRan();
    }

    public function test_a_successful_backup_can_be_downloaded(): void
    {
        Storage::fake('backups');
        Storage::disk('backups')->put('Printbuka/test-backup.zip', 'fake-zip-contents');

        $admin = $this->makeStaff('super_admin');

        $backup = SystemBackup::query()->create([
            'type' => 'manual',
            'status' => SystemBackup::STATUS_SUCCESS,
            'disk' => 'backups',
            'path' => 'Printbuka/test-backup.zip',
            'size_bytes' => strlen('fake-zip-contents'),
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.system-backups.download', $backup))
            ->assertOk()
            ->assertDownload('test-backup.zip');
    }

    public function test_deleting_a_backup_removes_the_file_and_the_row(): void
    {
        Storage::fake('backups');
        Storage::disk('backups')->put('Printbuka/to-delete.zip', 'fake-zip-contents');

        $admin = $this->makeStaff('super_admin');

        $backup = SystemBackup::query()->create([
            'type' => 'manual',
            'status' => SystemBackup::STATUS_SUCCESS,
            'disk' => 'backups',
            'path' => 'Printbuka/to-delete.zip',
            'size_bytes' => strlen('fake-zip-contents'),
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.system-backups.destroy', $backup))
            ->assertRedirect();

        $this->assertSame(0, SystemBackup::query()->count());
        Storage::disk('backups')->assertMissing('Printbuka/to-delete.zip');
    }

    public function test_a_running_backup_cannot_be_deleted(): void
    {
        $admin = $this->makeStaff('super_admin');

        $backup = SystemBackup::query()->create([
            'type' => 'manual',
            'status' => SystemBackup::STATUS_RUNNING,
            'disk' => 'backups',
            'started_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.system-backups.destroy', $backup))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, SystemBackup::query()->count());
    }
}
