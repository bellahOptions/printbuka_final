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
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'notification_message' => ['nullable', 'string', 'max:1000'],
            'announcement' => ['nullable', 'string', 'max:2000'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'paper_types' => ['nullable', 'string', 'max:5000'],
            'paper_sizes' => ['nullable', 'string', 'max:5000'],
            'finishings' => ['nullable', 'string', 'max:5000'],
            'paper_densities' => ['nullable', 'string', 'max:5000'],
        ]);
        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';

        foreach ($validated as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => str_contains($key, 'maintenance') ? 'maintenance' : 'general']
            );
        }

        return back()->with('status', 'Site settings updated.');
    }
}
