<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopCartController extends Controller
{
    public function index(): View
    {
        $cartItems = self::resolveCartItems();
        $total = collect($cartItems)->sum('line_total');

        return view('shop.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request, ShopProduct $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $product->loadMissing('optionGroups.options');

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'integer', 'exists:shop_product_options,id'],
        ]);

        $quantity = (int) $validated['quantity'];
        $selectedOptionIds = collect((array) ($validated['options'] ?? []))->filter()->map(fn ($id) => (int) $id)->sort()->values()->all();

        // Validate options belong to this product and required groups are fulfilled
        $validOptionIds = $product->optionGroups->flatMap(fn ($g) => $g->options->pluck('id'))->all();
        foreach ($selectedOptionIds as $id) {
            abort_unless(in_array($id, $validOptionIds, true), 422, 'Invalid option.');
        }

        foreach ($product->optionGroups as $group) {
            if ($group->is_required) {
                $groupOptionIds = $group->options->pluck('id')->all();
                if (empty(array_intersect($groupOptionIds, $selectedOptionIds))) {
                    return back()->withErrors(['options' => "Please select an option for \"{$group->name}\"."]);
                }
            }
        }

        $cart = session()->get('shop.cart', []);

        foreach ($cart as &$item) {
            if ($item['product_id'] === $product->id && $item['selected_option_ids'] === $selectedOptionIds) {
                $item['quantity'] = min(99, $item['quantity'] + $quantity);
                session()->put('shop.cart', $cart);

                return redirect()->route('shop.cart')->with('status', 'Quantity updated in cart.');
            }
        }

        $cart[] = ['product_id' => $product->id, 'quantity' => $quantity, 'selected_option_ids' => $selectedOptionIds];
        session()->put('shop.cart', $cart);

        return redirect()->route('shop.cart')->with('status', 'Item added to cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session()->get('shop.cart', []);

        foreach ($cart as $index => &$item) {
            if (isset($validated['quantities'][$index])) {
                $item['quantity'] = (int) $validated['quantities'][$index];
            }
        }

        session()->put('shop.cart', array_values($cart));

        return back()->with('status', 'Cart updated.');
    }

    public function remove(int $index): RedirectResponse
    {
        $cart = session()->get('shop.cart', []);
        unset($cart[$index]);
        session()->put('shop.cart', array_values($cart));

        return back()->with('status', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('shop.cart');

        return redirect()->route('shop.index')->with('status', 'Cart cleared.');
    }

    /**
     * Hydrate raw session cart data into resolved cart items with server-side prices.
     *
     * @return array<int, array{index:int,product:ShopProduct,quantity:int,selected_options:\Illuminate\Support\Collection,unit_price:float,line_total:float}>
     */
    public static function resolveCartItems(): array
    {
        $cartData = session()->get('shop.cart', []);

        if (empty($cartData)) {
            return [];
        }

        $productIds = collect($cartData)->pluck('product_id')->unique()->all();
        $products = ShopProduct::query()->whereIn('id', $productIds)->active()->with('optionGroups.options')->get()->keyBy('id');

        $optionIds = collect($cartData)->flatMap(fn ($item) => $item['selected_option_ids'] ?? [])->unique()->all();
        $options = ShopProductOption::query()->whereIn('id', $optionIds)->with('group')->get()->keyBy('id');

        $resolved = [];

        foreach ($cartData as $index => $item) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                continue; // product deactivated or deleted — skip silently
            }

            $selectedOptions = collect($item['selected_option_ids'] ?? [])->map(fn ($id) => $options->get($id))->filter()->values();
            $optionModifier = $selectedOptions->sum(fn ($opt) => (float) $opt->price_modifier);
            $unitPrice = $product->currentPrice() + $optionModifier;
            $quantity = (int) $item['quantity'];

            $resolved[] = [
                'index' => $index,
                'product' => $product,
                'quantity' => $quantity,
                'selected_options' => $selectedOptions,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $quantity, 2),
            ];
        }

        return $resolved;
    }
}
