<?php

namespace Tests\Feature;

use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPriceListCustomItemsTest extends TestCase
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

    public function test_operations_manager_can_create_list_and_delete_a_custom_item(): void
    {
        $user = $this->makeStaff('operations_manager');
        $product = Product::query()->create([
            'name' => 'Custom Item Test Product',
            'moq' => 100,
            'price' => 2500,
            'short_description' => 'Test product',
            'description' => 'Test product for custom price list item linking.',
            'paper_type' => 'Matte',
            'material_price_options' => [['label' => 'Matte', 'price' => 0]],
            'paper_size' => 'A4',
            'size_price_options' => [['label' => 'A4', 'price' => 0]],
            'finishing' => 'Gloss',
            'finish_price_options' => [['label' => 'Gloss', 'price' => 0]],
            'density_price_options' => [['label' => '350gsm', 'price' => 0]],
            'delivery_price_options' => [['label' => 'Pickup', 'price' => 0]],
            'paper_density' => '350gsm',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pricelist.custom.create'))
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.pricelist.custom.store'), [
                'label' => 'Custom Rush Fee',
                'service_slug' => 'dtf',
                'product_id' => $product->id,
                'price' => 2500,
            ])
            ->assertRedirect(route('admin.pricelist.custom.index'));

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'custom',
            'label' => 'Custom Rush Fee',
            'service_slug' => 'dtf',
            'product_id' => $product->id,
            'price' => 2500,
        ]);

        $item = PriceListItem::query()->where('label', 'Custom Rush Fee')->firstOrFail();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pricelist.custom.index'))
            ->assertOk()
            ->assertSee('Custom Rush Fee');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.pricelist.custom.destroy', $item))
            ->assertRedirect();

        $this->assertDatabaseMissing('price_list_items', ['id' => $item->id]);
    }

    public function test_managing_director_can_create_a_custom_item(): void
    {
        $user = $this->makeStaff('managing_director');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.pricelist.custom.store'), [
                'label' => 'MD Custom Item',
                'price' => 1000,
            ])
            ->assertRedirect(route('admin.pricelist.custom.index'));

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'custom',
            'label' => 'MD Custom Item',
        ]);
    }

    public function test_customer_service_still_has_pricelist_manage_access(): void
    {
        $user = $this->makeStaff('customer_service');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.pricelist.custom.store'), [
                'label' => 'CS Custom Item',
                'price' => 750,
            ])
            ->assertRedirect(route('admin.pricelist.custom.index'));

        $this->assertDatabaseHas('price_list_items', [
            'category' => 'custom',
            'label' => 'CS Custom Item',
        ]);
    }

    public function test_role_without_pricelist_manage_permission_is_forbidden(): void
    {
        $user = $this->makeStaff('office_assistant');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pricelist.custom.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.pricelist.custom.store'), [
                'label' => 'Blocked Item',
                'price' => 500,
            ])
            ->assertForbidden();
    }

    public function test_destroy_route_404s_for_non_custom_category_items(): void
    {
        $user = $this->makeStaff('operations_manager');

        $structuralItem = PriceListItem::query()->create([
            'category' => 'surcharge',
            'component_key' => 'express_order_surcharge',
            'label' => 'Express Order Surcharge',
            'price' => 5000,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.pricelist.custom.destroy', $structuralItem))
            ->assertNotFound();

        $this->assertDatabaseHas('price_list_items', ['id' => $structuralItem->id]);
    }
}
