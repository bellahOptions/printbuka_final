<?php

namespace Tests\Feature;

use App\Models\LargeFormatRate;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLargeFormatCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    public function test_operations_manager_can_manage_rates(): void
    {
        $user = $this->makeStaff('operations_manager');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.index'))
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.large-format.store'), [
                'material' => 'Flex Banner',
                'rate_per_sqft' => 200,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('large_format_rates', [
            'material' => 'Flex Banner',
            'rate_per_sqft' => 200,
        ]);

        $rate = LargeFormatRate::query()->firstOrFail();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.large-format.update', $rate), [
                'rate_per_sqft' => 250,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertSame('250.00', $rate->fresh()->rate_per_sqft);

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.large-format.destroy', $rate))
            ->assertRedirect();

        $this->assertDatabaseMissing('large_format_rates', ['id' => $rate->id]);
    }

    public function test_managing_director_can_manage_rates_via_wildcard_permission(): void
    {
        $user = $this->makeStaff('managing_director');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.index'))
            ->assertOk();
    }

    public function test_customer_service_can_use_the_calculator_but_not_manage_rates(): void
    {
        $user = $this->makeStaff('customer_service');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.calculator'))
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.large-format.store'), ['material' => 'x', 'rate_per_sqft' => 1])
            ->assertForbidden();
    }

    public function test_a_role_without_any_large_format_permission_is_forbidden(): void
    {
        $user = $this->makeStaff('office_assistant');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.calculator'))
            ->assertForbidden();
    }

    public function test_flex_banner_pricing_matches_the_stated_business_formula(): void
    {
        // 4ft x 5ft flex banner at ₦200/sqft => 4 * 5 * 200 = ₦4,000
        $rate = LargeFormatRate::query()->create(['material' => 'Flex Banner', 'rate_per_sqft' => 200]);

        $this->assertSame(4000.0, $rate->priceFor(4, 5, 'ft'));
    }

    public function test_sticker_pricing_in_inches_matches_the_stated_business_formula(): void
    {
        // 500 copies of 3in x 4in stickers at ₦400/sqft => 3*4*400/144*500 = ₦16,666.67
        $rate = LargeFormatRate::query()->create(['material' => 'SAV Sticker', 'rate_per_sqft' => 400]);

        $this->assertSame(16666.67, $rate->priceFor(3, 4, 'in', 500));
    }

    public function test_calculator_page_exposes_only_active_rates(): void
    {
        LargeFormatRate::query()->create(['material' => 'Flex Banner', 'rate_per_sqft' => 200, 'is_active' => true]);
        LargeFormatRate::query()->create(['material' => 'Retired Material', 'rate_per_sqft' => 100, 'is_active' => false]);

        $user = $this->makeStaff('customer_service');

        $response = $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.large-format.calculator'));

        $response->assertOk();
        $response->assertSee('Flex Banner');
        $response->assertDontSee('Retired Material');
    }
}
