<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteRequestBudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_submit_quote_with_budget_for_review(): void
    {
        $product = Product::query()->create([
            'name' => 'Business Card',
            'moq' => 100,
            'price' => 5000,
            'short_description' => 'Premium business card print',
            'description' => 'Premium business card print',
            'paper_type' => 'Matte',
            'paper_size' => 'A6',
            'finishing' => 'Gloss',
            'paper_density' => '300gsm',
            'is_active' => true,
        ]);

        $this->post(route('quotes.store'), [
            'product_id' => $product->id,
            'job_type' => 'Business Card',
            'size_format' => 'A6',
            'quantity' => 500,
            'quote_budget' => 150000,
            'customer_name' => 'Jane Client',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '08012345678',
            'delivery_city' => 'Lagos',
            'delivery_address' => '10 Admiralty Way',
            'material_substrate' => 'Art Card',
            'finish_lamination' => 'Gloss Lamination',
            'artwork_notes' => 'Please deliver in one batch.',
        ])->assertSessionHasNoErrors();

        $order = Order::query()->firstOrFail();

        $this->assertSame('quote', $order->service_type);
        $this->assertSame('150000.00', $order->quote_budget);
    }
}

