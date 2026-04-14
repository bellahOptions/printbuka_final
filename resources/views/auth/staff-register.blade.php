@extends('layouts.theme')

@section('title', 'Staff Registration | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-6xl overflow-hidden rounded-md bg-white shadow-xl shadow-cyan-950/10 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="p-6 sm:p-10">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Staff Registration</p>
                <h1 class="mt-2 text-4xl text-slate-950">Request access to Printbuka admin.</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">Already approved? <a href="{{ route('staff.login') }}" class="font-black text-pink-700 hover:text-pink-800">Use staff login</a>.</p>

                <form action="{{ route('staff.register.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black text-slate-800">First Name<input name="first_name" value="{{ old('first_name') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                        <label class="text-sm font-black text-slate-800">Last Name<input name="last_name" value="{{ old('last_name') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                        <label class="text-sm font-black text-slate-800">Phone<input name="phone" value="{{ old('phone') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                        <label class="text-sm font-black text-slate-800">Date of Birth<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                    </div>

                    <label class="block text-sm font-black text-slate-800">Address<input name="address" value="{{ old('address') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black text-slate-800">Requested Role<select name="requested_role" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"><option value="">Select role</option>@foreach ($staffRoles as $value => $label)<option value="{{ $value }}" @selected(old('requested_role') === $value)>{{ $label }}</option>@endforeach</select></label>
                        <label class="text-sm font-black text-slate-800">Other Role<input name="other_role" value="{{ old('other_role') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                    </div>

                    <label class="block text-sm font-black text-slate-800">Email<input type="email" name="email" value="{{ old('email') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                    @error('email')
                        <p class="text-sm font-semibold text-pink-700">{{ $message }}</p>
                    @enderror

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black text-slate-800">Password<input type="password" name="password" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                        <label class="text-sm font-black text-slate-800">Confirm Password<input type="password" name="password_confirmation" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100"></label>
                    </div>

                    <button class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Submit for Approval</button>
                </form>
            </div>

            <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Approval Required</p>
                    <h2 class="mt-4 text-5xl leading-tight">Super Admin assigns the final department.</h2>
                    <p class="mt-5 text-sm leading-7 text-slate-300">Your requested role helps management review access, but no staff dashboard opens until approval is complete.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
