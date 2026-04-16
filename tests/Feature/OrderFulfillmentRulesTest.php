<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderFulfillmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderFulfillmentRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_order_is_limited_to_two_units_and_auto_express_with_flat_fee(): void
    {
        Mail::fake();

        $product = $this->createProduct();

        $response = $this->post(route('orders.store', $product), $this->payload([
            'quantity' => 2,
            'is_sample' => '1',
            'delivery_method' => 'Client Pickup',
        ]));

        $order = Order::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('orders.success', $order));
        $this->assertTrue($order->is_sample);
        $this->assertTrue($order->is_express);
        $this->assertSame('9000.00', (string) $order->total_price);
        $this->assertNotNull($order->brief_received_at);
        $this->assertNotNull($order->estimated_delivery_at);
        $this->assertSame('🔴 Urgent', $order->priority);
        $this->assertSame(5000.0, (float) ($order->pricing_breakdown['sample_fee'] ?? 0));
        $this->assertSame(0.0, (float) ($order->pricing_breakdown['express_fee'] ?? 0));
    }

    public function test_sample_order_rejects_quantities_above_two_units(): void
    {
        Mail::fake();

        $product = $this->createProduct();

        $this->from(route('orders.create', $product))
            ->post(route('orders.store', $product), $this->payload([
                'quantity' => 3,
                'is_sample' => '1',
                'delivery_method' => 'Client Pickup',
            ]))
            ->assertRedirect(route('orders.create', $product))
            ->assertSessionHasErrors('quantity');
    }

    public function test_standard_orders_move_to_next_capacity_slot_when_day_is_full(): void
    {
        Mail::fake();

        Carbon::setTestNow(Carbon::parse('2026-04-16 10:00:00', 'Africa/Lagos'));
        try {
            $service = app(OrderFulfillmentService::class);
            $firstSlot = $service->estimateForNewOrder(false, now());

            for ($i = 0; $i < 6; $i++) {
                Order::query()->create([
                    'service_type' => 'print',
                    'quantity' => 10,
                    'unit_price' => 1000,
                    'total_price' => 10000,
                    'customer_name' => 'Client '.$i,
                    'customer_email' => "client{$i}@example.com",
                    'customer_phone' => '08012345678',
                    'status' => 'Analyzing Job Brief',
                    'payment_status' => 'Invoice Issued',
                    'estimated_delivery_at' => $firstSlot,
                    'is_express' => false,
                ]);
            }

            $product = $this->createProduct();
            $expectedSlot = $service->estimateForNewOrder(false, now());

            $this->post(route('orders.store', $product), $this->payload())
                ->assertRedirect();

            $order = Order::query()->latest('id')->firstOrFail();

            $this->assertFalse($order->is_express);
            $this->assertSame($expectedSlot->toDateString(), $order->estimated_delivery_at?->toDateString());
            $this->assertTrue($order->estimated_delivery_at?->gt($firstSlot) ?? false);
        } finally {
            Carbon::setTestNow();
        }
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
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
                ['label' => 'Dispatch Rider', 'price' => 1000],
            ],
            'paper_density' => '350gsm',
            'is_active' => true,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return [
            'quantity' => 100,
            'size_format' => 'A4',
            'material_substrate' => 'Matte',
            'paper_density' => '350gsm',
            'finish_lamination' => 'Gloss',
            'delivery_method' => 'Dispatch Rider',
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '08099998888',
            'delivery_city' => 'Lagos',
            'delivery_address' => '2 Manual Street',
            ...$overrides,
        ];
    }
}
