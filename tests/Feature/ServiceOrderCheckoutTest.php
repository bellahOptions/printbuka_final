<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_pages_and_individual_service_pages_are_accessible(): void
    {
        $response = $this->get(route('services.index'));

        $response->assertOk()->assertSee('Our Services');

        foreach (config('printbuka_services.services', []) as $slug => $service) {
            $this->get(route('services.show', $slug))
                ->assertOk()
                ->assertSee((string) ($service['name'] ?? ''));
        }
    }

    public function test_uv_dtf_service_order_route_redirects_to_products_section(): void
    {
        $response = $this->post(route('services.orders.store', 'uv-dtf'), [
            'quantity' => 3,
            'delivery_method' => 'Client Pickup',
            'customer_name' => 'Service Client',
            'customer_email' => 'service.client@example.com',
            'customer_phone' => '08012345678',
            'artwork_notes' => 'Need premium UV DTF branding transfer.',
        ]);

        $response->assertRedirect(route('products.index').'#uv-dtf-products');
        $response->assertSessionHas('warning', 'Please order this service from the products section.');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('invoices', 0);
    }
}
