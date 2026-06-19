<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerSyncService
{
    /**
     * Find or create a customer User record from contact details.
     *
     * - If the email already belongs to a staff account, returns null (no change).
     * - If the email matches an existing customer, fills in any missing name/phone and returns them.
     * - Otherwise, creates a new user with role='customer', a random password, and email pre-verified.
     */
    public function sync(string $email, string $fullName, ?string $phone = null): ?User
    {
        $email = strtolower(trim($email));

        if ($email === '') {
            return null;
        }

        try {
            $existing = User::where('email', $email)->first();

            if ($existing) {
                // Never touch staff or admin accounts
                if ($existing->role !== 'customer') {
                    return null;
                }

                $updates = [];

                if (empty($existing->first_name) && $fullName !== '') {
                    [$first, $last] = $this->parseName($fullName);
                    $updates['first_name'] = $first;
                    if ($last !== '') {
                        $updates['last_name'] = $last;
                    }
                }

                if (empty($existing->phone) && filled($phone)) {
                    $updates['phone'] = $phone;
                }

                if (! empty($updates)) {
                    $existing->update($updates);
                }

                return $existing;
            }

            [$firstName, $lastName] = $this->parseName($fullName);

            return User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'phone'             => $phone,
                'role'              => 'customer',
                'password'          => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('CustomerSyncService: failed to sync customer.', [
                'email'   => $email,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Sync a customer from a confirmed (paid) shop order and link user_id if missing.
     */
    public function syncFromShopOrder(ShopOrder $order): void
    {
        // Already linked — registered customer, nothing to do
        if ($order->user_id) {
            return;
        }

        $user = $this->sync(
            (string) $order->customer_email,
            (string) $order->customer_name,
            filled($order->customer_phone) ? (string) $order->customer_phone : null,
        );

        if ($user) {
            $order->update(['user_id' => $user->id]);
        }
    }

    /**
     * Sync a customer from a quote request order and link user_id if missing.
     */
    public function syncFromQuote(Order $order): void
    {
        // Already linked — registered customer who was logged in
        if ($order->user_id) {
            return;
        }

        $user = $this->sync(
            (string) $order->customer_email,
            (string) $order->customer_name,
            filled($order->customer_phone) ? (string) $order->customer_phone : null,
        );

        if ($user) {
            $order->update(['user_id' => $user->id]);
        }
    }

    /** @return array{0: string, 1: string} */
    private function parseName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);

        return [$parts[0] ?? '', $parts[1] ?? ''];
    }
}
