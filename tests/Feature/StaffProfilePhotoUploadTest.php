<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffProfilePhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_upload_profile_photo_for_staff(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'department' => 'Management',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'role' => 'designer',
            'department' => 'Design',
            'is_active' => true,
            'email_verified_at' => now(),
            'photo' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.staff.update', $staff), [
            'role' => $staff->role,
            'department' => $staff->department,
            'is_active' => 1,
            'photo' => UploadedFile::fake()->image('profile.jpg', 300, 300),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');

        $staff->refresh();

        $this->assertNotNull($staff->photo);
        $this->assertStringStartsWith('staff-photos/', (string) $staff->photo);
        Storage::disk('public')->assertExists((string) $staff->photo);
    }

    public function test_old_staff_photo_is_removed_when_replaced(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'department' => 'Management',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'role' => 'finance',
            'department' => 'Finance',
            'is_active' => true,
            'email_verified_at' => now(),
            'photo' => 'staff-photos/old-photo.jpg',
        ]);

        Storage::disk('public')->put('staff-photos/old-photo.jpg', 'old');

        $this->actingAs($admin)->put(route('admin.staff.update', $staff), [
            'role' => $staff->role,
            'department' => $staff->department,
            'is_active' => 1,
            'photo' => UploadedFile::fake()->image('new-photo.jpg', 300, 300),
        ])->assertSessionHasNoErrors();

        $staff->refresh();

        Storage::disk('public')->assertMissing('staff-photos/old-photo.jpg');
        Storage::disk('public')->assertExists((string) $staff->photo);
        $this->assertNotEquals('staff-photos/old-photo.jpg', $staff->photo);
    }

    public function test_invalid_file_type_is_rejected_for_staff_photo_upload(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'department' => 'Management',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'role' => 'production',
            'department' => 'Production',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.staff.index'))
            ->put(route('admin.staff.update', $staff), [
                'role' => $staff->role,
                'department' => $staff->department,
                'is_active' => 1,
                'photo' => UploadedFile::fake()->create('script.svg', 12, 'image/svg+xml'),
            ])
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHasErrors('photo');

        $this->assertNull($staff->fresh()->photo);
    }
}
