<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaystackCheckoutRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_checkout_redirects_to_paystack_when_gateway_is_configured(): void
    {
        Mail::fake();

        config()->set('services.paystack.secret_key', 'sk_test_123');

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-reference',
                ],
            ], 200),
        ]);

        $product = Product::query()->create([
            'name' => 'Business Cards',
            'moq' => 100,
            'price' => 2000,
            'short_description' => 'Premium business card printing',
            'description' => 'Premium business card printing for teams.',
            'paper_type' => 'Matte',
            'material_price_options' => [
                ['label' => 'Matte', 'price' => 0],
            ],
            'paper_size' => 'A4',
            'size_price_options' => [
                ['label' => 'A4', 'price' => 0],
            ],
            'finishing' => 'Gloss',
            'finish_price_options' => [
                ['label' => 'Gloss', 'price' => 0],
            ],
            'density_price_options' => [
                ['label' => '350gsm', 'price' => 0],
            ],
            'delivery_price_options' => [
                ['label' => 'Client Pickup', 'price' => 0],
            ],
            'paper_density' => '350gsm',
            'is_active' => true,
        ]);

        $response = $this->post(route('orders.store', $product), [
            'quantity' => 100,
            'size_format' => 'A4',
            'material_substrate' => 'Matte',
            'paper_density' => '350gsm',
            'finish_lamination' => 'Gloss',
            'delivery_method' => 'Client Pickup',
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '08099998888',
            'delivery_city' => 'Lagos',
            'delivery_address' => '2 Manual Street',
        ]);

        $response->assertRedirect('https://checkout.paystack.com/test-reference');

        $this->assertDatabaseHas('invoices', [
            'payment_gateway' => 'paystack',
        ]);
    }
}
