<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Support\TwoFactorTrust;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(private readonly OtpService $otpService)
    {
    }

    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('staff.login');
        }

        if (session('staff_2fa_verified')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        $sendError = null;

        if (! $this->otpService->hasActiveCode($user)) {
            try {
                $this->otpService->generateAndSend($user, $request->ip());
            } catch (\Throwable $e) {
                $sendError = "We couldn't send your verification code. Please try again in a moment.";
            }
        }

        return view('auth.admin.otp-challenge', ['sendError' => $sendError]);
    }

    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        try {
            $this->otpService->resend($user, $request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => "We couldn't send your verification code. Please try again in a moment."]);
        }

        return back()->with('status', 'A new code has been sent to your email.');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'remember_days' => ['nullable', 'integer', 'in:'.implode(',', TwoFactorTrust::ALLOWED_DAYS)],
        ]);

        $user = $request->user();
        abort_unless($user, 403);

        try {
            $valid = $this->otpService->verify($user, $request->string('code')->toString(), $request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'That code is invalid or has expired. Please try again.'])->onlyInput();
        }

        session(['staff_2fa_verified' => true]);

        TwoFactorTrust::remember($user, (int) $request->input('remember_days', 0), $request);

        return redirect()->intended(route('admin.dashboard'));
    }
}
