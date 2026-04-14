<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'product' => 'Business Cards',
                'customer_name' => 'Amina Okafor',
                'customer_email' => 'amina.okafor@example.com',
                'customer_phone' => '08030000001',
                'delivery_city' => 'Lagos',
                'delivery_address' => '12 Admiralty Way, Lekki Phase 1',
                'quantity' => 200,
                'status' => 'in_review',
                'artwork_notes' => 'Use the uploaded logo and keep the finish matte.',
            ],
            [
                'product' => 'Flyers',
                'customer_name' => 'Tunde Balogun',
                'customer_email' => 'tunde.balogun@example.com',
                'customer_phone' => '08030000002',
                'delivery_city' => 'Abuja',
                'delivery_address' => 'Plot 44 Gimbiya Street, Area 11',
                'quantity' => 1000,
                'status' => 'production',
                'artwork_notes' => 'Event flyer needed for weekend activation.',
            ],
            [
                'product' => 'Branded Mugs',
                'customer_name' => 'Chioma Nwosu',
                'customer_email' => 'chioma.nwosu@example.com',
                'customer_phone' => '08030000003',
                'delivery_city' => 'Port Harcourt',
                'delivery_address' => '18 Stadium Road',
                'quantity' => 24,
                'status' => 'pending',
                'artwork_notes' => 'Logo on one side, thank-you message on the other side.',
            ],
            [
                'product' => 'Corporate Gift Sets',
                'customer_name' => 'Kola Adeyemi',
                'customer_email' => 'kola.adeyemi@example.com',
                'customer_phone' => '08030000004',
                'delivery_city' => 'Ibadan',
                'delivery_address' => '7 Ring Road, Challenge',
                'quantity' => 20,
                'status' => 'delivered',
                'artwork_notes' => 'Pack with branded sleeves and client cards.',
            ],
        ];

        foreach ($orders as $seedOrder) {
            $product = Product::query()->where('name', $seedOrder['product'])->first();

            if (! $product) {
                continue;
            }

            $quantity = (int) $seedOrder['quantity'];
            $batches = (int) ceil($quantity / max(1, $product->moq));
            $total = $batches * (float) $product->price;

            $order = Order::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'customer_email' => $seedOrder['customer_email'],
                ],
                [
                    'user_id' => null,
                    'service_type' => $this->serviceTypeFor($product),
                    'job_type' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $total,
                    'customer_name' => $seedOrder['customer_name'],
                    'customer_phone' => $seedOrder['customer_phone'],
                    'delivery_city' => $seedOrder['delivery_city'],
                    'delivery_address' => $seedOrder['delivery_address'],
                    'artwork_notes' => $seedOrder['artwork_notes'],
                    'status' => $this->workbookStatusFor($seedOrder['status']),
                    'priority' => '🟡 Normal',
                    'payment_status' => $seedOrder['status'] === 'delivered' ? 'Invoice Settled (100%)' : 'Invoice Issued',
                    'amount_paid' => $seedOrder['status'] === 'delivered' ? $total : 0,
                ]
            );
            $order->update([
                'job_order_number' => 'PB-'.now()->format('Y').'-'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT),
            ]);

            Invoice::query()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => 'PB-INV-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                    'subtotal' => $total,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $total,
                    'status' => 'sent',
                    'issued_at' => now(),
                    'due_at' => now()->addDays(7),
                    'sent_at' => now(),
                ]
            );
        }
    }

    private function serviceTypeFor(Product $product): string
    {
        $name = strtolower($product->name.' '.$product->short_description.' '.$product->description);

        return str_contains($name, 'gift') || str_contains($name, 'mug') || str_contains($name, 'shirt') || str_contains($name, 'tote')
            ? 'gift'
            : 'print';
    }

    private function workbookStatusFor(string $status): string
    {
        return match ($status) {
            'in_review' => 'Design / Artwork Preparation',
            'production' => 'In Production',
            'delivered' => 'Delivered',
            default => 'Analyzing Job Brief',
        };
    }
}
