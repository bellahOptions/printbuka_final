<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\LivewireSecureUploads;
use App\Support\ProductOptionPricing;
use App\Support\ServiceCatalog;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'product' => new Product(['is_active' => true, 'moq' => 1, 'price' => 0, 'service_type' => 'print']),
            'categories' => ProductCategory::query()->with('parent')->orderBy('name')->get(),
            'serviceOptions' => $this->serviceOptions(),
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
            'categories' => ProductCategory::query()->with('parent')->orderBy('name')->get(),
            'serviceOptions' => $this->serviceOptions(),
            'optionLines' => $this->optionLines($product),
            ...$this->paperAttributeOptions($product),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteProductImages($product);
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'service_type' => ['required', 'string', Rule::in(array_keys($this->serviceOptions()))],
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
            'density_price_options' => ['nullable', 'string'],
            'delivery_price_options' => ['nullable', 'string'],
            'featured_image' => [
                'nullable',
                'file',
                'max:4096',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
            ],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'additional_images' => ['nullable', 'array', 'max:12'],
            'additional_images.*' => [
                'file',
                'max:4096',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
            ],
            'additional_image_paths' => ['nullable', 'array', 'max:12'],
            'additional_image_paths.*' => ['string', 'max:255'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'remove_additional_images' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['size_price_options'] = ProductOptionPricing::parseLines($request->input('size_price_options'));
        $validated['material_price_options'] = ProductOptionPricing::parseLines($request->input('material_price_options'));
        $validated['finish_price_options'] = ProductOptionPricing::parseLines($request->input('finish_price_options'));
        $validated['density_price_options'] = ProductOptionPricing::parseLines($request->input('density_price_options'));
        $validated['delivery_price_options'] = ProductOptionPricing::parseLines($request->input('delivery_price_options'));
        $imageUpdates = $this->syncProductImages($request, $product);
        unset(
            $validated['featured_image'],
            $validated['featured_image_path'],
            $validated['additional_images'],
            $validated['additional_image_paths'],
            $validated['remove_featured_image'],
            $validated['remove_additional_images']
        );

        return [
            ...$validated,
            ...$imageUpdates,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function syncProductImages(Request $request, ?Product $product = null): array
    {
        $updates = [];

        $removeFeaturedImage = $request->boolean('remove_featured_image');
        if ($product && $removeFeaturedImage && filled($product->featured_image)) {
            Storage::disk('public')->delete($product->featured_image);
            $updates['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($product && filled($product->featured_image)) {
                Storage::disk('public')->delete($product->featured_image);
            }

            $updates['featured_image'] = $request->file('featured_image')->store('product-images/featured', 'public');
        } elseif (filled($request->input('featured_image_path'))) {
            $livewireFeaturedPath = LivewireSecureUploads::consumePath(
                $request,
                (string) $request->input('featured_image_path'),
                ['product-images/featured']
            );

            if (! $livewireFeaturedPath) {
                throw ValidationException::withMessages([
                    'featured_image' => 'The uploaded featured image is invalid or expired. Please upload again.',
                ]);
            }

            if ($product && filled($product->featured_image)) {
                Storage::disk('public')->delete($product->featured_image);
            }

            $updates['featured_image'] = $livewireFeaturedPath;
        }

        $removeAdditionalImages = $request->boolean('remove_additional_images');
        if ($product && $removeAdditionalImages) {
            foreach ((array) $product->additional_images as $imagePath) {
                Storage::disk('public')->delete((string) $imagePath);
            }
            $updates['additional_images'] = [];
        }

        if ($request->hasFile('additional_images')) {
            $existingImages = ($product && ! $removeAdditionalImages)
                ? collect((array) $product->additional_images)
                : collect();

            $newImages = collect((array) $request->file('additional_images'))
                ->filter()
                ->map(fn ($file): string => $file->store('product-images/gallery', 'public'));

            $updates['additional_images'] = $existingImages
                ->merge($newImages)
                ->values()
                ->all();
        }

        $additionalImagePaths = collect((array) $request->input('additional_image_paths'))
            ->filter(fn ($path): bool => filled($path))
            ->map(fn ($path): string => (string) $path)
            ->values()
            ->all();

        if ($additionalImagePaths !== []) {
            $livewireAdditionalPaths = LivewireSecureUploads::consumePaths(
                $request,
                $additionalImagePaths,
                ['product-images/gallery']
            );

            if (count($livewireAdditionalPaths) !== count($additionalImagePaths)) {
                throw ValidationException::withMessages([
                    'additional_images' => 'One or more gallery images are invalid or expired. Please upload them again.',
                ]);
            }

            $baseImages = collect($updates['additional_images'] ?? (($product && ! $removeAdditionalImages) ? (array) $product->additional_images : []));

            $updates['additional_images'] = $baseImages
                ->merge($livewireAdditionalPaths)
                ->filter(fn ($path): bool => filled($path))
                ->values()
                ->all();
        }

        return $updates;
    }

    private function deleteProductImages(Product $product): void
    {
        if (filled($product->featured_image)) {
            Storage::disk('public')->delete($product->featured_image);
        }

        foreach ((array) $product->additional_images as $imagePath) {
            Storage::disk('public')->delete((string) $imagePath);
        }
    }

    private function optionLines(Product $product): array
    {
        $sizeDefault = ProductOptionPricing::parseLines((string) SiteSettings::get('default_size_price_options', ''));
        $materialDefault = ProductOptionPricing::parseLines((string) SiteSettings::get('default_material_price_options', ''));
        $finishDefault = ProductOptionPricing::parseLines((string) SiteSettings::get('default_finish_price_options', ''));
        $densityDefault = ProductOptionPricing::parseLines((string) SiteSettings::get('default_density_price_options', ''));
        $deliveryDefault = ProductOptionPricing::parseLines((string) SiteSettings::get('default_delivery_price_options', ''));

        return [
            'size_price_options' => ProductOptionPricing::toLines($product->size_price_options ?: $sizeDefault),
            'material_price_options' => ProductOptionPricing::toLines($product->material_price_options ?: $materialDefault),
            'finish_price_options' => ProductOptionPricing::toLines($product->finish_price_options ?: $finishDefault),
            'density_price_options' => ProductOptionPricing::toLines($product->density_price_options ?: $densityDefault),
            'delivery_price_options' => ProductOptionPricing::toLines($product->delivery_price_options ?: $deliveryDefault),
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

    /**
     * @return array<string, string>
     */
    private function serviceOptions(): array
    {
        $options = [
            'print' => 'General Print',
            'gift' => 'Gift Items',
        ];

        foreach (ServiceCatalog::all() as $slug => $service) {
            $options[ServiceCatalog::serviceTypeForSlug((string) $slug)] = (string) ($service['name'] ?? str($slug)->replace('-', ' ')->title());
        }

        return $options;
    }
}
