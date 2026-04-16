<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderRealtimePricingFromSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_page_uses_settings_default_option_prices_when_product_options_are_empty(): void
    {
        SiteSetting::query()->create([
            'key' => 'default_size_price_options',
            'value' => "A4|1200\nA3|2500",
            'group' => 'pricing',
        ]);

        $product = Product::query()->create([
            'name' => 'Flyers',
            'moq' => 100,
            'price' => 3000,
            'short_description' => 'Flyer print',
            'description' => 'Flyer print',
            'paper_type' => 'Art Paper',
            'paper_size' => 'A4',
            'finishing' => 'Gloss',
            'paper_density' => '300gsm',
            'is_active' => true,
        ]);

        $this->get(route('orders.create', $product))
            ->assertOk()
            ->assertSee('A4 + NGN 1,200.00')
            ->assertSee('A3 + NGN 2,500.00');
    }
}
