<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductOptionGroup;
use App\Models\ShopProductStockLog;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminShopProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $products = ShopProduct::query()
            ->withCount('optionGroups')
            ->when($request->input('search'), fn ($q, $s) => $q->where(function ($q) use ($s): void {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%")
                    ->orWhere('short_description', 'like', "%{$s}%");
            }))
            ->when($request->input('status') === 'active',   fn ($q) => $q->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->input('status') === 'featured', fn ($q) => $q->where('is_featured', true))
            ->when($request->input('stock') === 'out',       fn ($q) => $q->where('manage_stock', true)->where('stock_quantity', 0))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => ShopProduct::count(),
            'active'   => ShopProduct::where('is_active', true)->count(),
            'featured' => ShopProduct::where('is_featured', true)->count(),
            'out_of_stock' => ShopProduct::where('manage_stock', true)->where('stock_quantity', 0)->count(),
        ];

        return view('admin.shop-products.index', compact('products', 'stats'));
    }

    public function create(): View
    {
        return view('admin.shop-products.form', [
            'product' => null,
            'existingGroups' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $product = ShopProduct::create([
            ...$validated['fields'],
            'created_by' => auth()->id(),
        ]);

        $this->handleImage($request, $product);
        $this->syncOptionGroups($product, $validated['groups']);

        return redirect()->route('admin.shop-products.index')->with('status', 'Product created successfully.');
    }

    public function edit(ShopProduct $shopProduct): View
    {
        $shopProduct->loadMissing('optionGroups.options');

        $existingGroups = $shopProduct->optionGroups->map(fn ($group) => [
            'id' => $group->id,
            'name' => $group->name,
            'is_required' => $group->is_required,
            'options' => $group->options->map(fn ($opt) => [
                'id' => $opt->id,
                'name' => $opt->name,
                'price_modifier' => (float) $opt->price_modifier,
                'is_available' => $opt->is_available,
                'stock_quantity' => $opt->stock_quantity,
                'image' => $opt->image ?? '',
            ])->values()->all(),
        ])->values()->all();

        return view('admin.shop-products.form', [
            'product' => $shopProduct,
            'existingGroups' => $existingGroups,
        ]);
    }

    public function update(Request $request, ShopProduct $shopProduct): RedirectResponse
    {
        $validated = $this->validated($request, $shopProduct);

        $shopProduct->update($validated['fields']);
        $this->handleImage($request, $shopProduct);
        $this->syncOptionGroups($shopProduct, $validated['groups']);

        return redirect()->route('admin.shop-products.index')->with('status', 'Product updated.');
    }

    public function destroy(ShopProduct $shopProduct): RedirectResponse
    {
        $this->deleteImage((string) ($shopProduct->featured_image ?? ''));

        foreach ((array) ($shopProduct->additional_images ?? []) as $img) {
            $this->deleteImage((string) $img);
        }

        $shopProduct->delete();

        return back()->with('status', 'Product deleted.');
    }

    /** @return array{fields:array<string,mixed>,groups:array<int,mixed>} */
    private function validated(Request $request, ?ShopProduct $product = null): array
    {
        $id = $product?->id ?? 'NULL';
        $uniqueSlug = "unique:shop_products,slug,{$id}";
        $uniqueSku = "unique:shop_products,sku,{$id}";

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', $uniqueSku],
            'manage_stock' => ['nullable', 'boolean'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'option_groups' => ['nullable', 'array', 'max:10'],
            'option_groups.*.name' => ['required', 'string', 'max:100'],
            'option_groups.*.is_required' => ['nullable', 'boolean'],
            'option_groups.*.options' => ['nullable', 'array', 'max:50'],
            'option_groups.*.options.*.name' => ['required', 'string', 'max:100'],
            'option_groups.*.options.*.price_modifier' => ['nullable', 'numeric'],
            'option_groups.*.options.*.is_available' => ['nullable', 'boolean'],
            'option_groups.*.options.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'option_groups.*.options.*.image' => ['nullable', 'string', 'max:500'],
        ]);

        $fields = [
            'name' => $data['name'],
            'slug' => filled($data['slug'] ?? '') ? $data['slug'] : null,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'sale_price' => filled($data['sale_price'] ?? '') ? $data['sale_price'] : null,
            'sku' => filled($data['sku'] ?? '') ? $data['sku'] : null,
            'manage_stock' => $request->boolean('manage_stock'),
            'stock_quantity' => $request->boolean('manage_stock') ? ($data['stock_quantity'] ?? 0) : null,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ];

        return ['fields' => $fields, 'groups' => (array) ($data['option_groups'] ?? [])];
    }

    private function handleImage(Request $request, ShopProduct $product): void
    {
        if (! $request->hasFile('featured_image')) {
            return;
        }

        $request->validate([
            'featured_image' => ['file', 'max:4096', 'mimes:jpg,jpeg,png,webp'],
        ]);

        if (filled($product->featured_image)) {
            $this->deleteImage((string) $product->featured_image);
        }

        $file = $request->file('featured_image');
        $cloudinary = app(CloudinaryUploadService::class);
        $result = $cloudinary->storeToBoth($file, 'shop-products', 'shop-products');
        $product->update(['featured_image' => $result['cloudinary_public_id'] ?? $result['path']]);
    }

    /** @param array<int, array<string, mixed>> $groups */
    private function syncOptionGroups(ShopProduct $product, array $groups): void
    {
        $seenGroupIds = [];
        $seenOptionIds = [];

        foreach ($groups as $i => $groupData) {
            if (empty($groupData['name'])) {
                continue;
            }

            $groupId = isset($groupData['id']) ? (int) $groupData['id'] : null;

            if ($groupId && ShopProductOptionGroup::where('id', $groupId)->where('shop_product_id', $product->id)->exists()) {
                ShopProductOptionGroup::where('id', $groupId)->update([
                    'name' => $groupData['name'],
                    'is_required' => ! empty($groupData['is_required']),
                    'sort_order' => $i,
                ]);
                $group = ShopProductOptionGroup::find($groupId);
            } else {
                $group = ShopProductOptionGroup::create([
                    'shop_product_id' => $product->id,
                    'name' => $groupData['name'],
                    'is_required' => ! empty($groupData['is_required']),
                    'sort_order' => $i,
                ]);
            }

            $seenGroupIds[] = $group->id;

            foreach ((array) ($groupData['options'] ?? []) as $j => $optData) {
                if (empty($optData['name'])) {
                    continue;
                }

                $optionId = isset($optData['id']) ? (int) $optData['id'] : null;
                $newQty = isset($optData['stock_quantity']) && $optData['stock_quantity'] !== '' ? (int) $optData['stock_quantity'] : null;
                $newImage = filled($optData['image'] ?? '') ? $optData['image'] : null;

                if ($optionId && ShopProductOption::where('id', $optionId)->where('shop_product_option_group_id', $group->id)->exists()) {
                    $existing = ShopProductOption::find($optionId);
                    $oldQty = $existing->stock_quantity;

                    $existing->update([
                        'name' => $optData['name'],
                        'price_modifier' => (float) ($optData['price_modifier'] ?? 0),
                        'is_available' => ! isset($optData['is_available']) || (bool) $optData['is_available'],
                        'sort_order' => $j,
                        'stock_quantity' => $newQty,
                        'image' => $newImage,
                    ]);

                    // Log stock change if quantity was modified
                    if ($newQty !== null && $newQty !== $oldQty) {
                        $change = $oldQty === null ? $newQty : ($newQty - $oldQty);
                        ShopProductStockLog::create([
                            'shop_product_id' => $product->id,
                            'shop_product_option_id' => $existing->id,
                            'change' => $change,
                            'balance_after' => $newQty,
                            'reason' => $oldQty === null ? 'admin_set' : 'admin_adjust',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $seenOptionIds[] = $optionId;
                } else {
                    $option = ShopProductOption::create([
                        'shop_product_option_group_id' => $group->id,
                        'name' => $optData['name'],
                        'price_modifier' => (float) ($optData['price_modifier'] ?? 0),
                        'is_available' => ! isset($optData['is_available']) || (bool) $optData['is_available'],
                        'sort_order' => $j,
                        'stock_quantity' => $newQty,
                        'image' => $newImage,
                    ]);

                    if ($newQty !== null) {
                        ShopProductStockLog::create([
                            'shop_product_id' => $product->id,
                            'shop_product_option_id' => $option->id,
                            'change' => $newQty,
                            'balance_after' => $newQty,
                            'reason' => 'admin_set',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $seenOptionIds[] = $option->id;
                }
            }
        }

        // Remove options and groups no longer in the submitted data
        $product->optionGroups()->each(function (ShopProductOptionGroup $group) use ($seenGroupIds, $seenOptionIds): void {
            $group->options()
                ->whereNotIn('id', $seenOptionIds)
                ->delete();

            if (! in_array($group->id, $seenGroupIds, true)) {
                $group->delete();
            }
        });
    }

    private function deleteImage(string $path): void
    {
        if ($path === '') {
            return;
        }

        if (CloudinaryUrl::isCloudinaryResource($path)) {
            try {
                app(CloudinaryUploadService::class)->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Storage::disk('public')->delete($path);
    }
}
