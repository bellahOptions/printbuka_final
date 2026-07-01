<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication — Printbuka</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-950">Two-Factor Authentication</h1>
            <p class="text-sm text-slate-500 mt-2">Open your authenticator app and enter the 6-digit code.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-pink-200 bg-pink-50 p-3 text-sm font-semibold text-pink-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.two-factor.verify') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Authentication Code</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="11"
                    placeholder="000000"
                    autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl font-black tracking-[0.4em] focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none"
                >
            </div>

            <button type="submit" class="w-full rounded-xl bg-slate-900 py-3 text-sm font-black text-white hover:bg-slate-700 transition">
                Verify &amp; Continue
            </button>
        </form>

        <div class="mt-5 text-center">
            <p class="text-xs text-slate-400 mb-2">Lost access to your authenticator app?</p>
            <button type="button" id="show-recovery"
                class="text-xs font-bold text-pink-600 hover:text-pink-700 underline">
                Use a recovery code instead
            </button>
        </div>

        {{-- Recovery code form (hidden by default) --}}
        <form method="POST" action="{{ route('admin.two-factor.verify') }}" id="recovery-form" class="hidden mt-4">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Recovery Code</label>
                <input
                    type="text"
                    name="code"
                    placeholder="XXXXX-XXXXX"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-lg font-mono tracking-widest focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none"
                >
            </div>
            <button type="submit" class="w-full rounded-xl bg-pink-600 py-3 text-sm font-black text-white hover:bg-pink-700 transition">
                Verify with Recovery Code
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-500 mt-5">
        Signed in as <strong class="text-slate-300">{{ auth()->user()->displayName() }}</strong> ·
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="text-pink-400 hover:text-pink-300">Sign out</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <script>
        document.getElementById('show-recovery').addEventListener('click', function() {
            document.getElementById('recovery-form').classList.remove('hidden');
            this.closest('div').classList.add('hidden');
        });
    </script>
</div>
</body>
</html>
