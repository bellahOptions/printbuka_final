<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::query()->pluck('value', 'key'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $servicePriceKeys = [
            'service_price_direct_image_printing',
            'service_price_direct_image_printing_design',
            'service_price_direct_image_printing_delivery',
            'service_price_uv_dtf',
            'service_price_dtf',
            'service_price_dtf_design',
            'service_price_dtf_delivery',
            'service_dtf_size_price_options',
            'service_price_laser_engraving',
        ];
        $companyAccountKeys = [
            'company_account_name',
            'company_account_number',
            'company_account_bank_name',
            'company_account_note',
        ];

        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'notification_message' => ['nullable', 'string', 'max:1000'],
            'announcement' => ['nullable', 'string', 'max:2000'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'company_account_name' => ['nullable', 'string', 'max:255'],
            'company_account_number' => ['nullable', 'string', 'max:255'],
            'company_account_bank_name' => ['nullable', 'string', 'max:255'],
            'company_account_note' => ['nullable', 'string', 'max:500'],
            'paper_types' => ['nullable', 'string', 'max:5000'],
            'paper_sizes' => ['nullable', 'string', 'max:5000'],
            'finishings' => ['nullable', 'string', 'max:5000'],
            'paper_densities' => ['nullable', 'string', 'max:5000'],
            'default_material_price_options' => ['nullable', 'string', 'max:12000'],
            'default_size_price_options' => ['nullable', 'string', 'max:12000'],
            'default_finish_price_options' => ['nullable', 'string', 'max:12000'],
            'default_density_price_options' => ['nullable', 'string', 'max:12000'],
            'default_delivery_price_options' => ['nullable', 'string', 'max:12000'],
            'service_price_direct_image_printing' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_direct_image_printing_design' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_direct_image_printing_delivery' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_uv_dtf' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_dtf' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_dtf_design' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_price_dtf_delivery' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'service_dtf_size_price_options' => ['nullable', 'string', 'max:12000'],
            'service_price_laser_engraving' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'pending_job_reminder_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
            'support_ticket_unanswered_reminder_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
            'support_ticket_unanswered_reminder_cooldown_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
        ]);

        if ($request->user()?->role !== 'super_admin') {
            $requestedSuperAdminOnlyUpdate = collect(array_merge($servicePriceKeys, $companyAccountKeys))
                ->contains(fn (string $key): bool => array_key_exists($key, $validated));

            abort_if($requestedSuperAdminOnlyUpdate, 403);
        }

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $validated['pending_job_reminder_hours'] = (string) ($validated['pending_job_reminder_hours'] ?? 24);
        $validated['support_ticket_unanswered_reminder_hours'] = (string) ($validated['support_ticket_unanswered_reminder_hours'] ?? 24);
        $validated['support_ticket_unanswered_reminder_cooldown_hours'] = (string) ($validated['support_ticket_unanswered_reminder_cooldown_hours'] ?? 12);
        foreach ($servicePriceKeys as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $key !== 'service_dtf_size_price_options') {
                $validated[$key] = (string) $validated[$key];
            }
        }

        foreach ($validated as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $this->settingGroup($key)]
            );
        }

        return back()->with('status', 'Site settings updated.');
    }

    private function settingGroup(string $key): string
    {
        return match (true) {
            str_contains($key, 'maintenance') => 'maintenance',
            str_contains($key, '_price_options') => 'pricing',
            str_starts_with($key, 'service_price_') => 'service_pricing',
            str_starts_with($key, 'company_account_') => 'finance',
            str_contains($key, 'paper_') || str_contains($key, 'finishings') => 'print_options',
            str_contains($key, 'notification') || str_contains($key, 'announcement') => 'notifications',
            default => 'general',
        };
    }
}
