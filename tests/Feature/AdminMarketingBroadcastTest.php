<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class AdminMarketingBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_public_advertisement(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.advertisements.store'), [
                'placement' => 'top_banner',
                'title' => 'Weekend print deal',
                'body' => 'Save on business cards this weekend.',
                'cta_label' => 'Order now',
                'cta_url' => 'https://printbuka.test/products',
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('advertisements', [
            'title' => 'Weekend print deal',
            'placement' => 'top_banner',
            'is_active' => true,
        ]);

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Weekend print deal');
    }

    public function test_super_admin_notification_uses_laravel_database_notifications(): void
    {
        $admin = $this->superAdmin();
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notifications.store'), [
                'audience' => 'customers',
                'title' => 'Production update',
                'message' => 'Your dashboard now shows new order alerts.',
                'type' => 'info',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertTrue(DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->where('notifiable_id', $customer->id)
            ->where('data->title', 'Production update')
            ->exists());
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
