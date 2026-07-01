<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery Codes — Printbuka</title>
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
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-100 mb-4">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-950">Save Your Recovery Codes</h1>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Keep these codes safe. Each code can only be used <strong>once</strong> to regain access if you lose your authenticator app.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>
        @endif

        {{-- Recovery code grid --}}
        <div id="codes-box" class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-5">
            <div class="grid grid-cols-2 gap-2">
                @foreach ($codes as $code)
                    <div class="font-mono text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-lg px-3 py-2 text-center tracking-widest select-all">
                        {{ $code }}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mb-6">
            <button onclick="copyAllCodes()" class="flex-1 rounded-xl border border-slate-300 bg-white py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy All
            </button>
            <button onclick="downloadCodes()" class="flex-1 rounded-xl border border-slate-300 bg-white py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </button>
        </div>

        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-6">
            <p class="text-xs text-amber-800 font-semibold leading-relaxed">
                <strong>Warning:</strong> If you lose access to your authenticator app and have no recovery codes remaining, only a super admin or HR can reset your 2FA.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="block w-full text-center rounded-xl bg-slate-900 py-3 text-sm font-black text-white hover:bg-slate-700 transition">
            I've saved my codes — Continue to Dashboard
        </a>

        <div class="mt-4 pt-4 border-t border-slate-100">
            <form method="POST" action="{{ route('admin.two-factor.recovery-codes.regenerate') }}"
                  onsubmit="return confirm('This will permanently invalidate all existing recovery codes. Continue?')">
                @csrf
                <button type="submit" class="w-full text-xs font-bold text-slate-400 hover:text-pink-600 transition">
                    Regenerate recovery codes
                </button>
            </form>
        </div>

    </div>

    <p class="text-center text-xs text-slate-500 mt-5">
        Signed in as <strong class="text-slate-300">{{ auth()->user()->displayName() }}</strong> ·
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="text-pink-400 hover:text-pink-300">Sign out</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

</div>

<script>
const codes = @json($codes);

function copyAllCodes() {
    navigator.clipboard.writeText(codes.join('\n')).then(() => {
        alert('Recovery codes copied to clipboard!');
    });
}

function downloadCodes() {
    const text = 'Printbuka Staff — 2FA Recovery Codes\n\n' + codes.join('\n') + '\n\nStore these in a safe place. Each code can only be used once.';
    const blob = new Blob([text], { type: 'text/plain' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'printbuka-recovery-codes.txt';
    a.click();
}
</script>

</body>
</html>
