<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\ProductOptionPricing;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('category')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true, 'moq' => 1, 'price' => 0]),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'optionLines' => $this->optionLines(new Product),
            ...$this->paperAttributeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::query()->create($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'optionLines' => $this->optionLines($product),
            ...$this->paperAttributeOptions($product),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'moq' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'paper_type' => ['required', 'string', 'max:255'],
            'paper_size' => ['required', 'string', 'max:255'],
            'finishing' => ['required', 'string', 'max:255'],
            'paper_density' => ['required', 'string', 'max:255'],
            'size_price_options' => ['nullable', 'string'],
            'material_price_options' => ['nullable', 'string'],
            'finish_price_options' => ['nullable', 'string'],
            'delivery_price_options' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['size_price_options'] = ProductOptionPricing::parseLines($request->input('size_price_options'));
        $validated['material_price_options'] = ProductOptionPricing::parseLines($request->input('material_price_options'));
        $validated['finish_price_options'] = ProductOptionPricing::parseLines($request->input('finish_price_options'));
        $validated['delivery_price_options'] = ProductOptionPricing::parseLines($request->input('delivery_price_options'));

        return $validated;
    }

    private function optionLines(Product $product): array
    {
        return [
            'size_price_options' => ProductOptionPricing::toLines($product->size_price_options),
            'material_price_options' => ProductOptionPricing::toLines($product->material_price_options),
            'finish_price_options' => ProductOptionPricing::toLines($product->finish_price_options),
            'delivery_price_options' => ProductOptionPricing::toLines($product->delivery_price_options),
        ];
    }

    private function paperAttributeOptions(?Product $product = null): array
    {
        $settings = SiteSettings::all();

        return [
            'paperTypeOptions' => $this->settingList($settings['paper_types'] ?? '', config('printbuka_admin.materials', []), $product?->paper_type),
            'paperSizeOptions' => $this->settingList($settings['paper_sizes'] ?? '', config('printbuka_admin.sizes', []), $product?->paper_size),
            'finishingOptions' => $this->settingList($settings['finishings'] ?? '', config('printbuka_admin.finishes', []), $product?->finishing),
            'paperDensityOptions' => $this->settingList($settings['paper_densities'] ?? '', [
                '100gsm',
                '115gsm',
                '150gsm',
                '170gsm',
                '200gsm',
                '250gsm',
                '300gsm',
                '350gsm',
                'Self Adhesive',
                'Gift Item',
                'Custom',
            ], $product?->paper_density),
        ];
    }

    private function settingList(mixed $settingValue, array $fallback, ?string $currentValue = null): array
    {
        $options = collect(preg_split('/[\r\n,]+/', is_string($settingValue) ? $settingValue : ''))
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => $item !== '')
            ->values()
            ->all();

        if ($options === []) {
            $options = collect($fallback)
                ->map(fn (mixed $item): string => trim((string) $item))
                ->filter(fn (string $item): bool => $item !== '')
                ->values()
                ->all();
        }

        if (filled($currentValue) && ! in_array($currentValue, $options, true)) {
            $options[] = $currentValue;
        }

        return collect($options)->unique()->values()->all();
    }
}
