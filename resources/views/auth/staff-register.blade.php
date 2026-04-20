{{-- 
    PrintBuka Staff Registration - DaisyUI Redesign
--}}

@extends('layouts.theme')

@section('title', 'Staff Registration | PrintBuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-[#f4fbfb] to-white px-4 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-1.5 text-sm font-medium text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Staff Portal Access
            </div>
            <h1 class="mt-4 text-3xl font-bold text-slate-900 sm:text-4xl">Request Staff Account</h1>
            <p class="mt-2 text-slate-600">Submit your details for admin review and approval</p>
        </div>

        {{-- Main Card --}}
        <div class="card bg-white shadow-xl rounded-2xl">
            <div class="card-body p-6 sm:p-8">
                {{-- Info Alert --}}
                <div class="alert bg-cyan-50 border-cyan-200 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-cyan-800 text-sm">Staff accounts require Super Admin approval. You'll receive an email once granted access.</span>
                </div>

                <form action="{{ route('staff.register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Personal Information Grid --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">First Name *</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Last Name *</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Phone *</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Date of Birth *</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="input input-bordered w-full" required />
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Residential Address *</span></label>
                        <input type="text" name="address" value="{{ old('address') }}" class="input input-bordered w-full" required />
                    </div>

                    {{-- Role Selection --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Requested Role *</span></label>
                            <select name="requested_role" class="select select-bordered w-full" required>
                                <option value="">Select department</option>
                                @foreach ($staffRoles as $value => $label)
                                    <option value="{{ $value }}" @selected(old('requested_role') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Other Role (optional)</span></label>
                            <input type="text" name="other_role" value="{{ old('other_role') }}" class="input input-bordered w-full" placeholder="If 'Other' selected above" />
                        </div>
                    </div>

                    {{-- Email & Password --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Email Address *</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full @error('email') input-error @enderror" required />
                        @error('email') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Password *</span></label>
                            <div class="relative">
                                <input id="staff_password" type="password" name="password" class="input input-bordered w-full pr-24" required />
                                <button type="button" data-password-toggle data-target="staff_password" class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2">Show</button>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Confirm Password *</span></label>
                            <div class="relative">
                                <input id="staff_password_confirmation" type="password" name="password_confirmation" class="input input-bordered w-full pr-24" required />
                                <button type="button" data-password-toggle data-target="staff_password_confirmation" class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2">Show</button>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-block bg-pink-600 hover:bg-pink-700 border-pink-600 text-white font-bold mt-6">
                        Submit for Approval
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Login Link --}}
                <div class="mt-4 text-center text-sm">
                    Already approved? 
                    <a href="{{ route('staff.login') }}" class="link link-hover font-bold text-pink-600">Go to Staff Login</a>
                </div>
            </div>
        </div>
    </div>
</main>

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