<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingSettingsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_cannot_update_service_prices_in_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'service_price_dtf' => 5000,
                'service_dtf_size_price_options' => "A2|1000\nA3|700",
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('site_settings', [
            'key' => 'service_price_dtf',
            'value' => '5000',
        ]);
    }

    public function test_super_admin_can_update_service_prices_in_settings(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.settings.update'), [
                'service_price_direct_image_printing' => 2600,
                'service_price_uv_dtf' => 3900,
                'service_price_dtf' => 3150,
                'service_price_dtf_design' => 1800,
                'service_price_dtf_delivery' => 900,
                'service_dtf_size_price_options' => "A2|1600\nA3|900\nA4|500\nA5|250\nA6|100",
                'service_price_laser_engraving' => 5400,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_direct_image_printing',
            'value' => '2600',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_uv_dtf',
            'value' => '3900',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_dtf',
            'value' => '3150',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_dtf_design',
            'value' => '1800',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_dtf_delivery',
            'value' => '900',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_dtf_size_price_options',
            'value' => "A2|1600\nA3|900\nA4|500\nA5|250\nA6|100",
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'service_price_laser_engraving',
            'value' => '5400',
        ]);
    }
}
