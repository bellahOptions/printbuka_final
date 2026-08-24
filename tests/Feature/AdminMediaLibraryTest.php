<?php

namespace Tests\Feature;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminMediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_active_staff_member_can_list_the_image_library(): void
    {
        $staff = $this->staff('office_assistant');

        $response = $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->getJson(route('admin.media.index'));

        $response->assertOk()->assertJsonStructure(['ok', 'images', 'next_cursor']);
    }

    public function test_guest_cannot_reach_the_image_library(): void
    {
        $this->getJson(route('admin.media.index'))->assertRedirect();
    }

    public function test_upload_without_cloudinary_configured_fails_gracefully_not_with_a_server_error(): void
    {
        $staff = $this->staff('office_assistant');

        $response = $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->postJson(route('admin.media.store'), [
                'image' => UploadedFile::fake()->image('test.jpg', 400, 400),
            ]);

        $response->assertStatus(422)->assertJsonStructure(['ok', 'message']);
    }

    private function staff(string $role): User
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
}
