<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Turnstile
{
    public static function enabled(): bool
    {
        return app()->environment('public');
    }

    public static function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    public static function verify(Request $request): void
    {
        if (! self::enabled()) {
            return;
        }

        $secret = config('services.turnstile.secret_key');

        if (! $secret) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Captcha verification is not configured.',
            ]);
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

        if (! $response->ok() || ! $response->json('success')) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Captcha verification failed. Please try again.',
            ]);
        }
    }
}
