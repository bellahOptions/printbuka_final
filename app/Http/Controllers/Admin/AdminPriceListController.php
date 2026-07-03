<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Support\PriceList;
use App\Support\ProductOptionPricing;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPriceListController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const PRODUCT_OPTION_GROUPS = ['size', 'material', 'finish', 'density', 'delivery'];

    public function index(): View
    {
        return view('admin.pricelist.index', [
            'surcharges' => [
                'express_order_surcharge' => PriceList::surcharge('express_order_surcharge') ?? 5000,
                'sample_order_surcharge' => PriceList::surcharge('sample_order_surcharge') ?? 5000,
            ],
        ]);
    }

    public function editDefaults(): View
    {
        return view('admin.pricelist.defaults-edit', [
            'optionLines' => collect(self::PRODUCT_OPTION_GROUPS)
                ->mapWithKeys(fn (string $group): array => [$group => ProductOptionPricing::toLines(PriceList::productDefaultOptions($group))])
                ->all(),
        ]);
    }

    public function updateDefaults(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'size_options' => ['nullable', 'string', 'max:12000'],
            'material_options' => ['nullable', 'string', 'max:12000'],
            'finish_options' => ['nullable', 'string', 'max:12000'],
            'density_options' => ['nullable', 'string', 'max:12000'],
            'delivery_options' => ['nullable', 'string', 'max:12000'],
        ]);

        foreach (self::PRODUCT_OPTION_GROUPS as $group) {
            $lines = ProductOptionPricing::parseLines($validated[$group.'_options'] ?? '');
            $this->syncOptionRows('product', null, $group, $lines, (int) $request->user()->id);
        }

        return back()->with('status', 'Default pricing updated.');
    }

    public function updateSurcharges(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'express_order_surcharge' => ['required', 'numeric', 'min:0'],
            'sample_order_surcharge' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated as $key => $price) {
            PriceListItem::query()->updateOrCreate(
                ['category' => 'surcharge', 'component_key' => $key],
                [
                    'label' => str($key)->replace('_', ' ')->title()->toString(),
                    'price' => $price,
                    'is_active' => true,
                    'updated_by_id' => $request->user()->id,
                ]
            );
        }

        return back()->with('status', 'Surcharges updated.');
    }

    /**
     * @var array<string, string>
     */
    private const PRODUCT_OPTION_COLUMNS = [
        'size' => 'size_price_options',
        'material' => 'material_price_options',
        'finish' => 'finish_price_options',
        'density' => 'density_price_options',
        'delivery' => 'delivery_price_options',
    ];

    public function editProduct(Product $product): View
    {
        return view('admin.pricelist.product-edit', [
            'product' => $product,
            'optionLines' => collect(self::PRODUCT_OPTION_COLUMNS)
                ->mapWithKeys(fn (string $column, string $group): array => [
                    $group => ProductOptionPricing::toLines($product->{$column} ?: PriceList::productDefaultOptions($group)),
                ])
                ->all(),
        ]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'size_options' => ['nullable', 'string', 'max:12000'],
            'material_options' => ['nullable', 'string', 'max:12000'],
            'finish_options' => ['nullable', 'string', 'max:12000'],
            'density_options' => ['nullable', 'string', 'max:12000'],
            'delivery_options' => ['nullable', 'string', 'max:12000'],
        ]);

        $updates = ['price' => $validated['price']];

        foreach (self::PRODUCT_OPTION_COLUMNS as $group => $column) {
            $updates[$column] = ProductOptionPricing::parseLines($validated[$group.'_options'] ?? '');
        }

        $product->update($updates);

        return redirect()->route('admin.pricelist.products.edit', $product)->with('status', 'Pricing updated for '.$product->name.'.');
    }

    public function editService(string $slug): View
    {
        $service = ServiceCatalog::find($slug);
        abort_if(! $service, 404);

        $hasFees = in_array($slug, ['direct-image-printing', 'dtf'], true);

        return view('admin.pricelist.service-edit', [
            'slug' => $slug,
            'service' => $service,
            'hasFees' => $hasFees,
            'hasSizeOptions' => $slug === 'dtf',
            'designFee' => $hasFees ? ServiceCatalog::feeForSlug($slug, 'design_fee') : null,
            'deliveryFee' => $hasFees ? ServiceCatalog::feeForSlug($slug, 'delivery_fee') : null,
            'sizeOptionLines' => $slug === 'dtf' ? ProductOptionPricing::toLines(ServiceCatalog::optionsForSlug('dtf', 'size')) : null,
        ]);
    }

    public function updateService(Request $request, string $slug): RedirectResponse
    {
        $service = ServiceCatalog::find($slug);
        abort_if(! $service, 404);

        $hasFees = in_array($slug, ['direct-image-printing', 'dtf'], true);
        $hasSizeOptions = $slug === 'dtf';

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'design_fee' => [$hasFees ? 'required' : 'nullable', 'numeric', 'min:0'],
            'delivery_fee' => [$hasFees ? 'required' : 'nullable', 'numeric', 'min:0'],
            'size_options' => ['nullable', 'string', 'max:12000'],
        ]);

        $updatedById = (int) $request->user()->id;

        PriceListItem::query()->updateOrCreate(
            ['category' => 'service', 'service_slug' => $slug, 'component_group' => null, 'component_key' => null],
            [
                'label' => (string) ($service['name'] ?? $slug),
                'price' => $validated['price'],
                'is_active' => true,
                'updated_by_id' => $updatedById,
            ]
        );

        if ($hasFees) {
            foreach (['design_fee', 'delivery_fee'] as $feeKey) {
                PriceListItem::query()->updateOrCreate(
                    ['category' => 'service', 'service_slug' => $slug, 'component_group' => 'fee', 'component_key' => $feeKey],
                    [
                        'label' => str($feeKey)->replace('_', ' ')->title()->toString(),
                        'price' => $validated[$feeKey],
                        'is_active' => true,
                        'updated_by_id' => $updatedById,
                    ]
                );
            }
        }

        if ($hasSizeOptions) {
            $lines = ProductOptionPricing::parseLines($validated['size_options'] ?? '');
            $this->syncOptionRows('service', $slug, 'size', $lines, $updatedById);
        }

        return redirect()->route('admin.pricelist.services.edit', $slug)->with('status', 'Pricing updated for '.($service['name'] ?? $slug).'.');
    }

    /**
     * @param  array<int, array{label: string, price: float}>  $lines
     */
    private function syncOptionRows(string $category, ?string $serviceSlug, string $componentGroup, array $lines, int $updatedById): void
    {
        $submittedLabels = collect($lines)->pluck('label')->all();

        $deleteQuery = PriceListItem::query()
            ->where('category', $category)
            ->where('component_group', $componentGroup);

        $category === 'product' ? $deleteQuery->whereNull('product_id') : $deleteQuery->where('service_slug', $serviceSlug);

        if ($submittedLabels === []) {
            $deleteQuery->delete();

            return;
        }

        $deleteQuery->whereNotIn('label', $submittedLabels)->delete();

        foreach ($lines as $index => $line) {
            PriceListItem::query()->updateOrCreate(
                [
                    'category' => $category,
                    'product_id' => null,
                    'service_slug' => $serviceSlug,
                    'component_group' => $componentGroup,
                    'label' => $line['label'],
                ],
                [
                    'price' => $line['price'],
                    'sort_order' => $index,
                    'is_active' => true,
                    'updated_by_id' => $updatedById,
                ]
            );
        }
    }
}
