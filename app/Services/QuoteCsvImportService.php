<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\ImportedCustomer;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteCsvImportService
{
    /**
     * @return array{customers:int,jobs:int,invoices:int,rows:int}
     */
    public function import(UploadedFile $file, ?User $actor = null): array
    {
        $rows = $this->readRows($file->getRealPath());
        $groups = collect($rows)
            ->filter(fn (array $row): bool => filled($this->documentNumber($row)) || filled($this->documentId($row)))
            ->groupBy(fn (array $row): string => $this->documentId($row) ?: $this->documentNumber($row))
            ->values();

        $stats = ['customers' => 0, 'jobs' => 0, 'invoices' => 0, 'rows' => count($rows)];

        DB::transaction(function () use ($groups, $actor, &$stats): void {
            foreach ($groups as $documentRows) {
                $first = $documentRows->first();
                $documentType = $this->documentType($first);
                $customer = $this->customerFor($first);
                $stats['customers']++;

                $invoiceNumber = $this->documentNumber($first) ?: 'CSV-'.Str::upper(Str::random(8));
                $externalDocumentId = $this->documentId($first);
                $issuedAt = $this->documentDate($first) ?? now();
                $total = $this->moneyValue($first, 'Total');
                $subtotal = $this->moneyValue($first, 'SubTotal');
                $discount = $this->moneyValue($first, 'Entity Discount Amount');
                $tax = max(0, $total - $subtotal + $discount);
                $quantity = max(1, (int) round($documentRows->sum(fn (array $row): float => max(0, $this->moneyValue($row, 'Quantity')))));
                $lineItems = $documentRows
                    ->map(fn (array $row): string => trim(implode(' | ', array_filter([
                        $this->value($row, 'Item Name'),
                        $this->value($row, 'Item Desc'),
                        'Qty: '.$this->moneyValue($row, 'Quantity'),
                        'Total: '.$this->moneyValue($row, 'Item Total'),
                    ]))))
                    ->filter()
                    ->values()
                    ->all();

                $order = Order::query()->updateOrCreate(
                    ['job_order_number' => $invoiceNumber],
                    [
                        'user_id' => null,
                        'imported_customer_id' => $customer->id,
                        'created_by_admin_id' => $actor?->id,
                        'service_type' => $documentType,
                        'channel' => 'CSV Import',
                        'job_type' => $this->value($first, 'Subject') ?: ($this->value($first, 'Item Name') ?: 'Imported '.($documentType === 'quote' ? 'quote' : 'invoice')),
                        'quantity' => $quantity,
                        'unit_price' => $quantity > 0 ? round($subtotal / $quantity, 2) : $subtotal,
                        'total_price' => $subtotal,
                        'customer_name' => $customer->displayName(),
                        'customer_email' => $customer->email ?: $this->fallbackEmailFor($customer),
                        'customer_phone' => $customer->phone ?: ($customer->customer_number ?: 'N/A'),
                        'delivery_city' => $this->value($first, 'Shipping City') ?: $this->value($first, 'Billing City'),
                        'delivery_address' => $this->addressFor($first),
                        'artwork_notes' => $this->value($first, 'Notes'),
                        'status' => 'Delivered',
                        'priority' => 'Imported',
                        'actual_delivery_at' => $issuedAt,
                        'amount_paid' => $total,
                        'payment_status' => 'Invoice Settled (100%)',
                        'internal_notes' => trim("Imported from CSV {$documentType} {$invoiceNumber}. External ID: {$externalDocumentId}\n".implode("\n", $lineItems)),
                        'phase_approval_status' => 'Approved',
                        'phase_approval_comment' => 'Imported as paid and delivered from uploaded CSV.',
                        'phase_approved_by_id' => $actor?->id,
                        'phase_approved_at' => now(),
                    ]
                );
                $stats['jobs']++;

                Invoice::query()->updateOrCreate(
                    ['invoice_number' => $invoiceNumber],
                    [
                        'order_id' => $order->id,
                        'imported_customer_id' => $customer->id,
                        'external_document_id' => $externalDocumentId,
                        'external_customer_id' => $this->value($first, 'Customer ID'),
                        'payment_reference' => $externalDocumentId ? 'CSV-'.$externalDocumentId : null,
                        'payment_gateway' => 'csv_import',
                        'subtotal' => $subtotal,
                        'tax_amount' => $tax,
                        'discount_amount' => $discount,
                        'total_amount' => $total,
                        'status' => 'paid',
                        'issued_at' => $issuedAt,
                        'due_at' => $this->dueDate($first),
                        'sent_at' => $issuedAt,
                        'paid_at' => $issuedAt,
                    ]
                );
                $stats['invoices']++;
            }
        });

        return $stats;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle);
        $rows = [];

        if (! is_array($headers)) {
            fclose($handle);

            return [];
        }

        $headers = array_map(fn (mixed $header): string => trim((string) $header), $headers);

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null] || $line === false) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = trim((string) ($line[$index] ?? ''));
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function customerFor(array $row): ImportedCustomer
    {
        $customerId = $this->value($row, 'Customer ID') ?: Str::slug($this->value($row, 'Customer Name'));
        $name = $this->value($row, 'Customer Name') ?: 'Imported Customer';
        $email = $this->value($row, 'Primary Contact EmailID');
        [$firstName, $lastName] = $this->splitName($name);

        $identity = $customerId !== ''
            ? ['external_customer_id' => $customerId]
            : ($this->value($row, 'Customer Number') !== ''
                ? ['customer_number' => $this->value($row, 'Customer Number')]
                : ['name' => $name]);

        return ImportedCustomer::query()->updateOrCreate(
            $identity,
            [
                'external_customer_id' => $customerId ?: null,
                'customer_number' => $this->value($row, 'Customer Number') ?: null,
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email ?: null,
                'phone' => $this->value($row, 'Primary Contact Mobile') ?: $this->value($row, 'Primary Contact Phone') ?: $this->value($row, 'Billing Phone') ?: $this->value($row, 'Shipping Phone Number') ?: null,
                'company_name' => $name,
                'billing_address' => $this->value($row, 'Billing Address') ?: null,
                'billing_city' => $this->value($row, 'Billing City') ?: null,
                'billing_state' => $this->value($row, 'Billing State') ?: null,
                'billing_country' => $this->value($row, 'Billing Country') ?: null,
                'billing_code' => $this->value($row, 'Billing Code') ?: null,
                'shipping_address' => $this->value($row, 'Shipping Address') ?: null,
                'shipping_city' => $this->value($row, 'Shipping City') ?: null,
                'shipping_state' => $this->value($row, 'Shipping State') ?: null,
                'shipping_country' => $this->value($row, 'Shipping Country') ?: null,
                'shipping_code' => $this->value($row, 'Shipping Code') ?: null,
                'source' => 'csv_import',
            ]
        );
    }

    private function fallbackEmailFor(ImportedCustomer $customer): string
    {
        $key = $customer->external_customer_id ?: $customer->customer_number ?: $customer->id;

        return 'imported+'.Str::lower(Str::slug((string) $key)).'@import.printbuka.local';
    }

    /**
     * @return array{0:string,1:string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [];

        return [
            $parts[0] ?? 'Imported',
            $parts[1] ?? 'Customer',
        ];
    }

    private function addressFor(array $row): ?string
    {
        $address = $this->value($row, 'Shipping Address') ?: $this->value($row, 'Billing Address');
        $city = $this->value($row, 'Shipping City') ?: $this->value($row, 'Billing City');
        $state = $this->value($row, 'Shipping State') ?: $this->value($row, 'Billing State');
        $country = $this->value($row, 'Shipping Country') ?: $this->value($row, 'Billing Country');

        return collect([$address, $city, $state, $country])
            ->filter()
            ->implode(', ') ?: null;
    }

    private function value(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    private function documentType(array $row): string
    {
        return filled($this->value($row, 'Quote Number')) || filled($this->value($row, 'Quote ID')) ? 'quote' : 'print';
    }

    private function documentNumber(array $row): string
    {
        return $this->value($row, 'Quote Number') ?: $this->value($row, 'Invoice Number');
    }

    private function documentId(array $row): string
    {
        return $this->value($row, 'Quote ID') ?: $this->value($row, 'Invoice ID');
    }

    private function documentDate(array $row): ?Carbon
    {
        return $this->dateValue($row, 'Quote Date') ?? $this->dateValue($row, 'Invoice Date') ?? $this->dateValue($row, 'Issued Date');
    }

    private function dueDate(array $row): ?Carbon
    {
        return $this->dateValue($row, 'Expiry Date') ?? $this->dateValue($row, 'Due Date');
    }

    private function moneyValue(array $row, string $key): float
    {
        return (float) str_replace([',', ' '], '', $this->value($row, $key));
    }

    private function dateValue(array $row, string $key): ?Carbon
    {
        $value = $this->value($row, $key);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
