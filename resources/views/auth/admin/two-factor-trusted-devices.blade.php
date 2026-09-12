<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trusted Devices — Printbuka</title>
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
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 mb-4">
                <svg class="w-7 h-7 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-950">Trusted Devices</h1>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Devices you chose to stay signed in on without re-entering a code. Remove any you don't recognize.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>
        @endif

        <div class="space-y-3 mb-6">
            @forelse ($devices as $device)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-3">
                    <div class="min-w-0">
                        <p class="text-sm font-black text-slate-900 truncate">{{ $device->label ?: 'Unknown device' }}</p>
                        <p class="text-xs text-slate-400">
                            Trusted until {{ $device->expires_at->format('M j, Y') }}
                            @if ($device->last_used_at)
                                · last used {{ $device->last_used_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.two-factor.trusted-devices.revoke', $device) }}" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-pink-600 hover:text-pink-700">Remove</button>
                    </form>
                </div>
            @empty
                <p class="text-sm font-semibold text-slate-400 text-center py-4">No trusted devices — you'll be asked to verify every time you sign in.</p>
            @endforelse
        </div>

        @if ($devices->isNotEmpty())
            <form method="POST" action="{{ route('admin.two-factor.trusted-devices.revoke-all') }}"
                  onsubmit="return confirm('Remove all trusted devices? Every device (including this one) will need to verify again on next login.')" class="mb-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-xl border border-slate-300 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                    Remove all trusted devices
                </button>
            </form>
        @endif

        <a href="{{ route('admin.dashboard') }}"
           class="block w-full text-center rounded-xl bg-slate-900 py-3 text-sm font-black text-white hover:bg-slate-700 transition">
            Back to Dashboard
        </a>

    </div>

    <p class="text-center text-xs text-slate-500 mt-5">
        Signed in as <strong class="text-slate-300">{{ auth()->user()->displayName() }}</strong> ·
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="text-pink-400 hover:text-pink-300">Sign out</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

</div>
</body>
</html>
