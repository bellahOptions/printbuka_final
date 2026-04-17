<?php

namespace Tests\Feature;

use App\Mail\InvoicePaidReceiptMail;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminInvoiceCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_invoice_from_printbuka_product_catalog(): void
    {
        Mail::fake();

        $admin = $this->adminUser('super_admin');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'first_name' => 'Invoice',
            'last_name' => 'Client',
            'email' => 'invoice.client@example.com',
            'phone' => '08011112222',
        ]);
        $product = Product::query()->create([
            'name' => 'Business Cards',
            'moq' => 100,
            'price' => 2500,
            'short_description' => 'Premium print',
            'description' => 'Premium business card printing.',
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

        $this->actingAs($admin)
            ->post(route('admin.invoices.store'), [
                'customer_id' => $customer->id,
                'customer_name' => 'Ignore Name',
                'customer_email' => 'ignore@example.com',
                'customer_phone' => '08000000000',
                'catalog_item_key' => 'product:'.$product->id,
                'quantity' => 4,
                'tax_amount' => 500,
                'discount_amount' => 100,
                'invoice_status' => 'unpaid',
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('status');

        $order = Order::query()->latest('id')->firstOrFail();
        $invoice = Invoice::query()->where('order_id', $order->id)->firstOrFail();

        $this->assertSame($product->id, $order->product_id);
        $this->assertSame('print', $order->service_type);
        $this->assertSame('Business Cards', $order->job_type);
        $this->assertSame('10000.00', (string) $invoice->subtotal);
        $this->assertSame('10400.00', (string) $invoice->total_amount);
        $this->assertSame('unpaid', $invoice->status);
        $this->assertSame('product:'.$product->id, $order->pricing_breakdown['catalog_item_key'] ?? null);
    }

    public function test_invoice_creation_rejects_non_catalog_item_keys(): void
    {
        $admin = $this->adminUser('super_admin');

        $this->actingAs($admin)
            ->from(route('admin.invoices.create'))
            ->post(route('admin.invoices.store'), [
                'customer_name' => 'Invalid Client',
                'customer_email' => 'invalid.client@example.com',
                'customer_phone' => '08055556666',
                'catalog_item_key' => 'custom:123',
                'quantity' => 1,
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.create'))
            ->assertSessionHasErrors('catalog_item_key');
    }

    public function test_admin_can_create_paid_invoice_from_service_catalog(): void
    {
        Mail::fake();

        $admin = $this->adminUser('operations');

        $this->actingAs($admin)
            ->post(route('admin.invoices.store'), [
                'customer_name' => 'Service Client',
                'customer_email' => 'service.client@example.com',
                'customer_phone' => '08077778888',
                'catalog_item_key' => 'service:dtf',
                'quantity' => 3,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'invoice_status' => 'paid',
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('status');

        $order = Order::query()->latest('id')->firstOrFail();
        $invoice = Invoice::query()->where('order_id', $order->id)->firstOrFail();

        $this->assertNull($order->product_id);
        $this->assertSame('service:dtf', $order->service_type);
        $this->assertSame('DTF', $order->job_type);
        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame((string) $invoice->total_amount, (string) $order->amount_paid);

        Mail::assertSent(InvoicePaidReceiptMail::class, function (InvoicePaidReceiptMail $mail): bool {
            return $mail->hasTo('service.client@example.com');
        });
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
