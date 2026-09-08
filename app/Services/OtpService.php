<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\StaffOtpCode;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public const CODE_LENGTH = 6;

    public const CODE_TTL_MINUTES = 10;

    public const MAX_VERIFY_ATTEMPTS = 5;

    public function hasActiveCode(User $user): bool
    {
        return StaffOtpCode::query()->where('user_id', $user->id)->active()->exists();
    }

    public function generateAndSend(User $user, ?string $ip = null): StaffOtpCode
    {
        $lock = Cache::lock('otp-send:'.$user->id, 10);

        return $lock->block(3, function () use ($user, $ip): StaffOtpCode {
            StaffOtpCode::query()->where('user_id', $user->id)->active()->update(['consumed_at' => now()]);

            $code = str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

            $otp = StaffOtpCode::create([
                'user_id' => $user->id,
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
                'last_sent_at' => now(),
                'ip_address' => $ip,
            ]);

            Mail::to((string) $user->email)->send(new OtpCodeMail($user, $code, self::CODE_TTL_MINUTES));

            return $otp;
        });
    }

    public function resend(User $user, Request $request): StaffOtpCode
    {
        $this->ensureIsNotRateLimited($request, 'otp-send');

        RateLimiter::hit($this->throttleKey($request, 'otp-send'), 60);

        return $this->generateAndSend($user, $request->ip());
    }

    public function verify(User $user, string $code, Request $request): bool
    {
        $this->ensureIsNotRateLimited($request, 'otp-verify');

        $otp = StaffOtpCode::query()->where('user_id', $user->id)->active()->latest('id')->first();

        if (! $otp) {
            RateLimiter::hit($this->throttleKey($request, 'otp-verify'), 60);

            return false;
        }

        $normalized = str_replace(' ', '', $code);

        if (! Hash::check($normalized, $otp->code_hash)) {
            $otp->increment('attempts');

            if ($otp->attempts >= self::MAX_VERIFY_ATTEMPTS) {
                $otp->forceFill(['consumed_at' => now()])->save();
            }

            RateLimiter::hit($this->throttleKey($request, 'otp-verify'), 60);

            return false;
        }

        $otp->forceFill(['consumed_at' => now()])->save();
        RateLimiter::clear($this->throttleKey($request, 'otp-verify'));

        return true;
    }

    private function ensureIsNotRateLimited(Request $request, string $action): void
    {
        $limit = $action === 'otp-send' ? 5 : 10;

        if (! RateLimiter::tooManyAttempts($this->throttleKey($request, $action), $limit)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request, $action));

        throw ValidationException::withMessages([
            'code' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(Request $request, string $action): string
    {
        $user = $request->user();

        return Str::transliterate(Str::lower($action.'|'.($user?->email ?? 'guest').'|'.$request->ip()));
    }
}
