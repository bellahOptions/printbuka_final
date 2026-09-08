<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShopCatalogInfiniteLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_products_index_uses_infinite_loading(): void
    {
        foreach (range(1, 22) as $index) {
            ShopProduct::query()->create([
                'name' => 'Mug '.$index,
                'price' => 1500,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->admin())
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.shop-products.index'));

        $response->assertOk();
        $response->assertSeeText('Loading more products as you scroll...');
        $response->assertDontSee('?page=2');
        $response->assertSeeText('Mug 1');
    }

    public function test_shop_products_search_filter_still_works(): void
    {
        ShopProduct::query()->create(['name' => 'Ceramic Mug', 'price' => 1500, 'is_active' => true]);
        ShopProduct::query()->create(['name' => 'Tote Bag', 'price' => 2500, 'is_active' => true]);

        $response = $this->actingAs($this->admin())
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.shop-products.index', ['search' => 'Ceramic']));

        $response->assertOk();
        $response->assertSeeText('Ceramic Mug');
        $response->assertDontSeeText('Tote Bag');
    }

    public function test_shop_orders_index_uses_infinite_loading(): void
    {
        foreach (range(1, 27) as $index) {
            $this->createShopOrder('SO-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT), 'order_received');
        }

        $response = $this->actingAs($this->admin())
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.shop-orders.index'));

        $response->assertOk();
        $response->assertSeeText('Loading more orders as you scroll...');
        $response->assertDontSee('?page=2');
        $response->assertSeeText('SO-0001');
    }

    public function test_shop_orders_status_filter_still_works(): void
    {
        $this->createShopOrder('SO-DISPATCH', 'dispatched');
        $this->createShopOrder('SO-RECEIVED', 'order_received');

        $response = $this->actingAs($this->admin())
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.shop-orders.index', ['status' => 'dispatched']));

        $response->assertOk();
        $response->assertSeeText('SO-DISPATCH');
        $response->assertDontSeeText('SO-RECEIVED');
    }

    public function test_product_categories_index_uses_infinite_loading(): void
    {
        foreach (range(1, 22) as $index) {
            ProductCategory::query()->create([
                'name' => 'Category '.$index,
                'slug' => 'category-'.$index,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->admin())
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.product-categories.index'));

        $response->assertOk();
        $response->assertSeeText('Loading more categories as you scroll...');
        $response->assertDontSee('?page=2');
        $response->assertSeeText('Category 1');
    }

    private function createShopOrder(string $reference, string $fulfillmentStatus): ShopOrder
    {
        return ShopOrder::query()->create([
            'reference' => $reference,
            'customer_name' => $reference.' Customer',
            'customer_email' => strtolower($reference).'@example.com',
            'shipping_name' => $reference.' Customer',
            'shipping_address' => '1 Test Street',
            'shipping_city' => 'Lagos',
            'shipping_state' => 'Lagos',
            'subtotal' => 1000,
            'total' => 1000,
            'payment_status' => 'paid',
            'fulfillment_status' => $fulfillmentStatus,
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
