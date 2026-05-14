<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductQuoteOnlyPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_only_product_cta_prefills_quote_request_form(): void
    {
        $product = $this->quoteOnlyProduct();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSeeText('Request quotation')
            ->assertSee(route('quotes.create', ['product_id' => $product->id]), false)
            ->assertDontSee(route('orders.create', $product), false);

        $this->get(route('quotes.create', ['product_id' => $product->id]))
            ->assertOk()
            ->assertSeeText('Selected product')
            ->assertSeeText($product->name)
            ->assertSee('value="'.$product->id.'" selected', false)
            ->assertSee('value="'.$product->name.'" selected', false)
            ->assertSee('value="'.$product->moq.'"', false);
    }

    public function test_direct_order_route_redirects_quote_only_products_to_quote_form(): void
    {
        $product = $this->quoteOnlyProduct();

        $this->get(route('orders.create', $product))
            ->assertRedirect(route('quotes.create', ['product_id' => $product->id]));
    }

    public function test_admin_can_mark_product_price_as_unavailable(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $payload = [
            'service_type' => 'print',
            'name' => 'Embossed Luxury Pack',
            'moq' => 50,
            'price' => 0,
            'price_unavailable' => '1',
            'short_description' => 'Custom packaging',
            'description' => 'Custom packaging with variable pricing.',
            'paper_type' => 'Art Card',
            'paper_size' => 'Custom',
            'finishing' => 'Foil Stamping',
            'paper_density' => '350gsm',
            'is_active' => '1',
        ];

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.index'));

        $this->assertTrue((bool) Product::query()->where('name', 'Embossed Luxury Pack')->value('price_unavailable'));
    }

    private function quoteOnlyProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Custom Exhibition Booth',
            'moq' => 1,
            'price' => 0,
            'price_unavailable' => true,
            'short_description' => 'Custom booth build',
            'description' => 'Custom booth build with variable specifications.',
            'paper_type' => 'Acrylic',
            'paper_size' => 'Custom',
            'finishing' => 'No Finish',
            'paper_density' => 'Custom',
            'is_active' => true,
        ]);
    }
}
