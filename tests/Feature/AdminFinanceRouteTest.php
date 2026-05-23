<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFinanceRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_staff_can_open_finance_index_and_create_pages(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($finance)
            ->get(route('admin.finance.index'))
            ->assertOk();

        $this->actingAs($finance)
            ->get(route('admin.finance.create'))
            ->assertOk()
            ->assertViewIs('admin.finance.form');
    }
}

