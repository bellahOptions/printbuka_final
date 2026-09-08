<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OtpSystemToggledMail;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $superAdminOnlyKeys = [
            'company_account_name',
            'company_account_number',
            'company_account_bank_name',
            'company_account_note',
            'important_action_notification_emails',
            'otp_enabled',
        ];

        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'notification_message' => ['nullable', 'string', 'max:1000'],
            'announcement' => ['nullable', 'string', 'max:2000'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:2000'],
            'otp_enabled' => ['nullable', 'boolean'],
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
            'pending_job_reminder_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
            'support_ticket_unanswered_reminder_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
            'support_ticket_unanswered_reminder_cooldown_hours' => ['nullable', 'integer', 'min:1', 'max:240'],
            'important_action_notification_emails' => ['nullable', 'string', 'max:2000'],
            'home_hero_image_1' => ['nullable', 'url', 'max:2000'],
            'home_hero_image_2' => ['nullable', 'url', 'max:2000'],
            'home_hero_image_3' => ['nullable', 'url', 'max:2000'],
            'home_hero_image_4' => ['nullable', 'url', 'max:2000'],
            'home_hero_image_5' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_1' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_2' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_3' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_4' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_5' => ['nullable', 'url', 'max:2000'],
            'home_category_fallback_image_6' => ['nullable', 'url', 'max:2000'],
            'home_promo_image_1' => ['nullable', 'url', 'max:2000'],
            'home_promo_image_2' => ['nullable', 'url', 'max:2000'],
        ]);

        $isSuperAdmin = $request->user()?->role === 'super_admin';

        if (! $isSuperAdmin) {
            $requestedSuperAdminOnlyUpdate = collect($superAdminOnlyKeys)
                ->contains(fn (string $key): bool => array_key_exists($key, $validated));

            abort_if($requestedSuperAdminOnlyUpdate, 403);

            // otp_enabled's checkbox is only rendered for super admins, so it
            // is simply absent from non-super-admin submissions. Never derive
            // it from $request->boolean() here — that would coerce a missing
            // field to false and silently disable OTP for every save.
            unset($validated['otp_enabled']);
        }

        $previousOtpEnabled = null;

        if ($isSuperAdmin) {
            $previousOtpEnabled = SiteSetting::query()->where('key', 'otp_enabled')->value('value') ?? '1';
            $validated['otp_enabled'] = $request->boolean('otp_enabled') ? '1' : '0';
        }

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $validated['pending_job_reminder_hours'] = (string) ($validated['pending_job_reminder_hours'] ?? 24);
        $validated['support_ticket_unanswered_reminder_hours'] = (string) ($validated['support_ticket_unanswered_reminder_hours'] ?? 24);
        $validated['support_ticket_unanswered_reminder_cooldown_hours'] = (string) ($validated['support_ticket_unanswered_reminder_cooldown_hours'] ?? 12);

        foreach ($validated as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $this->settingGroup($key)]
            );
        }

        SiteSettings::clearCache();

        if ($isSuperAdmin && $previousOtpEnabled !== $validated['otp_enabled']) {
            $this->notifyOtpToggle($request->user(), $validated['otp_enabled'] === '1');
        }

        return back()->with('status', 'Site settings updated.');
    }

    private function notifyOtpToggle(User $actor, bool $enabled): void
    {
        try {
            Mail::to((string) $actor->email)->send(new OtpSystemToggledMail($actor, $actor, $enabled, isActingAdmin: true));
        } catch (\Throwable $exception) {
            Log::error('OTP toggle confirmation email failed.', [
                'actor_id' => $actor->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $recipients = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('id', '!=', $actor->id)
            ->get();

        foreach ($recipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new OtpSystemToggledMail($recipient, $actor, $enabled, isActingAdmin: false));
            } catch (\Throwable $exception) {
                Log::error('OTP toggle notification email failed.', [
                    'recipient_id' => $recipient->id,
                    'actor_id' => $actor->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function settingGroup(string $key): string
    {
        return match (true) {
            str_contains($key, 'otp') => 'security',
            str_contains($key, 'maintenance') => 'maintenance',
            str_starts_with($key, 'company_account_') => 'finance',
            str_contains($key, 'paper_') || str_contains($key, 'finishings') => 'print_options',
            str_contains($key, 'important_action') => 'notifications',
            str_contains($key, 'notification') || str_contains($key, 'announcement') => 'notifications',
            str_starts_with($key, 'home_') => 'homepage_images',
            default => 'general',
        };
    }
}
