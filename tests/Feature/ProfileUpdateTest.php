<?php

namespace Tests\Feature;

use App\Support\LivewireSecureUploads;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_receives_generated_default_avatar_url_when_photo_is_missing(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'photo' => null,
            'avatar' => null,
        ]);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', (string) $user->profilePhotoUrl());
        $this->assertStringStartsWith('data:image/svg+xml;base64,', (string) $user->profile_photo_url);
    }

    public function test_customer_can_update_profile_and_email_remains_unchanged(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'original@example.com',
            'password' => 'Password123',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => 'Updated',
            'last_name' => 'Customer',
            'phone' => '08012345678',
            'companyName' => 'Updated Co',
            'address' => 'Lekki, Lagos',
            'date_of_birth' => '1998-03-10',
            'email' => 'hacker-change@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');

        $user->refresh();

        $this->assertSame('Updated', $user->first_name);
        $this->assertSame('Customer', $user->last_name);
        $this->assertSame('08012345678', $user->phone);
        $this->assertSame('Updated Co', $user->companyName);
        $this->assertSame('Lekki, Lagos', $user->address);
        $this->assertSame('original@example.com', $user->email);
    }

    public function test_admin_can_update_profile_via_admin_profile_route(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'department' => 'Management',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'admin@example.com',
            'password' => 'Password123',
        ]);

        $this->actingAs($admin)->put(route('admin.profile.update'), [
            'first_name' => 'Ada',
            'last_name' => 'Manager',
            'phone' => '09000000000',
            'companyName' => 'Printbuka HQ',
            'address' => 'Ikeja, Lagos',
            'date_of_birth' => '1990-01-15',
        ])->assertSessionHasNoErrors();

        $admin->refresh();

        $this->assertSame('Ada', $admin->first_name);
        $this->assertSame('Manager', $admin->last_name);
        $this->assertSame('09000000000', $admin->phone);
        $this->assertSame('Ikeja, Lagos', $admin->address);
        $this->assertSame('Management', $admin->department);
        $this->assertNull($admin->requested_role);
        $this->assertNull($admin->other_role);
        $this->assertSame('admin@example.com', $admin->email);
    }

    public function test_user_can_change_password_from_profile_when_current_password_is_correct(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'password' => 'OldPassword123',
        ]);

        $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'companyName' => $user->companyName,
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NewPassword123', (string) $user->fresh()->password));
    }

    public function test_customer_can_update_profile_photo_via_livewire_secure_path(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $livewirePath = 'user-photos/livewire-photo.jpg';
        Storage::disk('public')->put($livewirePath, 'image-content');

        $this->actingAs($user)
            ->withSession([LivewireSecureUploads::SESSION_KEY => [$livewirePath]])
            ->put(route('profile.update'), [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'companyName' => $user->companyName,
                'photo_upload_path' => $livewirePath,
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertSame($livewirePath, $user->fresh()->photo);
    }

    public function test_profile_photo_update_rejects_tampered_livewire_path(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $untrustedPath = 'user-photos/untrusted.jpg';
        Storage::disk('public')->put($untrustedPath, 'image-content');

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('profile.update'), [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'companyName' => $user->companyName,
                'photo_upload_path' => $untrustedPath,
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('photo');

        $this->assertNull($user->fresh()->photo);
    }
}
