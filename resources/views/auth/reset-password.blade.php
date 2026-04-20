{{-- 
    PrintBuka Password Reset - DaisyUI Redesign
--}}

@extends('layouts.theme')

@section('title', 'Reset Password | PrintBuka')

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
            <h2 class="mt-4 text-2xl font-bold text-slate-900">Set New Password</h2>
            <p class="mt-1 text-sm text-slate-600">Create a strong, unique password for your account</p>
        </div>

        {{-- Main Card --}}
        <div class="card bg-white shadow-xl rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                @if (session('status'))
                    <div class="alert alert-success shadow-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email Field --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold text-slate-700">Email Address</span></label>
                        <input type="email" name="email" value="{{ old('email', $email) }}" 
                            class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                            placeholder="you@example.com" required />
                        @error('email') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold text-slate-700">New Password</span></label>
                        <input type="password" name="password" 
                            class="input input-bordered w-full focus:input-primary @error('password') input-error @enderror"
                            placeholder="••••••••" required />
                        @error('password') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold text-slate-700">Confirm Password</span></label>
                        <input type="password" name="password_confirmation" 
                            class="input input-bordered w-full focus:input-primary"
                            placeholder="••••••••" required />
                    </div>

                    {{-- Password Tips --}}
                    <div class="text-xs text-slate-500 space-y-1">
                        <p class="font-semibold">Password must:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Be at least 8 characters long</li>
                            <li>Include letters and numbers</li>
                            <li>Not be commonly used</li>
                        </ul>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-pink-600 text-white font-bold mt-4">
                        Reset Password
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Back to Login --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="link link-hover text-sm text-slate-500 hover:text-pink-600">
                        ← Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection