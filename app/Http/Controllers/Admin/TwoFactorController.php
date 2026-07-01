<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private function g2fa(): Google2FA
    {
        return new Google2FA();
    }

    // ── Setup ──────────────────────────────────────────────────────────────

    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // If already confirmed and session is verified, go to dashboard
        if ($user->hasTwoFactorEnabled() && session('staff_2fa_verified')) {
            return redirect()->route('admin.dashboard');
        }

        // Generate a secret if the user doesn't have one yet
        if (! $user->two_factor_secret) {
            $secret = $this->g2fa()->generateSecretKey();
            $user->forceFill([
                'two_factor_secret'         => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode($this->generateRecoveryCodes())),
            ])->save();
        }

        $secret  = decrypt($user->two_factor_secret);
        $otpUrl  = $this->g2fa()->getQRCodeUrl(config('app.name'), (string) $user->email, $secret);
        $qrSvg   = $this->buildQrSvg($otpUrl);

        return view('auth.admin.two-factor-setup', [
            'user'   => $user,
            'secret' => $secret,
            'qrSvg'  => $qrSvg,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'digits:6']]);

        $user   = $request->user();
        $secret = decrypt($user->two_factor_secret);
        $valid  = $this->g2fa()->verifyKey($secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'The code is invalid. Check your authenticator app and try again.']);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        session(['staff_2fa_verified' => true]);

        return redirect()->route('admin.two-factor.recovery-codes')
            ->with('status', 'Two-factor authentication enabled. Save your recovery codes now — you won\'t see them again.');
    }

    // ── Recovery Codes ─────────────────────────────────────────────────────

    public function showRecoveryCodes(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.two-factor.setup');
        }

        $codes = collect(json_decode(decrypt($user->two_factor_recovery_codes), true));

        return view('auth.admin.two-factor-recovery-codes', ['codes' => $codes]);
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasTwoFactorEnabled() && session('staff_2fa_verified'), 403);

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($this->generateRecoveryCodes())),
        ])->save();

        return redirect()->route('admin.two-factor.recovery-codes')
            ->with('status', 'Recovery codes regenerated. Your old codes are now invalid.');
    }

    // ── Challenge ──────────────────────────────────────────────────────────

    public function showChallenge(Request $request): View|RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('staff.login');
        }

        if (session('staff_2fa_verified')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('auth.admin.two-factor-challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $user   = $request->user();
        $code   = str_replace([' ', '-'], '', $request->code);
        $valid  = false;

        // Try TOTP first
        if ($user->two_factor_secret) {
            $valid = $this->g2fa()->verifyKey(decrypt($user->two_factor_secret), $code);
        }

        // Fall back to recovery code
        if (! $valid && $user->two_factor_recovery_codes) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            $idx = array_search($code, $recoveryCodes, true);

            if ($idx !== false) {
                array_splice($recoveryCodes, $idx, 1);
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                ])->save();
                $valid = true;
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid authentication code. Please try again.'])->onlyInput('code');
        }

        session(['staff_2fa_verified' => true]);

        return redirect()->intended(route('admin.dashboard'));
    }

    // ── Disable (super admin / HR only) ────────────────────────────────────

    public function disable(Request $request): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor->canAdmin('staff.kyc') || $actor->canAdmin('*'), 403);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $target = \App\Models\User::findOrFail($request->user_id);
        abort_if($target->role === 'customer', 403);

        $target->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        // If the target is the currently-logged-in user, clear their session flag too
        if ($actor->id === $target->id) {
            session()->forget('staff_2fa_verified');
        }

        return back()->with('status', '2FA disabled for '.$target->displayName().'. They will be prompted to set it up again on next login.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function generateRecoveryCodes(): array
    {
        return Collection::times(8, fn () =>
            strtoupper(Str::random(5)).'-'.strtoupper(Str::random(5))
        )->all();
    }

    private function buildQrSvg(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($url);
    }
}
