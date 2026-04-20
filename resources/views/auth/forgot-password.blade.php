{{-- 
    PrintBuka Forgot Password - DaisyUI Redesign
    Consistent with the new authentication flow design system
--}}

@extends('layouts.theme')

@section('title', 'Forgot Password | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-[#f4fbfb] to-white px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-md">
        {{-- Logo/Brand --}}
        <div class="mb-8 text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-pink-600 shadow-lg">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-slate-900">Forgot Password?</h2>
            <p class="mt-1 text-sm text-slate-600">No worries, we'll send you a reset link</p>
        </div>

        {{-- Main Card --}}
        <div class="card bg-white shadow-xl rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                {{-- Success Message Alert --}}
                @if (session('status'))
                    <div class="alert alert-success shadow-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Info Alert --}}
                <div class="alert bg-cyan-50 border-cyan-200 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-cyan-800 text-sm">Enter your registered email to receive a password reset link.</span>
                </div>

                <form action="{{ route('password.email') }}" method="POST" class="mt-4 space-y-6">
                    @csrf

                    {{-- Email Field --}}
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

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-pink-600 text-white font-bold shadow-md shadow-pink-200">
                        Send Reset Link
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Back to Login --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="link link-hover text-sm text-slate-500 hover:text-pink-600 flex items-center justify-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Sign In
                    </a>
                </div>

                {{-- Help Text --}}
                <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400">
                        Need help? <a href="#" class="link link-hover text-pink-600">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Security Note --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-slate-400 flex items-center justify-center gap-1">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                We'll only send reset links to verified email addresses on file.
            </p>
        </div>
    </div>
</main>
@endsection