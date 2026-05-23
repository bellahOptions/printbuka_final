<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentProcessRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_initiate_payment_for_own_invoice(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'customer@example.com',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'service_type' => 'print',
            'quantity' => 2,
            'unit_price' => 5000,
            'total_price' => 10000,
            'customer_name' => 'Customer Example',
            'customer_email' => $customer->email,
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-TEST-001',
            'payment_status' => 'Pending Payment',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-TEST-001',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'pending',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->mock(PaystackService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('initializeForInvoice')
                ->once()
                ->andReturn([
                    'ok' => true,
                    'authorization_url' => 'https://paystack.example/checkout/test',
                ]);
        });

        $this->actingAs($customer)
            ->get(route('payment.process', $invoice))
            ->assertRedirect('https://paystack.example/checkout/test');
    }

    public function test_customer_cannot_initiate_payment_for_another_users_invoice(): void
    {
        $owner = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'owner@example.com',
        ]);

        $intruder = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'email' => 'intruder@example.com',
        ]);

        $order = Order::query()->create([
            'user_id' => $owner->id,
            'service_type' => 'print',
            'quantity' => 1,
            'unit_price' => 8000,
            'total_price' => 8000,
            'customer_name' => 'Owner User',
            'customer_email' => $owner->email,
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-TEST-002',
            'payment_status' => 'Pending Payment',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-TEST-002',
            'subtotal' => 8000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 8000,
            'status' => 'pending',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->actingAs($intruder)
            ->get(route('payment.process', $invoice))
            ->assertRedirect(route('user.invoices.index'))
            ->assertSessionHas('error');
    }
}

