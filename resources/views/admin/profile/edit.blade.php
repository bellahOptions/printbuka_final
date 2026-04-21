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
                    <div class="sm:col-span-2 rounded-md border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Access Assignment</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">
                            Role and department are assigned by the Super Admin only.
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-white px-3 py-1 text-xs font-black text-slate-700">
                                Role: {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                            <span class="rounded-md bg-white px-3 py-1 text-xs font-black text-slate-700">
                                Department: {{ $user->department ?: 'Unassigned' }}
                            </span>
                        </div>
                    </div>
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
                    <div class="block text-sm font-black">
                        Upload New Photo
                        <div class="mt-2">
                            <livewire:uploads.secure-image-upload
                                :key="'admin-profile-photo-'.$user->id"
                                input-name="photo_upload_path"
                                directory="staff-photos"
                                :max-size-kb="2048"
                                :max-files="1"
                                :multiple="false"
                                :initial-path="old('photo_upload_path')"
                            />
                        </div>
                        @error('photo') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        @error('photo_upload_path') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </div>
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
