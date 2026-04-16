@extends('layouts.admin')

@section('title', 'Edit Profile | Printbuka Admin')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-black text-cyan-300">Admin Dashboard</a>
            <h1 class="mt-3 text-4xl">Edit profile.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                Update your admin/staff profile details. Your registered email address cannot be changed here.
            </p>
        </div>

        @if (session('status'))
            <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-black">First Name
                        <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('first_name') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Last Name
                        <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('last_name') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Phone
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('phone') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Company Name
                        <input name="companyName" value="{{ old('companyName', $user->companyName) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('companyName') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Address
                        <input name="address" value="{{ old('address', $user->address) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('address') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Date of Birth
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('date_of_birth') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Department
                        <select name="department" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}" @selected(old('department', $user->department) === $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                        @error('department') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Requested Role
                        <select name="requested_role" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            <option value="">Select role</option>
                            @foreach ($staffSignupRoles as $value => $label)
                                <option value="{{ $value }}" @selected(old('requested_role', $user->requested_role) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('requested_role') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black sm:col-span-2">Other Role
                        <input name="other_role" value="{{ old('other_role', $user->other_role) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('other_role') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black sm:col-span-2">Registered Email
                        <input value="{{ $user->email }}" readonly disabled class="mt-2 min-h-12 w-full rounded-md border border-slate-200 bg-slate-100 px-4 font-semibold text-slate-500">
                    </label>
                </div>

                <div class="space-y-5 rounded-md border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Profile Image</p>
                    @if ($user->profilePhotoUrl())
                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->displayName() }}" class="h-28 w-28 rounded-full border border-slate-200 object-cover">
                    @else
                        <div class="flex h-28 w-28 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-black text-slate-700">{{ $user->profileInitials() }}</div>
                    @endif
                    <label class="block text-sm font-black">
                        Upload New Photo
                        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold file:mr-3 file:rounded file:border-0 file:bg-pink-50 file:px-3 file:py-2 file:text-xs file:font-black file:text-pink-700">
                        @error('photo') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="flex items-center gap-3 text-sm font-bold text-slate-700">
                        <input type="checkbox" name="remove_photo" value="1" class="h-5 w-5 rounded border-slate-300 text-pink-600">
                        Remove current photo
                    </label>
                </div>
            </div>

            <div class="mt-8 rounded-md border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Change Password</p>
                <div class="mt-4 grid gap-5 sm:grid-cols-3">
                    <label class="text-sm font-black">Current Password
                        <input type="password" name="current_password" autocomplete="current-password" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('current_password') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">New Password
                        <input type="password" name="password" autocomplete="new-password" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        @error('password') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </label>
                    <label class="text-sm font-black">Confirm Password
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                    </label>
                </div>
            </div>

            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Profile</button>
        </form>
    </div>
@endsection
