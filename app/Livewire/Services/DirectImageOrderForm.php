<?php

namespace App\Livewire\Services;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\PaystackService;
use App\Support\ProductOptionPricing;
use App\Support\ReferenceCode;
use App\Support\ServiceCatalog;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class DirectImageOrderForm extends Component
{
    use WithFileUploads;

    public array $service = [];

    public array $paperTypeOptions = [];

    public array $paperSizeOptions = [];

    public int $quantity = 1;

    public ?string $paper_type = null;

    public ?string $paper_size = null;

    public string $has_design = 'yes';

    public mixed $design_file = null;

    public ?string $design_brief = null;

    public string $delivery_type = 'pickup';

    public ?int $delivery_address_id = null;

    public ?string $delivery_city = null;

    public ?string $delivery_address = null;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public array $savedDeliveryAddresses = [];

    public function mount(array $service): void
    {
        abort_unless(Auth::check(), 403);

        $this->service = $service;

        $paperTypeOptions = ProductOptionPricing::parseLines((string) SiteSettings::get('default_material_price_options', ''));
        $paperSizeOptions = ProductOptionPricing::parseLines((string) SiteSettings::get('default_size_price_options', ''));

        $this->paperTypeOptions = $paperTypeOptions !== []
            ? $paperTypeOptions
            : collect(preg_split('/\r\n|\r|\n/', (string) SiteSettings::get('paper_types', '')))
                ->map(fn (string $label): string => trim($label))
                ->filter()
                ->map(fn (string $label): array => ['label' => $label, 'price' => 0])
                ->values()
                ->all();

        $this->paperSizeOptions = $paperSizeOptions !== []
            ? $paperSizeOptions
            : collect(preg_split('/\r\n|\r|\n/', (string) SiteSettings::get('paper_sizes', '')))
                ->map(fn (string $label): string => trim($label))
                ->filter()
                ->map(fn (string $label): array => ['label' => $label, 'price' => 0])
                ->values()
                ->all();

        $this->paper_type = $this->paperTypeOptions[0]['label'] ?? null;
        $this->paper_size = $this->paperSizeOptions[0]['label'] ?? null;

        $customer = Auth::user();

        $this->customer_name = $customer?->displayName() ?? '';
        $this->customer_email = (string) ($customer?->email ?? '');
        $this->customer_phone = (string) ($customer?->phone ?? '');

        $addresses = $customer?->deliveryAddresses()->get() ?? collect();

        $this->savedDeliveryAddresses = $addresses
            ->map(fn ($address): array => [
                'id' => $address->id,
                'label' => $address->label,
                'city' => $address->city,
                'address' => $address->address,
                'is_default' => (bool) $address->is_default,
            ])
            ->values()
            ->all();

        $defaultAddress = collect($this->savedDeliveryAddresses)->firstWhere('is_default', true)
            ?? $this->savedDeliveryAddresses[0]
            ?? null;

        if (is_array($defaultAddress)) {
            $this->delivery_address_id = (int) $defaultAddress['id'];
            $this->delivery_city = (string) $defaultAddress['city'];
            $this->delivery_address = (string) $defaultAddress['address'];
        }
    }

    public function updatedDeliveryType(string $value): void
    {
        if ($value === 'pickup') {
            $this->delivery_address_id = null;
            $this->delivery_city = null;
            $this->delivery_address = null;
        }

        if ($value === 'delivery' && $this->delivery_address_id) {
            $this->applySavedAddress((int) $this->delivery_address_id);
        }
    }

    public function updatedDeliveryAddressId(?int $addressId): void
    {
        if ($this->delivery_type !== 'delivery' || ! $addressId) {
            return;
        }

        $this->applySavedAddress((int) $addressId);
    }

    private function applySavedAddress(int $addressId): void
    {
        $address = collect($this->savedDeliveryAddresses)->firstWhere('id', $addressId);

        if (! is_array($address)) {
            return;
        }

        $this->delivery_city = (string) ($address['city'] ?? '');
        $this->delivery_address = (string) ($address['address'] ?? '');
    }

    public function getBasePriceProperty(): float
    {
        return ServiceCatalog::priceForSlug('direct-image-printing');
    }

    public function getPaperTypePriceProperty(): float
    {
        return ProductOptionPricing::priceFromOptions($this->paperTypeOptions, $this->paper_type);
    }

    public function getPaperSizePriceProperty(): float
    {
        return ProductOptionPricing::priceFromOptions($this->paperSizeOptions, $this->paper_size);
    }

    public function getDesignPriceProperty(): float
    {
        if ($this->has_design === 'yes') {
            return 0;
        }

        return max(0, (float) SiteSettings::get('service_price_direct_image_printing_design', 0));
    }

    public function getDeliveryPriceProperty(): float
    {
        if ($this->delivery_type !== 'delivery') {
            return 0;
        }

        return max(0, (float) SiteSettings::get('service_price_direct_image_printing_delivery', 0));
    }

    public function getUnitPriceProperty(): float
    {
        return $this->basePrice + $this->paperTypePrice + $this->paperSizePrice;
    }

    public function getEstimatedTotalProperty(): float
    {
        $quantity = max(1, (int) $this->quantity);

        return ($quantity * $this->unitPrice) + $this->designPrice + $this->deliveryPrice;
    }

    public function submit(InvoiceService $invoiceService, PaystackService $paystackService)
    {
        $typeLabels = collect($this->paperTypeOptions)->pluck('label')->map(fn ($label): string => (string) $label)->all();
        $sizeLabels = collect($this->paperSizeOptions)->pluck('label')->map(fn ($label): string => (string) $label)->all();

        $validated = $this->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'paper_type' => ['required', Rule::in($typeLabels)],
            'paper_size' => ['required', Rule::in($sizeLabels)],
            'has_design' => ['required', Rule::in(['yes', 'no'])],
            'design_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,svg,zip', 'max:20480'],
            'design_brief' => ['nullable', 'string', 'max:3000'],
            'delivery_type' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_address_id' => [
                'nullable',
                Rule::exists('delivery_addresses', 'id')->where(fn ($query) => $query->where('user_id', (int) Auth::id())),
            ],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
        ]);

        if ($validated['has_design'] === 'yes' && ! $this->design_file) {
            $this->addError('design_file', 'Please upload your design/artwork file.');

            return;
        }

        if ($validated['has_design'] === 'no' && ! filled($validated['design_brief'] ?? null)) {
            $this->addError('design_brief', 'Please provide a design brief.');

            return;
        }

        if ($validated['delivery_type'] === 'delivery') {
            if (! filled($validated['delivery_city'] ?? null)) {
                $this->addError('delivery_city', 'Delivery city is required.');

                return;
            }

            if (! filled($validated['delivery_address'] ?? null)) {
                $this->addError('delivery_address', 'Delivery address is required.');

                return;
            }
        }

        if ($validated['delivery_type'] === 'pickup') {
            $validated['delivery_city'] = null;
            $validated['delivery_address'] = null;
        }

        $assets = [];
        if ($this->design_file) {
            $assets[] = [
                'path' => $this->design_file->store('job-assets', 'public'),
                'name' => $this->design_file->getClientOriginalName(),
                'mime' => $this->design_file->getClientMimeType(),
                'size' => $this->design_file->getSize(),
                'uploaded_at' => now()->toISOString(),
            ];
        }

        $order = Order::query()->create([
            'product_id' => null,
            'user_id' => Auth::id(),
            'service_type' => ServiceCatalog::serviceTypeForSlug('direct-image-printing'),
            'channel' => 'Online',
            'job_type' => (string) ($this->service['name'] ?? 'Direct Image Printing'),
            'job_order_number' => ReferenceCode::jobOrderNumber('print'),
            'quantity' => (int) $validated['quantity'],
            'unit_price' => $this->unitPrice,
            'total_price' => $this->estimatedTotal,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'delivery_city' => $validated['delivery_city'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_method' => $validated['delivery_type'] === 'pickup' ? 'Client Pickup' : 'Delivery Address',
            'material_substrate' => $validated['paper_type'],
            'size_format' => $validated['paper_size'],
            'artwork_notes' => $validated['has_design'] === 'no'
                ? (string) $validated['design_brief']
                : 'Customer uploaded design/artwork file.',
            'job_image_assets' => $assets,
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
            'pricing_breakdown' => [
                'base_price' => $this->basePrice,
                'paper_type_price' => $this->paperTypePrice,
                'paper_size_price' => $this->paperSizePrice,
                'unit_price' => $this->unitPrice,
                'quantity' => (int) $validated['quantity'],
                'design_fee' => $this->designPrice,
                'delivery_fee' => $this->deliveryPrice,
                'total' => $this->estimatedTotal,
            ],
        ]);

        $invoice = $invoiceService->createForOrder($order);
        $invoiceService->sendInvoice($invoice);

        session()->put('tracked_orders.'.$order->id, true);

        $paymentInit = $paystackService->initializeForInvoice($invoice, [
            'payment_context' => 'service_order',
            'service_slug' => 'direct-image-printing',
            'order_form' => 'direct_image_livewire',
        ]);

        if (($paymentInit['ok'] ?? false) && filled($paymentInit['authorization_url'] ?? null)) {
            return redirect()->away((string) $paymentInit['authorization_url']);
        }

        return redirect()->route('services.orders.success', [
            'service' => 'direct-image-printing',
            'order' => $order,
        ])->with(
            'warning',
            $paymentInit['message'] ?? 'Order submitted, but Paystack redirect is unavailable right now.'
        );
    }

    public function render()
    {
        return view('livewire.services.direct-image-order-form');
    }
}
