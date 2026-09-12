<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email — Printbuka</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <img src="{{ asset('prn-old-logo-drk.svg') }}" alt="Printbuka" class="h-10 w-auto mx-auto">
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-3xl shadow-2xl p-8">

        <div class="text-center mb-7">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 mb-4">
                <svg class="w-7 h-7 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-950">Check Your Email</h1>
            <p class="text-sm text-slate-500 mt-2">We sent a 6-digit verification code to your email address.</p>
        </div>

        @if ($sendError ?? null)
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm font-semibold text-amber-700">
                {{ $sendError }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-pink-200 bg-pink-50 p-3 text-sm font-semibold text-pink-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.otp.verify') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Verification Code</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="000000"
                    autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl font-black tracking-[0.4em] focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none"
                >
            </div>

            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Stay Signed In</label>
                <select name="remember_days" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                    <option value="0">Just this session — ask again next time</option>
                    <option value="7">Don't ask again for 7 days on this device</option>
                    <option value="30">Don't ask again for 30 days on this device</option>
                    <option value="60">Don't ask again for 60 days on this device</option>
                    <option value="90">Don't ask again for 90 days on this device</option>
                </select>
            </div>

            <button type="submit" class="w-full rounded-xl bg-slate-900 py-3 text-sm font-black text-white hover:bg-slate-700 transition">
                Verify &amp; Continue
            </button>
        </form>

        <form method="POST" action="{{ route('admin.otp.send') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-slate-200 py-3 text-sm font-black text-slate-700 hover:bg-slate-50 transition">
                Resend Code
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-500 mt-5">
        Signed in as <strong class="text-slate-300">{{ auth()->user()->displayName() }}</strong> ·
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="text-pink-400 hover:text-pink-300">Sign out</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</div>
</body>
</html>
