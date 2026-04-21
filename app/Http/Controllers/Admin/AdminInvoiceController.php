<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceLifecycleService;
use App\Services\InvoiceService;
use App\Support\ProductOptionPricing;
use App\Support\ReferenceCode;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminInvoiceController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function allowedInvoiceStatuses(): array
    {
        return ['unpaid', 'paid', 'disputed'];
    }

    public function index(): View
    {
        return view('admin.invoices.index');
    }

    public function create(): View
    {
        $services = collect(ServiceCatalog::all())
            ->map(function (array $service, string $slug): array {
                return [
                    'key' => 'service:'.$slug,
                    'slug' => $slug,
                    'name' => (string) ($service['name'] ?? str($slug)->replace('-', ' ')->title()),
                    'price' => ServiceCatalog::priceForSlug($slug),
                ];
            })
            ->values();
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.invoices.create', [
            'customers' => User::query()
                ->where('role', 'customer')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'products' => $products,
            'services' => $services,
            'sizes' => config('printbuka_admin.sizes'),
            'invoiceStatuses' => $this->allowedInvoiceStatuses(),
            'productOptionCatalog' => $this->productOptionCatalog($products),
        ]);
    }

    public function createQuotation(): View
    {
        $services = collect(ServiceCatalog::all())
            ->map(function (array $service, string $slug): array {
                return [
                    'key' => 'service:'.$slug,
                    'slug' => $slug,
                    'name' => (string) ($service['name'] ?? str($slug)->replace('-', ' ')->title()),
                    'price' => ServiceCatalog::priceForSlug($slug),
                ];
            })
            ->values();

        return view('admin.invoices.quotation', [
            'customers' => User::query()
                ->where('role', 'customer')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'services' => $services,
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
            'finishes' => config('printbuka_admin.finishes'),
        ]);
    }

    public function store(
        Request $request,
        InvoiceService $invoiceService,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse {
        if (! $request->filled('order_id')) {
            return $this->storeCatalogInvoice($request, $invoiceService, $invoiceLifecycleService);
        }

        $invoice = Invoice::query()->create($this->validated($request));
        $invoiceLifecycleService->handleStatusChange($invoice);
        $sent = $invoiceService->sendInvoice($invoice->load('order.product'));

        return redirect()
            ->route('admin.invoices.index')
            ->with(
                $sent ? 'status' : 'warning',
                $sent
                    ? 'Invoice created and emailed with PDF attachment.'
                    : 'Invoice created, but the email could not be sent. Check mail configuration.'
            );
    }

    private function storeCatalogInvoice(
        Request $request,
        InvoiceService $invoiceService,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'customer'))],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'catalog_item_key' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'paper_density' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'delivery_method' => ['nullable', 'string', 'max:255'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'invoice_status' => ['nullable', 'string', Rule::in($this->allowedInvoiceStatuses())],
            'due_at' => ['nullable', 'date'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $catalogItem = $this->resolveCatalogItem((string) $validated['catalog_item_key']);

        if (! $catalogItem) {
            throw ValidationException::withMessages([
                'catalog_item_key' => 'Select a valid Printbuka product or service.',
            ]);
        }

        $customer = null;

        if (filled($validated['customer_id'] ?? null)) {
            $customer = User::query()->where('role', 'customer')->find($validated['customer_id']);
        }

        $validated['tax_amount'] = (float) ($validated['tax_amount'] ?? 0);
        $validated['discount_amount'] = (float) ($validated['discount_amount'] ?? 0);

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $catalogItem['unit_price'];
        $productPricing = null;

        if (($catalogItem['source_type'] ?? null) === 'product' && filled($catalogItem['product_id'])) {
            $product = Product::query()->find((int) $catalogItem['product_id']);
            if ($product) {
                $productPricing = $this->productPricingForInvoice($product, $validated, $quantity);
                $unitPrice = (float) ($productPricing['effective_unit_price'] ?? $unitPrice);
            }
        }

        $subtotal = (float) ($productPricing['subtotal'] ?? ($quantity * $unitPrice));
        $total = max(0, $subtotal + $validated['tax_amount'] - $validated['discount_amount']);
        $invoiceStatus = strtolower((string) ($validated['invoice_status'] ?? 'unpaid'));

        $lineItems = (array) ($productPricing['line_items'] ?? [[
            'description' => (string) $catalogItem['name'],
            'quantity' => $quantity,
            'rate' => $unitPrice,
            'amount' => $subtotal,
        ]]);

        $order = null;
        $invoice = null;

        DB::transaction(function () use (&$order, &$invoice, $validated, $customer, $catalogItem, $quantity, $unitPrice, $subtotal, $total, $invoiceStatus, $lineItems, $request, $productPricing): void {
            $order = Order::query()->create([
                'product_id' => $catalogItem['product_id'],
                'user_id' => $customer?->id,
                'created_by_admin_id' => $request->user()?->id,
                'service_type' => (string) $catalogItem['service_type'],
                'channel' => 'Manual',
                'job_type' => (string) $catalogItem['name'],
                'size_format' => $productPricing['selected_options']['size_format'] ?? ($validated['size_format'] ?? null),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $subtotal,
                'customer_name' => $customer?->displayName() ?? $validated['customer_name'],
                'customer_email' => $customer?->email ?? $validated['customer_email'],
                'customer_phone' => $customer?->phone ?? $validated['customer_phone'],
                'delivery_city' => $validated['delivery_city'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'artwork_notes' => $validated['artwork_notes'] ?? null,
                'material_substrate' => $productPricing['selected_options']['material_substrate'] ?? ($validated['material_substrate'] ?? null),
                'paper_density' => $productPricing['selected_options']['paper_density'] ?? ($validated['paper_density'] ?? null),
                'finish_lamination' => $productPricing['selected_options']['finish_lamination'] ?? ($validated['finish_lamination'] ?? null),
                'delivery_method' => $productPricing['selected_options']['delivery_method'] ?? ($validated['delivery_method'] ?? null),
                'status' => 'Analyzing Job Brief',
                'job_order_number' => ReferenceCode::jobOrderNumber((string) $catalogItem['service_type']),
                'priority' => '🟡 Normal',
                'brief_received_by_id' => $request->user()?->id,
                'brief_received_at' => now(),
                'assigned_designer_id' => Order::autoAssignableDesignerId(),
                'payment_status' => 'Invoice Issued',
                'internal_notes' => $validated['internal_notes'] ?? null,
                'pricing_breakdown' => [
                    'catalog_item_key' => (string) $validated['catalog_item_key'],
                    'line_items' => $lineItems,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'selected_options' => $productPricing['selected_options'] ?? [],
                    'option_prices' => $productPricing['option_prices'] ?? [],
                    'delivery_option_price' => (float) ($productPricing['delivery_option_price'] ?? 0),
                    'effective_unit_price' => (float) ($productPricing['effective_unit_price'] ?? $unitPrice),
                    'subtotal' => $subtotal,
                    'tax_amount' => $validated['tax_amount'],
                    'discount_amount' => $validated['discount_amount'],
                    'total' => $total,
                ],
            ]);

            $invoice = Invoice::query()->create([
                'order_id' => $order->id,
                'invoice_number' => ReferenceCode::invoiceNumber(),
                'subtotal' => $subtotal,
                'tax_amount' => $validated['tax_amount'],
                'discount_amount' => $validated['discount_amount'],
                'total_amount' => $total,
                'status' => $invoiceStatus,
                'issued_at' => now(),
                'due_at' => $validated['due_at'] ?? now()->addDays(7),
                'sent_at' => null,
            ]);
        });

        if ($invoiceStatus === 'paid') {
            $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), 'unpaid');
        }

        $shouldSend = $request->boolean('send_email');
        $sent = false;

        if ($shouldSend) {
            $sent = $invoiceService->sendInvoice($invoice->load('order.product'));
        }

        $baseMessage = $invoiceStatus === 'paid'
            ? 'Invoice created and marked as paid.'
            : 'Invoice created successfully.';
        $sentMessage = $invoiceStatus === 'paid'
            ? 'Invoice created, marked as paid, and emailed successfully.'
            : 'Invoice created and emailed with PDF attachment.';
        $failedMessage = $invoiceStatus === 'paid'
            ? 'Invoice created and marked as paid, but email could not be sent.'
            : 'Invoice created, but the email could not be sent. Check mail configuration.';

        return redirect()
            ->route('admin.invoices.index')
            ->with(
                $sent || ! $shouldSend ? 'status' : 'warning',
                $sent
                    ? $sentMessage
                    : ($shouldSend ? $failedMessage : $baseMessage)
            );
    }

    /**
     * @return array{name:string,unit_price:float,service_type:string,product_id:int|null,source_type:string}|null
     */
    private function resolveCatalogItem(string $catalogItemKey): ?array
    {
        if (str_starts_with($catalogItemKey, 'product:')) {
            $id = (int) substr($catalogItemKey, 8);
            $product = Product::query()->where('is_active', true)->find($id);

            if (! $product) {
                return null;
            }

            return [
                'name' => (string) $product->name,
                'unit_price' => (float) $product->price,
                'service_type' => $this->serviceTypeForProduct($product),
                'product_id' => $product->id,
                'source_type' => 'product',
            ];
        }

        if (str_starts_with($catalogItemKey, 'service:')) {
            $slug = substr($catalogItemKey, 8);
            $service = ServiceCatalog::find($slug);

            if (! $service) {
                return null;
            }

            return [
                'name' => (string) ($service['name'] ?? str($slug)->replace('-', ' ')->title()),
                'unit_price' => ServiceCatalog::priceForSlug($slug),
                'service_type' => ServiceCatalog::serviceTypeForSlug($slug),
                'product_id' => null,
                'source_type' => 'service',
            ];
        }

        return null;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @return array<int, array<string, mixed>>
     */
    private function productOptionCatalog($products): array
    {
        return $products
            ->mapWithKeys(function (Product $product): array {
                $sizeOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'size_price_options', 'default_size_price_options', $product->paper_size);
                $materialOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'material_price_options', 'default_material_price_options', $product->paper_type);
                $densityOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'density_price_options', 'default_density_price_options', $product->paper_density);
                $finishOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'finish_price_options', 'default_finish_price_options', $product->finishing);
                $deliveryOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'delivery_price_options', 'default_delivery_price_options', 'Client Pickup');

                return [
                    $product->id => [
                        'base_price' => (float) $product->price,
                        'defaults' => [
                            'size_format' => $this->selectedOptionLabel($sizeOptions, null, $product->paper_size),
                            'material_substrate' => $this->selectedOptionLabel($materialOptions, null, $product->paper_type),
                            'paper_density' => $this->selectedOptionLabel($densityOptions, null, $product->paper_density),
                            'finish_lamination' => $this->selectedOptionLabel($finishOptions, null, $product->finishing),
                            'delivery_method' => $this->selectedOptionLabel($deliveryOptions, null, 'Client Pickup'),
                        ],
                        'options' => [
                            'size_format' => $sizeOptions,
                            'material_substrate' => $materialOptions,
                            'paper_density' => $densityOptions,
                            'finish_lamination' => $finishOptions,
                            'delivery_method' => $deliveryOptions,
                        ],
                    ],
                ];
            })
            ->all();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function productPricingForInvoice(Product $product, array $validated, int $quantity): array
    {
        $sizeOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'size_price_options', 'default_size_price_options', $product->paper_size);
        $materialOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'material_price_options', 'default_material_price_options', $product->paper_type);
        $densityOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'density_price_options', 'default_density_price_options', $product->paper_density);
        $finishOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'finish_price_options', 'default_finish_price_options', $product->finishing);
        $deliveryOptions = ProductOptionPricing::optionsForProductOrSetting($product, 'delivery_price_options', 'default_delivery_price_options', 'Client Pickup');

        $selectedOptions = [
            'size_format' => $this->selectedOptionLabel($sizeOptions, (string) ($validated['size_format'] ?? ''), $product->paper_size),
            'material_substrate' => $this->selectedOptionLabel($materialOptions, (string) ($validated['material_substrate'] ?? ''), $product->paper_type),
            'paper_density' => $this->selectedOptionLabel($densityOptions, (string) ($validated['paper_density'] ?? ''), $product->paper_density),
            'finish_lamination' => $this->selectedOptionLabel($finishOptions, (string) ($validated['finish_lamination'] ?? ''), $product->finishing),
            'delivery_method' => $this->selectedOptionLabel($deliveryOptions, (string) ($validated['delivery_method'] ?? ''), 'Client Pickup'),
        ];

        $optionPrices = [
            'size_price' => ProductOptionPricing::priceFromOptions($sizeOptions, $selectedOptions['size_format']),
            'material_price' => ProductOptionPricing::priceFromOptions($materialOptions, $selectedOptions['material_substrate']),
            'density_price' => ProductOptionPricing::priceFromOptions($densityOptions, $selectedOptions['paper_density']),
            'finish_price' => ProductOptionPricing::priceFromOptions($finishOptions, $selectedOptions['finish_lamination']),
        ];
        $deliveryOptionPrice = ProductOptionPricing::priceFromOptions($deliveryOptions, $selectedOptions['delivery_method']);
        $effectiveUnitPrice = (float) $product->price + array_sum($optionPrices);
        $productSubtotal = $quantity * $effectiveUnitPrice;
        $subtotal = $productSubtotal + $deliveryOptionPrice;

        $lineItems = [[
            'description' => $this->productDescriptionWithOptions((string) $product->name, $selectedOptions),
            'quantity' => $quantity,
            'rate' => $effectiveUnitPrice,
            'amount' => $productSubtotal,
        ]];

        if ($deliveryOptionPrice > 0) {
            $lineItems[] = [
                'description' => 'Delivery ('.$selectedOptions['delivery_method'].')',
                'quantity' => 1,
                'rate' => $deliveryOptionPrice,
                'amount' => $deliveryOptionPrice,
            ];
        }

        return [
            'selected_options' => $selectedOptions,
            'option_prices' => $optionPrices,
            'delivery_option_price' => $deliveryOptionPrice,
            'effective_unit_price' => $effectiveUnitPrice,
            'subtotal' => $subtotal,
            'line_items' => $lineItems,
        ];
    }

    /**
     * @param  array<int, array{label: string, price: float}>  $options
     */
    private function selectedOptionLabel(array $options, ?string $selected, ?string $fallback): ?string
    {
        $selected = filled($selected) ? trim((string) $selected) : null;
        $fallback = filled($fallback) ? trim((string) $fallback) : null;
        $labels = collect($options)->pluck('label')->filter()->values();

        if ($selected !== null && $labels->contains($selected)) {
            return $selected;
        }

        if ($fallback !== null && $labels->contains($fallback)) {
            return $fallback;
        }

        return $labels->first();
    }

    /**
     * @param  array<string, string|null>  $selectedOptions
     */
    private function productDescriptionWithOptions(string $productName, array $selectedOptions): string
    {
        $parts = collect([
            filled($selectedOptions['size_format'] ?? null) ? 'Size: '.$selectedOptions['size_format'] : null,
            filled($selectedOptions['material_substrate'] ?? null) ? 'Material: '.$selectedOptions['material_substrate'] : null,
            filled($selectedOptions['paper_density'] ?? null) ? 'Density: '.$selectedOptions['paper_density'] : null,
            filled($selectedOptions['finish_lamination'] ?? null) ? 'Finish: '.$selectedOptions['finish_lamination'] : null,
        ])->filter()->values()->all();

        if ($parts === []) {
            return $productName;
        }

        return $productName.' ('.implode(', ', $parts).')';
    }

    private function serviceTypeForProduct(Product $product): string
    {
        if (filled($product->service_type)) {
            return (string) $product->service_type;
        }

        $value = strtolower($product->name.' '.$product->short_description.' '.$product->description);

        if (
            str_contains($value, 'gift')
            || str_contains($value, 'mug')
            || str_contains($value, 'shirt')
            || str_contains($value, 'tote')
        ) {
            return 'gift';
        }

        return 'print';
    }

    public function edit(Invoice $invoice): View
    {
        return view('admin.invoices.form', [
            'invoice' => $invoice,
            'orders' => Order::query()->latest()->get(),
            'invoiceStatuses' => $this->allowedInvoiceStatuses(),
        ]);
    }

    public function update(
        Request $request,
        Invoice $invoice,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse {
        $previousStatus = (string) $invoice->status;
        $invoice->update($this->validated($request, $invoice));
        $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), $previousStatus);

        return redirect()->route('admin.invoices.index')->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('status', 'Invoice deleted.');
    }

    public function markAsPaid(Invoice $invoice, InvoiceLifecycleService $invoiceLifecycleService): RedirectResponse
    {
        if ((string) $invoice->status === 'paid') {
            return back()->with('status', $invoice->documentTypeLabel().' is already marked as paid.');
        }

        $previousStatus = (string) $invoice->status;

        $invoice->forceFill([
            'status' => 'paid',
        ])->save();

        $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), $previousStatus);

        return back()->with('status', $invoice->fresh('order')->documentTypeLabel().' marked as paid.');
    }

    public function storeQuotation(
        Request $request,
        InvoiceService $invoiceService,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'customer'))],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
            'job_type' => ['required', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1', 'required_without:line_items'],
            'unit_price' => ['nullable', 'numeric', 'min:0', 'required_without:line_items'],
            'line_items' => ['nullable', 'array', 'min:1', 'required_without:quantity'],
            'line_items.*.source_type' => ['nullable', 'string', Rule::in(['custom', 'product', 'service'])],
            'line_items.*.catalog_item_key' => ['nullable', 'string', 'max:255'],
            'line_items.*.description' => ['nullable', 'string', 'max:500'],
            'line_items.*.size' => ['nullable', 'string', 'max:255'],
            'line_items.*.color' => ['nullable', 'string', 'max:255'],
            'line_items.*.finishing' => ['nullable', 'string', 'max:255'],
            'line_items.*.quantity' => ['required_with:line_items', 'integer', 'min:1'],
            'line_items.*.rate' => ['required_with:line_items', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'invoice_status' => ['nullable', 'string', Rule::in($this->allowedInvoiceStatuses())],
            'due_at' => ['nullable', 'date'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $lineItemCatalogErrors = [];

        foreach ((array) ($validated['line_items'] ?? []) as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $sourceType = strtolower((string) ($item['source_type'] ?? 'custom'));

            if (! in_array($sourceType, ['product', 'service'], true)) {
                continue;
            }

            $catalogItemKey = trim((string) ($item['catalog_item_key'] ?? ''));

            if (
                $catalogItemKey === ''
                || ! str_starts_with($catalogItemKey, $sourceType.':')
                || ! $this->resolveCatalogItem($catalogItemKey)
            ) {
                $lineItemCatalogErrors['line_items.'.$index.'.catalog_item_key'] = 'Select a valid '.$sourceType.' from Printbuka catalog.';
            }
        }

        if ($lineItemCatalogErrors !== []) {
            throw ValidationException::withMessages($lineItemCatalogErrors);
        }

        $customer = null;

        if (filled($validated['customer_id'] ?? null)) {
            $customer = User::query()->where('role', 'customer')->find($validated['customer_id']);
        }

        $validated['tax_amount'] = (float) ($validated['tax_amount'] ?? 0);
        $validated['discount_amount'] = (float) ($validated['discount_amount'] ?? 0);
        $lineItems = $this->normalizedLineItems($validated);
        $subtotal = collect($lineItems)->sum(fn (array $item): float => (float) ($item['amount'] ?? 0));
        $quantity = max(1, (int) collect($lineItems)->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0)));
        $unitPrice = $quantity > 0 ? $subtotal / $quantity : 0;
        $total = max(0, $subtotal + $validated['tax_amount'] - $validated['discount_amount']);
        $invoiceStatus = strtolower((string) ($validated['invoice_status'] ?? 'unpaid'));

        $order = null;
        $invoice = null;

        DB::transaction(function () use (&$order, &$invoice, $validated, $customer, $subtotal, $total, $quantity, $unitPrice, $lineItems, $invoiceStatus, $request): void {
            $order = Order::query()->create([
                'product_id' => $validated['product_id'] ?? null,
                'user_id' => $customer?->id,
                'created_by_admin_id' => $request->user()?->id,
                'service_type' => 'quote',
                'channel' => 'Manual',
                'job_type' => $validated['job_type'],
                'size_format' => $validated['size_format'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $subtotal,
                'customer_name' => $customer?->displayName() ?? $validated['customer_name'],
                'customer_email' => $customer?->email ?? $validated['customer_email'],
                'customer_phone' => $customer?->phone ?? $validated['customer_phone'],
                'delivery_city' => $validated['delivery_city'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'artwork_notes' => $validated['artwork_notes'] ?? null,
                'status' => 'Quote Requested',
                'job_order_number' => ReferenceCode::jobOrderNumber('quote'),
                'priority' => '🟡 Normal',
                'brief_received_by_id' => $request->user()?->id,
                'brief_received_at' => now(),
                'assigned_designer_id' => Order::autoAssignableDesignerId(),
                'payment_status' => 'Awaiting Invoice',
                'internal_notes' => $validated['internal_notes'] ?? null,
                'pricing_breakdown' => [
                    'line_items' => $lineItems,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'tax_amount' => $validated['tax_amount'],
                    'discount_amount' => $validated['discount_amount'],
                    'total' => $total,
                ],
            ]);

            $invoice = Invoice::query()->create([
                'order_id' => $order->id,
                'invoice_number' => ReferenceCode::invoiceNumber(),
                'subtotal' => $subtotal,
                'tax_amount' => $validated['tax_amount'],
                'discount_amount' => $validated['discount_amount'],
                'total_amount' => $total,
                'status' => $invoiceStatus,
                'issued_at' => now(),
                'due_at' => $validated['due_at'] ?? now()->addDays(7),
                'sent_at' => null,
            ]);
        });

        if ($invoiceStatus === 'paid') {
            $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), 'unpaid');
        }

        $shouldSend = $request->boolean('send_email');
        $sent = false;

        if ($shouldSend) {
            $sent = $invoiceService->sendInvoice($invoice->load('order.product'));
        }

        $baseMessage = $invoiceStatus === 'paid'
            ? 'Quotation created and marked as paid.'
            : 'Quotation created successfully.';
        $sentMessage = $invoiceStatus === 'paid'
            ? 'Quotation created, marked as paid, and emailed successfully.'
            : 'Quotation created and emailed successfully.';
        $failedMessage = $invoiceStatus === 'paid'
            ? 'Quotation created and marked as paid, but email could not be sent.'
            : 'Quotation created, but email could not be sent.';

        return redirect()
            ->route('admin.invoices.index')
            ->with(
                $sent || ! $shouldSend ? 'status' : 'warning',
                $sent
                    ? $sentMessage
                    : ($shouldSend ? $failedMessage : $baseMessage)
            );
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, array<string, mixed>>
     */
    private function normalizedLineItems(array $validated): array
    {
        $items = collect($validated['line_items'] ?? [])
            ->filter(fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                $sourceType = strtolower((string) ($item['source_type'] ?? 'custom'));

                if (! in_array($sourceType, ['custom', 'product', 'service'], true)) {
                    $sourceType = 'custom';
                }

                $catalogItemKey = trim((string) ($item['catalog_item_key'] ?? ''));
                $catalogItem = null;

                if (
                    in_array($sourceType, ['product', 'service'], true)
                    && $catalogItemKey !== ''
                    && str_starts_with($catalogItemKey, $sourceType.':')
                ) {
                    $catalogItem = $this->resolveCatalogItem($catalogItemKey);
                }

                $description = trim((string) ($item['description'] ?? ''));
                $quantity = max(1, (int) ($item['quantity'] ?? 0));
                $rate = max(0, (float) ($item['rate'] ?? 0));
                $size = trim((string) ($item['size'] ?? ''));
                $color = trim((string) ($item['color'] ?? ''));
                $finishing = trim((string) ($item['finishing'] ?? ''));

                if ($catalogItem && $rate <= 0) {
                    $rate = max(0, (float) ($catalogItem['unit_price'] ?? 0));
                }

                if ($description === '') {
                    $description = (string) ($catalogItem['name'] ?? 'Custom item');
                }

                $specParts = collect([
                    $size !== '' ? 'Size: '.$size : null,
                    $color !== '' ? 'Color: '.$color : null,
                    $finishing !== '' ? 'Finishing: '.$finishing : null,
                ])->filter()->values()->all();

                if ($specParts !== []) {
                    $description .= ' ('.implode(', ', $specParts).')';
                }

                return [
                    'source_type' => $sourceType,
                    'catalog_item_key' => $catalogItemKey !== '' ? $catalogItemKey : null,
                    'description' => $description,
                    'size' => $size !== '' ? $size : null,
                    'color' => $color !== '' ? $color : null,
                    'finishing' => $finishing !== '' ? $finishing : null,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $quantity * $rate,
                ];
            })
            ->filter(fn (array $item): bool => $item['description'] !== '')
            ->values();

        if ($items->isNotEmpty()) {
            return $items->all();
        }

        $quantity = max(1, (int) ($validated['quantity'] ?? 1));
        $rate = max(0, (float) ($validated['unit_price'] ?? 0));

        return [[
            'description' => (string) ($validated['job_type'] ?? 'Custom item'),
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $quantity * $rate,
        ]];
    }

    private function validated(Request $request, ?Invoice $invoice = null): array
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'invoice_number' => ['nullable', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($invoice?->id)],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in($this->allowedInvoiceStatuses())],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
        ]);

        $validated['tax_amount'] ??= 0;
        $validated['discount_amount'] ??= 0;
        $validated['invoice_number'] = $validated['invoice_number'] ?: ($invoice?->invoice_number ?: ReferenceCode::invoiceNumber());
        $validated['status'] = strtolower((string) $validated['status']);

        return $validated;
    }
}
