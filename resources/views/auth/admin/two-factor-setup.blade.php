<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication — Printbuka</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <img src="{{ asset('prn-old-logo-drk.svg') }}" alt="Printbuka" class="h-10 w-auto mx-auto">
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-3xl shadow-2xl p-8">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-pink-100 mb-4">
                <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-950">Secure Your Account</h1>
            <p class="text-sm text-slate-500 mt-2">Two-factor authentication is required for all staff accounts.</p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>
        @endif

        {{-- Steps --}}
        <div class="space-y-5 mb-7">
            <div class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-600 text-white text-xs font-black flex items-center justify-center">1</span>
                <p class="text-sm text-slate-700">Install an authenticator app on your phone — <strong>Google Authenticator</strong>, <strong>Authy</strong>, or <strong>Microsoft Authenticator</strong>.</p>
            </div>
            <div class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-600 text-white text-xs font-black flex items-center justify-center">2</span>
                <p class="text-sm text-slate-700">Scan the QR code below, or enter the setup key manually.</p>
            </div>
            <div class="flex gap-3">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-600 text-white text-xs font-black flex items-center justify-center">3</span>
                <p class="text-sm text-slate-700">Enter the 6-digit code from your app to confirm setup.</p>
            </div>
        </div>

        {{-- QR Code --}}
        <div class="flex flex-col items-center gap-4 mb-7">
            <div class="p-3 bg-white rounded-2xl border-2 border-slate-200 inline-block">
                {!! $qrSvg !!}
            </div>
            <details class="text-center">
                <summary class="text-xs font-bold text-slate-400 cursor-pointer hover:text-pink-600">Can't scan? Enter setup key manually</summary>
                <p class="mt-2 text-xs font-mono bg-slate-100 rounded-xl px-4 py-2 tracking-widest select-all text-slate-700">{{ $secret }}</p>
            </details>
        </div>

        {{-- Confirm form --}}
        <form method="POST" action="{{ route('admin.two-factor.enable') }}">
            @csrf

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-pink-200 bg-pink-50 p-3 text-sm font-semibold text-pink-700">
                    {{ $errors->first() }}
                </div>
            @endif

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
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl font-black tracking-[0.5em] focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none"
                    value="{{ old('code') }}"
                >
            </div>

            <button type="submit" class="w-full rounded-xl bg-pink-600 py-3 text-sm font-black text-white hover:bg-pink-700 transition">
                Enable Two-Factor Authentication
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
