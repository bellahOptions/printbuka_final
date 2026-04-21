<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportIndexRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_support_index_route(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/support')
            ->assertOk()
            ->assertSeeText('Support Tickets');
    }

    public function test_guest_is_redirected_to_login_from_support_index_route(): void
    {
        $this->get('/support')
            ->assertRedirect(route('login'));
    }
}

