<?php

namespace App\Livewire\Services;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderFulfillmentService;
use App\Services\PaystackService;
use App\Support\ExternalAssetLinks;
use App\Support\ProductOptionPricing;
use App\Support\ReferenceCode;
use App\Support\ServiceCatalog;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class DtfOrderForm extends Component
{
    use WithFileUploads;

    private const ALLOWED_FILM_SIZES = ['A2', 'A3', 'A4', 'A5', 'A6'];

    public array $service = [];

    public array $filmSizeOptions = [];

    public int $quantity = 1;

    public string $material = 'Film';

    public ?string $film_size = null;

    public string $has_design = 'yes';

    public mixed $design_file = null;

    public ?string $asset_drive_links = null;

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

        $configuredSizeOptions = ProductOptionPricing::parseLines((string) SiteSettings::get('service_dtf_size_price_options', ''));

        $this->filmSizeOptions = collect($configuredSizeOptions)
            ->map(fn (array $option): array => [
                'label' => strtoupper((string) ($option['label'] ?? '')),
                'price' => (float) ($option['price'] ?? 0),
            ])
            ->filter(fn (array $option): bool => in_array($option['label'], self::ALLOWED_FILM_SIZES, true))
            ->values()
            ->all();

        if ($this->filmSizeOptions === []) {
            $this->filmSizeOptions = collect(self::ALLOWED_FILM_SIZES)
                ->map(fn (string $label): array => ['label' => $label, 'price' => 0])
                ->all();
        }

        $this->film_size = $this->filmSizeOptions[0]['label'] ?? 'A4';

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
        return ServiceCatalog::priceForSlug('dtf');
    }

    public function getFilmSizePriceProperty(): float
    {
        return ProductOptionPricing::priceFromOptions($this->filmSizeOptions, $this->film_size);
    }

    public function getDesignPriceProperty(): float
    {
        if ($this->has_design === 'yes') {
            return 0;
        }

        return max(0, (float) SiteSettings::get('service_price_dtf_design', 0));
    }

    public function getDeliveryPriceProperty(): float
    {
        if ($this->delivery_type !== 'delivery') {
            return 0;
        }

        return max(0, (float) SiteSettings::get('service_price_dtf_delivery', 0));
    }

    public function getUnitPriceProperty(): float
    {
        return $this->basePrice + $this->filmSizePrice;
    }

    public function getEstimatedTotalProperty(): float
    {
        $quantity = max(1, (int) $this->quantity);

        return ($quantity * $this->unitPrice) + $this->designPrice + $this->deliveryPrice;
    }

    public function submit(
        InvoiceService $invoiceService,
        PaystackService $paystackService,
        OrderFulfillmentService $orderFulfillmentService
    )
    {
        $sizeLabels = collect($this->filmSizeOptions)->pluck('label')->map(fn ($label): string => (string) $label)->all();

        $validated = $this->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'film_size' => ['required', Rule::in($sizeLabels)],
            'has_design' => ['required', Rule::in(['yes', 'no'])],
            'design_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:5120'],
            'asset_drive_links' => ['nullable', 'string', 'max:4000'],
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

        $invalidLinks = ExternalAssetLinks::invalidLinks($validated['asset_drive_links'] ?? null);

        if ($invalidLinks !== []) {
            $this->addError('asset_drive_links', 'Use valid external links from Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, or Mega.');

            return;
        }

        if ($validated['has_design'] === 'yes' && ! $this->design_file && ! filled($validated['asset_drive_links'] ?? null)) {
            $this->addError('design_file', 'Upload an image file or provide external drive links for non-image files.');
            $this->addError('asset_drive_links', 'Provide external links if your artwork is in PDF, SVG, or ZIP format.');

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

        $artworkNotes = $validated['has_design'] === 'no'
            ? (string) $validated['design_brief']
            : 'Customer uploaded design/artwork image file.';

        $artworkNotes = ExternalAssetLinks::appendToNotes($artworkNotes, $validated['asset_drive_links'] ?? null);

        $order = Order::query()->create([
            'product_id' => null,
            'user_id' => Auth::id(),
            'service_type' => ServiceCatalog::serviceTypeForSlug('dtf'),
            'channel' => 'Online',
            'job_type' => (string) ($this->service['name'] ?? 'DTF'),
            'job_order_number' => ReferenceCode::jobOrderNumber('print'),
            'quantity' => (int) $validated['quantity'],
            'unit_price' => $this->unitPrice,
            'total_price' => $this->estimatedTotal,
            'priority' => '🟡 Normal',
            'brief_received_at' => now(),
            'estimated_delivery_at' => $orderFulfillmentService->estimateForNewOrder(false, now()),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'delivery_city' => $validated['delivery_city'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_method' => $validated['delivery_type'] === 'pickup' ? 'Client Pickup' : 'Delivery Address',
            'material_substrate' => $this->material,
            'size_format' => $validated['film_size'],
            'artwork_notes' => $artworkNotes,
            'job_image_assets' => $assets,
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
            'pricing_breakdown' => [
                'base_price' => $this->basePrice,
                'film_size_price' => $this->filmSizePrice,
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
            'service_slug' => 'dtf',
            'order_form' => 'dtf_livewire',
        ]);

        if (($paymentInit['ok'] ?? false) && filled($paymentInit['authorization_url'] ?? null)) {
            return redirect()->away((string) $paymentInit['authorization_url']);
        }

        return redirect()->route('services.orders.success', [
            'service' => 'dtf',
            'order' => $order,
        ])->with(
            'warning',
            $paymentInit['message'] ?? 'Order submitted, but Paystack redirect is unavailable right now.'
        );
    }

    public function render()
    {
        return view('livewire.services.dtf-order-form');
    }
}
