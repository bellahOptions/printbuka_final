<?php

namespace App\Support;

use App\Models\TwoFactorTrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * "Remember this device" for staff 2FA/OTP — lets a staff member choose how
 * long they stay signed in without being re-challenged for TOTP/OTP, via a
 * long-lived cookie backed by a hashed, revocable DB record (selector +
 * validator, same shape as Laravel's own remember-me cookie).
 */
class TwoFactorTrust
{
    private const COOKIE = 'pb_trusted_device';

    /**
     * Allowed "remember for" durations, in days. 0 means "this session only"
     * and results in no trust record being created.
     */
    public const ALLOWED_DAYS = [0, 7, 30, 60, 90];

    public static function remember(User $user, int $days, Request $request): void
    {
        if (! in_array($days, self::ALLOWED_DAYS, true) || $days <= 0) {
            return;
        }

        $selector = Str::random(20);
        $validator = Str::random(40);

        $user->twoFactorTrustedDevices()->create([
            'selector' => $selector,
            'hashed_token' => hash('sha256', $validator),
            'label' => self::labelFor((string) $request->userAgent()),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays($days),
        ]);

        Cookie::queue(self::COOKIE, $selector.'|'.$validator, $days * 1440);
    }

    public static function check(Request $request, User $user): bool
    {
        $cookie = $request->cookie(self::COOKIE);

        if (! is_string($cookie) || ! str_contains($cookie, '|')) {
            return false;
        }

        [$selector, $validator] = explode('|', $cookie, 2);

        $device = TwoFactorTrustedDevice::query()
            ->where('user_id', $user->id)
            ->where('selector', $selector)
            ->first();

        if (! $device || $device->isExpired()) {
            return false;
        }

        if (! hash_equals($device->hashed_token, hash('sha256', $validator))) {
            return false;
        }

        $device->forceFill(['last_used_at' => now()])->save();

        return true;
    }

    public static function forgetCurrentDevice(): void
    {
        Cookie::queue(Cookie::forget(self::COOKIE));
    }

    /**
     * Revoke every trusted device for a user (e.g. when 2FA is reset/disabled).
     */
    public static function forgetAll(User $user): void
    {
        $user->twoFactorTrustedDevices()->delete();
    }

    private static function labelFor(string $userAgent): string
    {
        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            default => 'Browser',
        };

        $platform = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown device',
        };

        return "{$browser} on {$platform}";
    }
}
