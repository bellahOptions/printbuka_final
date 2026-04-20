{{-- 
    PrintBuka Email Verification - DaisyUI Redesign
--}}

@extends('layouts.theme')

@section('title', 'Verify Email | PrintBuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-[#f4fbfb] to-white px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-md">
        {{-- Icon --}}
        <div class="mb-6 text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card bg-white shadow-xl rounded-2xl">
            <div class="card-body p-6 sm:p-8 text-center">
                <h2 class="card-title justify-center text-2xl">Verify Your Email</h2>
                <p class="text-slate-600 text-sm mt-2">
                    We sent a verification link to your email address. Click the link to activate your account.
                </p>

                @if (session('status'))
                    <div class="alert alert-success shadow-lg mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Resend Form --}}
                <form action="{{ route('verification.send') }}" method="POST" class="mt-6">
                    @csrf
                    <div class="form-control">
                        <input type="email" name="email" value="{{ old('email', $email ?? '') }}" 
                            class="input input-bordered w-full text-center @error('email') input-error @enderror"
                            placeholder="Enter your email" required />
                        @error('email') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 text-white mt-4">
                        Resend Verification Email
                    </button>
                </form>

                {{-- Divider --}}
                <div class="divider text-xs text-slate-400">OR</div>

                {{-- Login Link --}}
                <a href="{{ route('login') }}" class="link link-hover text-sm text-pink-600 font-semibold">
                    Return to Sign In
                </a>

                {{-- Help Text --}}
                <p class="text-xs text-slate-400 mt-4">
                    Didn't receive the email? Check your spam folder or contact support.
                </p>
            </div>
        </div>
    </div>
</main>
@endsection