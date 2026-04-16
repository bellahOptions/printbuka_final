<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminOrderCreationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_job_for_existing_customer_with_pickup_and_creator_tracking(): void
    {
        Mail::fake();
        $manualBriefDate = now()->subDays(4)->format('Y-m-d H:i:s');

        $admin = $this->adminUser('customer_service');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'first_name' => 'Client',
            'last_name' => 'Account',
            'email' => 'client.account@example.com',
            'phone' => '08011223344',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.store'), [
                'channel' => 'Manual',
                'job_type' => 'Business Cards',
                'quantity' => 20,
                'unit_price' => 1500,
                'priority' => '🟡 Normal',
                'payment_status' => 'Invoice Issued',
                'delivery_preference' => 'pickup',
                'brief_received_at' => $manualBriefDate,
                'customer_id' => $customer->id,
                'customer_name' => 'Wrong Name',
                'customer_email' => 'wrong.email@example.com',
                'customer_phone' => '08000000000',
            ])
            ->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame($admin->id, $order->created_by_admin_id);
        $this->assertSame($admin->id, $order->brief_received_by_id);
        $this->assertNotSame($manualBriefDate, $order->brief_received_at?->format('Y-m-d H:i:s'));
        $this->assertSame($customer->displayName(), $order->customer_name);
        $this->assertSame($customer->email, $order->customer_email);
        $this->assertSame($customer->phone, $order->customer_phone);
        $this->assertSame('Client Pickup', $order->delivery_method);
        $this->assertNull($order->delivery_city);
        $this->assertNull($order->delivery_address);
        $this->assertMatchesRegularExpression('/^JOB-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', (string) $order->job_order_number);

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
        ]);
    }

    public function test_delivery_city_and_address_are_required_when_delivery_is_selected(): void
    {
        Mail::fake();

        $admin = $this->adminUser('customer_service');

        $this->actingAs($admin)
            ->post(route('admin.orders.store'), [
                'channel' => 'Manual',
                'job_type' => 'Business Cards',
                'quantity' => 20,
                'unit_price' => 1500,
                'priority' => '🟡 Normal',
                'payment_status' => 'Invoice Issued',
                'delivery_preference' => 'delivery',
                'customer_name' => 'Delivery Client',
                'customer_email' => 'delivery.client@example.com',
                'customer_phone' => '08044556677',
                'delivery_method' => 'Dispatch Rider',
            ])
            ->assertSessionHasErrors(['delivery_city', 'delivery_address']);
    }

    private function adminUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
