<?php

namespace Tests\Feature;

use App\Models\PriceListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingSettingsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_without_pricelist_permission_cannot_update_service_prices(): void
    {
        $staff = User::factory()->create([
            'role' => 'office_assistant',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff)
            ->put(route('admin.pricelist.services.update', 'dtf'), [
                'price' => 5000,
                'design_fee' => 1000,
                'delivery_fee' => 500,
                'size_options' => "A2|1000\nA3|700",
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('price_list_items', [
            'category' => 'service',
            'service_slug' => 'dtf',
            'price' => '5000.00',
        ]);
    }

    public function test_customer_service_can_update_service_prices(): void
    {
        $customerService = User::factory()->create([
            'role' => 'customer_service',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customerService)
            ->put(route('admin.pricelist.services.update', 'dtf'), [
                'price' => 3150,
                'design_fee' => 1800,
                'delivery_fee' => 900,
                'size_options' => "A2|1600\nA3|900\nA4|500\nA5|250\nA6|100",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'service',
            'service_slug' => 'dtf',
            'component_group' => null,
            'price' => '3150.00',
            'updated_by_id' => $customerService->id,
        ]);

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'service',
            'service_slug' => 'dtf',
            'component_group' => 'fee',
            'component_key' => 'design_fee',
            'price' => '1800.00',
        ]);

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'service',
            'service_slug' => 'dtf',
            'component_group' => 'size',
            'label' => 'A3',
            'price' => '900.00',
        ]);
    }

    public function test_super_admin_can_update_surcharges(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.pricelist.surcharges.update'), [
                'express_order_surcharge' => 6000,
                'sample_order_surcharge' => 4500,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'surcharge',
            'component_key' => 'express_order_surcharge',
            'price' => '6000.00',
        ]);

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'surcharge',
            'component_key' => 'sample_order_surcharge',
            'price' => '4500.00',
        ]);
    }
}
