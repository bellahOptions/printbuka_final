<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\StaffSignupAlertMail;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', [
            'mode' => 'customer',
            'storeRoute' => route('login.store'),
            'registerRoute' => route('register'),
        ]);
    }

    public function showStaffLogin(): View
    {
        return view('auth.login', [
            'mode' => 'staff',
            'storeRoute' => route('staff.login.store'),
            'registerRoute' => route('staff.register'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        return $this->attemptLogin($request, false);
    }

    public function staffLogin(Request $request): RedirectResponse
    {
        return $this->attemptLogin($request, true);
    }

    private function attemptLogin(Request $request, bool $staffOnly): RedirectResponse
    {
        $action = $staffOnly ? 'staff-login' : 'login';

        $this->ensureIsNotRateLimited($request, $action);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request, $action));

            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('email');
        }

        if ($staffOnly && Auth::user()->role === 'customer') {
            Auth::logout();
            RateLimiter::hit($this->throttleKey($request, $action));

            return back()
                ->withErrors([
                    'email' => 'Use the customer login for customer accounts.',
                ])
                ->onlyInput('email');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey($request, $action));

            return back()
                ->withErrors([
                    'email' => $staffOnly
                        ? 'Your staff account is pending approval by the Super Admin.'
                        : 'This account is inactive. Contact Printbuka management.',
                ])
                ->onlyInput('email');
        }

        if (Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey($request, $action));

            return redirect()
                ->route('verification.notice', ['email' => $credentials['email']])
                ->with('status', 'Your email is not verified yet. Please verify your email before signing in.');
        }

        RateLimiter::clear($this->throttleKey($request, $action));

        $request->session()->regenerate();

        return redirect()->intended($this->postLoginRoute());
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showStaffRegister(): View
    {
        return view('auth.staff-register');
    }

    public function register(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request, 'register');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:22'],
            'companyName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create($validated);
        $user->sendEmailVerificationNotification();

        RateLimiter::clear($this->throttleKey($request, 'register'));

        return redirect()
            ->route('login')
            ->with('status', 'Account created. Please verify your email address before signing in.');
    }

    public function staffRegister(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request, 'staff-register');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:22'],
            'address' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $staff = User::create([
            ...$validated,
            'companyName' => 'Printbuka',
            'role' => 'staff_pending',
            'department' => null,
            'requested_role' => null,
            'other_role' => null,
            'is_active' => false,
        ]);
        $staff->sendEmailVerificationNotification();
        $this->notifySuperAdminsOfStaffSignup($staff);

        RateLimiter::clear($this->throttleKey($request, 'staff-register'));

        return redirect()
            ->route('staff.login')
            ->with('status', 'Staff registration submitted. Your account remains pending until Super Admin approval, and email verification will be required at first login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function ensureIsNotRateLimited(Request $request, string $action): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request, $action), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request, $action));

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(Request $request, string $action): string
    {
        return Str::transliterate(Str::lower($action.'|'.$request->input('email', 'user').'|'.$request->ip()));
    }

    private function postLoginRoute(): string
    {
        return Auth::user()?->hasAdminAccess()
            ? route('admin.dashboard')
            : route('dashboard'); 
    }

    private function notifySuperAdminsOfStaffSignup(User $staff): void
    {
        $recipients = User::query()
            ->where('role', 'super_admin')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        foreach ($recipients as $recipient) {
            try {
                Mail::to((string) $recipient->email)->send(new StaffSignupAlertMail($recipient, $staff));
            } catch (\Throwable $exception) {
                Log::error('Staff signup alert email failed.', [
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'staff_id' => $staff->id,
                    'staff_email' => $staff->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
