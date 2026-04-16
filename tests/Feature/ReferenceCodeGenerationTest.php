<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Support\ReferenceCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_number_is_standard_unique_code_and_remains_stable_for_same_order(): void
    {
        $order = $this->createOrder('print', ReferenceCode::jobOrderNumber('print'));

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createForOrder($order);
        $regenerated = $invoiceService->createForOrder($order->fresh());

        $this->assertSame($invoice->invoice_number, $regenerated->invoice_number);
        $this->assertMatchesRegularExpression('/^INV-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', $invoice->invoice_number);
        $this->assertDoesNotMatchRegularExpression('/^PB-INV-\d+$/', $invoice->invoice_number);
    }

    public function test_job_orders_receive_unique_reference_codes_by_service_type(): void
    {
        $printReference = ReferenceCode::jobOrderNumber('print');
        $quoteReference = ReferenceCode::jobOrderNumber('quote');

        $this->assertMatchesRegularExpression('/^JOB-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', $printReference);
        $this->assertMatchesRegularExpression('/^QTE-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', $quoteReference);
        $this->assertNotSame($printReference, $quoteReference);

        $printOrder = $this->createOrder('print', $printReference);
        $quoteOrder = $this->createOrder('quote', $quoteReference);

        $this->assertDatabaseHas('orders', ['id' => $printOrder->id, 'job_order_number' => $printReference]);
        $this->assertDatabaseHas('orders', ['id' => $quoteOrder->id, 'job_order_number' => $quoteReference]);
    }

    private function createOrder(string $serviceType, string $reference): Order
    {
        return Order::query()->create([
            'service_type' => $serviceType,
            'channel' => 'Online',
            'job_type' => $serviceType === 'quote' ? 'Quote Request' : 'Business Cards',
            'size_format' => 'A4',
            'quantity' => 1,
            'unit_price' => 1000,
            'total_price' => 1000,
            'customer_name' => 'Test Customer',
            'customer_email' => strtolower($reference).'@example.com',
            'customer_phone' => '08012345678',
            'delivery_city' => 'Lagos',
            'delivery_address' => '10 Admiralty Way',
            'status' => $serviceType === 'quote' ? 'Quote Requested' : 'Analyzing Job Brief',
            'job_order_number' => $reference,
            'priority' => '🟡 Normal',
            'payment_status' => $serviceType === 'quote' ? 'Awaiting Invoice' : 'Invoice Issued',
        ]);
    }
}

