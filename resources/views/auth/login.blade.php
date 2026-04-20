{{-- 
    PrintBuka Limited - Authentication Pages Redesign
    Colors maintained: Pink-600 (#DB2777), Cyan-300 (#67E8F9), Slate-950 (#020617)
    Following DaisyUI component standards for better maintainability and consistency
--}}

@extends('layouts.theme')

@section('title', (($mode ?? 'customer') === 'staff' ? 'Staff Login' : 'Login').' | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-[#f4fbfb] to-white px-4 py-12 sm:px-6 lg:px-8">
    {{-- Hero Section with Brand Messaging --}}
    <div class="mx-auto max-w-7xl text-center mb-8 lg:mb-12">
        <div class="inline-flex items-center gap-2 rounded-full bg-pink-50 px-4 py-1.5 text-sm font-medium text-pink-700 ring-1 ring-pink-200/50">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-pink-500"></span>
            </span>
            Trusted by 10,000+ businesses in Nigeria
        </div>
        <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
            Print <span class="text-pink-600">Everything</span> You Imagine
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
            Custom gifts, premium business materials, and cutting-edge printing technology — all under one roof.
        </p>
    </div>

    {{-- Main Card Container --}}
    <div class="mx-auto max-w-6xl">
        <div class="grid overflow-hidden rounded-2xl bg-white shadow-2xl shadow-slate-200/50 lg:grid-cols-5">
            
            {{-- Left Column - Brand Showcase (DaisyUI Card) --}}
            <div class="hidden lg:block lg:col-span-2 bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white">
                <div class="flex h-full flex-col justify-between">
                    <div class="space-y-6">
                        {{-- Logo Area --}}
                        <div class="flex items-center gap-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-500 shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-black tracking-tight">PrintBuka</span>
                        </div>

                        {{-- Tagline --}}
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-cyan-300">
                                {{ ($mode ?? 'customer') === 'staff' ? 'Staff Portal' : 'Welcome Back' }}
                            </p>
                            <h2 class="mt-3 text-3xl font-bold leading-tight">
                                {{ ($mode ?? 'customer') === 'staff' ? 'Manage print operations efficiently.' : 'Your printing journey continues here.' }}
                            </h2>
                            <p class="mt-4 text-sm leading-relaxed text-slate-300">
                                {{ ($mode ?? 'customer') === 'staff' 
                                    ? 'Access order management, customer support tools, and production dashboards.' 
                                    : 'Track orders, reorder favorites, save custom designs, and manage your print projects.' }}
                            </p>
                        </div>

                        {{-- Feature List (DaisyUI List) --}}
                        <ul class="mt-6 space-y-3">
                            <li class="flex items-center gap-3 text-sm">
                                <div class="rounded-full bg-cyan-500/20 p-1">
                                    <svg class="h-4 w-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span>Direct Image Printing (DIP)</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <div class="rounded-full bg-cyan-500/20 p-1">
                                    <svg class="h-4 w-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span>UV-DTF & DTF Transfers</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <div class="rounded-full bg-cyan-500/20 p-1">
                                    <svg class="h-4 w-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span>Laser Engraving Services</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Trust Badge --}}
                    <div class="mt-8 rounded-xl bg-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="h-8 w-8 rounded-full border-2 border-white bg-cyan-400"></div>
                                <div class="h-8 w-8 rounded-full border-2 border-white bg-pink-400"></div>
                                <div class="h-8 w-8 rounded-full border-2 border-white bg-amber-400"></div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold">Join 10,000+ happy customers</p>
                                <div class="flex items-center gap-1 text-xs text-cyan-300">★★★★★ (4.9/5)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Login Form (DaisyUI Card) --}}
            <div class="lg:col-span-3 p-6 sm:p-8 lg:p-10">
                {{-- Mobile Brand Header --}}
                <div class="mb-6 lg:hidden">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-600">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-900">PrintBuka</span>
                    </div>
                </div>

                {{-- Form Header --}}
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-wider text-pink-600">
                        {{ ($mode ?? 'customer') === 'staff' ? 'Staff Access' : 'Sign In' }}
                    </p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">
                        {{ ($mode ?? 'customer') === 'staff' ? 'Staff Dashboard' : 'Welcome Back' }}
                    </h3>
                    <p class="mt-2 text-slate-600">
                        {{ ($mode ?? 'customer') === 'staff' 
                            ? 'Enter your staff credentials to access the management portal.' 
                            : 'Sign in to access your orders, designs, and faster checkout.' }}
                    </p>
                </div>

                {{-- Success Message Alert (DaisyUI Alert) --}}
                @if (session('status'))
                    <div class="alert alert-success mb-6 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Login Form --}}
                <form action="{{ $storeRoute ?? route('login.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Email Field (DaisyUI Input) --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Email Address</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="input input-bordered w-full pl-10 focus:input-primary focus:ring-2 focus:ring-pink-100 @error('email') input-error @enderror"
                                placeholder="you@example.com" required autocomplete="email" />
                        </div>
                        @error('email')
                            <label class="label">
                                <span class="label-text-alt text-pink-600">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Password Field (DaisyUI Input with toggle) --}}
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700">Password</span>
                            <a href="{{ route('password.request') }}" class="label-text-alt link link-primary text-pink-600 font-semibold">Forgot?</a>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" 
                                class="input input-bordered w-full pl-10 pr-24 focus:input-primary focus:ring-2 focus:ring-pink-100 @error('password') input-error @enderror"
                                placeholder="••••••••" required autocomplete="current-password" />
                            <button type="button" data-password-toggle data-target="password"
                                class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 hover:text-pink-600">
                                Show
                            </button>
                        </div>
                        @error('password')
                            <label class="label">
                                <span class="label-text-alt text-pink-600">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Remember Me Checkbox (DaisyUI Checkbox) --}}
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="remember" value="1" class="checkbox checkbox-sm checkbox-pink-600" />
                            <span class="label-text text-slate-600">Remember me for 30 days</span>
                        </label>
                    </div>

                    {{-- Additional Links --}}
                    <div class="flex flex-wrap gap-4 text-sm">
                        <a href="{{ route('verification.notice') }}" class="link link-hover text-slate-500 hover:text-pink-600">
                            Resend verification email
                        </a>
                    </div>

                    {{-- Submit Button (DaisyUI Button) --}}
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-pink-600 text-white font-bold shadow-md shadow-pink-200">
                        {{ ($mode ?? 'customer') === 'staff' ? 'Access Staff Portal' : 'Sign In to Dashboard' }}
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>

                    {{-- Sign Up Link --}}
                    <div class="text-center text-sm text-slate-600">
                        {{ ($mode ?? 'customer') === 'staff' ? 'Need staff access?' : 'New to PrintBuka?' }}
                        <a href="{{ $registerRoute ?? route('register') }}" class="link link-hover font-bold text-pink-600 hover:text-pink-700">
                            {{ ($mode ?? 'customer') === 'staff' ? 'Request staff account' : 'Create an account' }}
                        </a>
                    </div>
                </form>

                {{-- Social Proof Bar --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                        <span class="flex items-center gap-1">✓ 24hr Turnaround</span>
                        <span class="flex items-center gap-1">✓ Free Design Support</span>
                        <span class="flex items-center gap-1">✓ Nationwide Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- Password Toggle Script --}}
<script>
    (() => {
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const input = document.getElementById(button.dataset.target || '');
            if (!input) return;

            button.addEventListener('click', () => {
                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.textContent = shouldShow ? 'Hide' : 'Show';
            });
        });
    })();
</script>
@endsection