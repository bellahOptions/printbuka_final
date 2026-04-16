<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryAddressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $this->validated($request);
        $hasAddress = $user->deliveryAddresses()->exists();
        $setDefault = $request->boolean('is_default') || ! $hasAddress;

        if ($setDefault) {
            $user->deliveryAddresses()->update(['is_default' => false]);
        }

        $user->deliveryAddresses()->create([
            ...$validated,
            'is_default' => $setDefault,
        ]);

        return back()->with('status', 'Delivery address added.');
    }

    public function update(Request $request, DeliveryAddress $deliveryAddress): RedirectResponse
    {
        $this->ensureOwnership($request, $deliveryAddress);
        $validated = $this->validated($request);
        $setDefault = $request->boolean('is_default');

        if ($setDefault) {
            $request->user()->deliveryAddresses()
                ->whereKeyNot($deliveryAddress->id)
                ->update(['is_default' => false]);
        } elseif ($deliveryAddress->is_default && ! $request->user()->deliveryAddresses()->whereKeyNot($deliveryAddress->id)->where('is_default', true)->exists()) {
            $setDefault = true;
        }

        $deliveryAddress->update([
            ...$validated,
            'is_default' => $setDefault,
        ]);

        return back()->with('status', 'Delivery address updated.');
    }

    public function destroy(Request $request, DeliveryAddress $deliveryAddress): RedirectResponse
    {
        $this->ensureOwnership($request, $deliveryAddress);
        $wasDefault = $deliveryAddress->is_default;
        $deliveryAddress->delete();

        if ($wasDefault) {
            $replacement = $request->user()->deliveryAddresses()->first();
            if ($replacement) {
                $replacement->update(['is_default' => true]);
            }
        }

        return back()->with('status', 'Delivery address removed.');
    }

    public function setDefault(Request $request, DeliveryAddress $deliveryAddress): RedirectResponse
    {
        $this->ensureOwnership($request, $deliveryAddress);

        $request->user()->deliveryAddresses()->update(['is_default' => false]);
        $deliveryAddress->update(['is_default' => true]);

        return back()->with('status', 'Default delivery address updated.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        return [
            'label' => $validated['label'],
            'recipient_name' => $validated['recipient_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'],
            'address' => $validated['street_address'],
            'landmark' => $validated['landmark'] ?? null,
        ];
    }

    private function ensureOwnership(Request $request, DeliveryAddress $deliveryAddress): void
    {
        abort_unless($deliveryAddress->user_id === $request->user()?->id, 403);
    }
}
