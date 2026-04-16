@extends('layouts.theme')

@section('title', (($mode ?? 'customer') === 'staff' ? 'Staff Login' : 'Login').' | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-6xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Welcome back</p>
                    <h1 class="mt-4 text-5xl leading-tight">{{ ($mode ?? 'customer') === 'staff' ? 'Enter the staff workspace.' : 'Pick up your print order where you left off.' }}</h1>
                    <p class="mt-5 text-sm leading-7 text-slate-300">{{ ($mode ?? 'customer') === 'staff' ? 'Staff accounts are approved by the Super Admin before dashboard access is enabled.' : 'Track orders, save product ideas and manage your Printbuka account from one dashboard.' }}</p>
                </div>
                <div class="rounded-md bg-white p-5 text-slate-950">
                    <p class="text-sm font-black text-pink-700">Popular next step</p>
                    <p class="mt-2 text-2xl font-black">Browse products after sign in.</p>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">{{ ($mode ?? 'customer') === 'staff' ? 'Staff Login' : 'Login' }}</p>
                <h2 class="mt-2 text-4xl text-slate-950">{{ ($mode ?? 'customer') === 'staff' ? 'Access staff tools.' : 'Access your account.' }}</h2>
                @if (session('status'))
                    <p class="mt-3 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                @endif
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ ($mode ?? 'customer') === 'staff' ? 'Need staff access?' : 'New to Printbuka?' }}
                    <a href="{{ $registerRoute ?? route('register') }}" class="font-black text-pink-700 hover:text-pink-800">{{ ($mode ?? 'customer') === 'staff' ? 'Register as staff' : 'Create an account' }}</a>.
                </p>


                <form action="{{ $storeRoute ?? route('login.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                            required
                        />
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="text-sm font-black text-slate-800">Password</label>
                        <div class="relative mt-2">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                class="min-h-12 w-full rounded-md border border-slate-200 px-4 pr-28 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"
                                required
                            />
                            <button
                                type="button"
                                data-password-toggle
                                data-target="password"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                            >
                                Show
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm font-bold text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500" />
                        Remember me
                    </label>
                    <p class="text-sm font-bold text-slate-600">
                        Forgot your password?
                        <a href="{{ route('password.request') }}" class="font-black text-pink-700 hover:text-pink-800">Reset it here</a>.
                    </p>
                    <p class="text-sm font-bold text-slate-600">
                        Need a new verification link?
                        <a href="{{ route('verification.notice') }}" class="font-black text-pink-700 hover:text-pink-800">Resend verification email</a>.
                    </p>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Login</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        (() => {
            document.querySelectorAll('[data-password-toggle]').forEach((button) => {
                const input = document.getElementById(button.dataset.target || '');
                if (!input) {
                    return;
                }

                button.addEventListener('click', () => {
                    const shouldShow = input.type === 'password';
                    input.type = shouldShow ? 'text' : 'password';
                    button.textContent = shouldShow ? 'Hide' : 'Show';
                });
            });
        })();
    </script>
@endsection
