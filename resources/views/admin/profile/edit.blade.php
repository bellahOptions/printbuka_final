@extends('layouts.admin')

@section('title', 'Edit Profile | Printbuka Admin')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="pb-page-header">
            <div>
                <h1 class="pb-page-title">Edit profile</h1>
                <p class="pb-page-subtitle max-w-3xl">Update your admin/staff profile details. Your registered email address cannot be changed here.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="pb-btn pb-btn-md pb-btn-outline">← Dashboard</a>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="pb-card p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">First Name</label>
                        <input name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="pb-input w-full">
                        @error('first_name') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Last Name</label>
                        <input name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="pb-input w-full">
                        @error('last_name') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Phone</label>
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="pb-input w-full">
                        @error('phone') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Company Name</label>
                        <input name="companyName" value="{{ old('companyName', $user->companyName) }}" class="pb-input w-full">
                        @error('companyName') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Address</label>
                        <input name="address" value="{{ old('address', $user->address) }}" class="pb-input w-full">
                        @error('address') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="pb-input w-full">
                        @error('date_of_birth') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 rounded-md border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Access Assignment</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">
                            Role and department are assigned by the Process & Technology Manager only.
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="pb-badge pb-badge-secondary">
                                Role: {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                            <span class="pb-badge pb-badge-secondary">
                                Department: {{ $user->department ?: 'Unassigned' }}
                            </span>
                        </div>
                    </div>
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label">Registered Email</label>
                        <input value="{{ $user->email }}" readonly disabled class="pb-input w-full">
                    </div>
                </div>

                <div class="space-y-5 rounded-md border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Profile Image</p>
                    @if ($user->profilePhotoUrl())
                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->displayName() }}" class="h-28 w-28 rounded-full border border-slate-200 object-cover">
                    @else
                        <div class="flex h-28 w-28 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-black text-slate-700">{{ $user->profileInitials() }}</div>
                    @endif
                    <div class="pb-field">
                        <label class="pb-label">Upload New Photo</label>
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
                        @error('photo') <p class="pb-field-error">{{ $message }}</p> @enderror
                        @error('photo_upload_path') <p class="pb-field-error">{{ $message }}</p> @enderror
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
                    <div class="pb-field">
                        <label class="pb-label">Current Password</label>
                        <input type="password" name="current_password" autocomplete="current-password" class="pb-input w-full">
                        @error('current_password') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">New Password</label>
                        <input type="password" name="password" autocomplete="new-password" class="pb-input w-full">
                        @error('password') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="pb-input w-full">
                    </div>
                </div>
            </div>

            <button class="pb-btn pb-btn-md pb-btn-primary mt-6">Save Profile</button>
        </form>
    </div>
@endsection
