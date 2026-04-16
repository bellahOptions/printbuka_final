<?php

namespace Tests\Feature;

use App\Models\DeliveryAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_multiple_delivery_addresses_and_switch_default(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('profile.addresses.store'), $this->addressPayload([
                'label' => 'Home',
                'city' => 'Lagos',
                'street_address' => '10 Admiralty Way',
            ]))
            ->assertSessionHasNoErrors();

        $this->actingAs($user)
            ->post(route('profile.addresses.store'), $this->addressPayload([
                'label' => 'Office',
                'city' => 'Abuja',
                'street_address' => '12 Herbert Macaulay Road',
            ]))
            ->assertSessionHasNoErrors();

        $home = DeliveryAddress::query()->where('user_id', $user->id)->where('label', 'Home')->firstOrFail();
        $office = DeliveryAddress::query()->where('user_id', $user->id)->where('label', 'Office')->firstOrFail();

        $this->assertTrue($home->is_default);
        $this->assertFalse($office->is_default);

        $this->actingAs($user)
            ->put(route('profile.addresses.default', $office))
            ->assertSessionHasNoErrors();

        $home->refresh();
        $office->refresh();

        $this->assertFalse($home->is_default);
        $this->assertTrue($office->is_default);
    }

    public function test_user_cannot_modify_another_users_delivery_address(): void
    {
        $owner = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $intruder = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $address = DeliveryAddress::query()->create([
            'user_id' => $owner->id,
            'label' => 'Owner Home',
            'recipient_name' => $owner->displayName(),
            'phone' => '08000000000',
            'city' => 'Lagos',
            'address' => '22 Adeniran Ogunsanya',
            'landmark' => 'Near bus stop',
            'is_default' => true,
        ]);

        $this->actingAs($intruder)
            ->put(route('profile.addresses.update', $address), $this->addressPayload())
            ->assertForbidden();

        $this->actingAs($intruder)
            ->put(route('profile.addresses.default', $address))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('profile.addresses.destroy', $address))
            ->assertForbidden();

        $this->assertDatabaseHas('delivery_addresses', [
            'id' => $address->id,
            'user_id' => $owner->id,
        ]);
    }

    private function addressPayload(array $overrides = []): array
    {
        return [
            'label' => 'Warehouse',
            'recipient_name' => 'Jane Doe',
            'phone' => '08012345678',
            'city' => 'Lagos',
            'street_address' => '1 Example Street',
            'landmark' => 'Near the gate',
            ...$overrides,
        ];
    }
}
