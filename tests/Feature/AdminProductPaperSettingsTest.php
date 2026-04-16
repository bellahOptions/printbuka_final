<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductPaperSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_form_uses_paper_dropdown_options_from_site_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        SiteSetting::query()->create(['key' => 'paper_types', 'value' => "Ultra Matte Board\nKraft", 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'paper_sizes', 'value' => "A7\nA2", 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'finishings', 'value' => "Soft Touch\nVelvet Lamination", 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'paper_densities', 'value' => "180gsm\n320gsm", 'group' => 'general']);

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Select paper type')
            ->assertSee('Ultra Matte Board')
            ->assertSee('Kraft')
            ->assertSee('Select paper size')
            ->assertSee('A7')
            ->assertSee('A2')
            ->assertSee('Select finishing')
            ->assertSee('Soft Touch')
            ->assertSee('Velvet Lamination')
            ->assertSee('Select paper density')
            ->assertSee('180gsm')
            ->assertSee('320gsm');
    }
}

