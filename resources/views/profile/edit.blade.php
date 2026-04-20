@extends('layouts.theme')

@section('title', 'Edit Profile | Printbuka')

@section('content')
    <main class="bg-[#f4fbfb] px-4 py-16 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto max-w-6xl space-y-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <h1 class="mt-3 text-4xl">Edit profile.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                    Update your profile details.
                </p>
            </div>

            @if (session('status'))
                <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
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

                        @if ($user->role !== 'customer')
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
                        @endif

                        <label class="text-sm font-black sm:col-span-2">Registered Email
                            <input value="{{ $user->email }}" readonly disabled class="mt-2 min-h-12 w-full rounded-md border border-slate-200 bg-slate-100 px-4 font-semibold text-slate-500">
                        </label>
                    </div>

                    <div class="space-y-5 rounded-md border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Profile Image</p>
                        @if ($user->profilePhotoUrl())
                            <img src="/{{ $user->profilePhotoUrl() }}" alt="{{ $user->displayName() }}" class="h-28 w-28 rounded-full border border-slate-200 object-cover">
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

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Saved Delivery Addresses</p>
                        <h2 class="mt-2 text-2xl text-slate-950">Choose where your orders should go.</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            Add multiple delivery addresses and set one as default for faster checkout.
                        </p>
                    </div>
                </div>

                <div class="mt-6 rounded-md border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-black text-slate-900">Add New Address</p>
                    <form action="{{ route('profile.addresses.store') }}" method="POST" class="mt-4 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <label class="text-sm font-black">Label
                            <input name="label" value="{{ old('label') }}" placeholder="Home, Office, Warehouse" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('label') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="text-sm font-black">Recipient Name
                            <input name="recipient_name" value="{{ old('recipient_name', $user->displayName()) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('recipient_name') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="text-sm font-black">Phone
                            <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('phone') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="text-sm font-black">City
                            <input name="city" value="{{ old('city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('city') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="text-sm font-black sm:col-span-2">Street Address
                            <input name="street_address" value="{{ old('street_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('street_address') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="text-sm font-black sm:col-span-2">Landmark (Optional)
                            <input name="landmark" value="{{ old('landmark') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                            @error('landmark') <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p> @enderror
                        </label>
                        <label class="flex items-center gap-3 text-sm font-bold text-slate-700 sm:col-span-2">
                            <input type="checkbox" name="is_default" value="1" @checked(old('is_default')) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                            Set as default delivery address
                        </label>
                        <div class="sm:col-span-2">
                            <button type="submit" class="rounded-md bg-cyan-700 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-800">Add Address</button>
                        </div>
                    </form>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($deliveryAddresses as $deliveryAddress)
                        <article class="rounded-md border {{ $deliveryAddress->is_default ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-white' }} p-5">
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm font-black uppercase tracking-wide text-slate-800">
                                    {{ $deliveryAddress->label }}
                                    @if ($deliveryAddress->is_default)
                                        <span class="ml-2 rounded bg-emerald-600 px-2 py-1 text-xs font-black text-white">Default</span>
                                    @endif
                                </p>
                            </div>

                            <form action="{{ route('profile.addresses.update', $deliveryAddress) }}" method="POST" class="grid gap-4 sm:grid-cols-2">
                                @csrf
                                @method('PUT')

                                <label class="text-sm font-black">Label
                                    <input name="label" value="{{ old('label', $deliveryAddress->label) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="text-sm font-black">Recipient Name
                                    <input name="recipient_name" value="{{ old('recipient_name', $deliveryAddress->recipient_name) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="text-sm font-black">Phone
                                    <input name="phone" value="{{ old('phone', $deliveryAddress->phone) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="text-sm font-black">City
                                    <input name="city" value="{{ old('city', $deliveryAddress->city) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="text-sm font-black sm:col-span-2">Street Address
                                    <input name="street_address" value="{{ old('street_address', $deliveryAddress->address) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="text-sm font-black sm:col-span-2">Landmark
                                    <input name="landmark" value="{{ old('landmark', $deliveryAddress->landmark) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                                </label>
                                <label class="flex items-center gap-3 text-sm font-bold text-slate-700 sm:col-span-2">
                                    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $deliveryAddress->is_default)) class="h-5 w-5 rounded border-slate-300 text-pink-600">
                                    Set as default delivery address
                                </label>
                                <div class="sm:col-span-2">
                                    <button type="submit" class="rounded-md bg-pink-600 px-4 py-2 text-sm font-black text-white transition hover:bg-pink-700">Save Address</button>
                                </div>
                            </form>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                @unless ($deliveryAddress->is_default)
                                    <form action="{{ route('profile.addresses.default', $deliveryAddress) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-md border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-black text-cyan-700 transition hover:bg-cyan-100">Make Default</button>
                                    </form>
                                @endunless
                                <form action="{{ route('profile.addresses.destroy', $deliveryAddress) }}" method="POST" onsubmit="return confirm('Delete this delivery address?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-pink-200 bg-pink-50 px-4 py-2 text-sm font-black text-pink-700 transition hover:bg-pink-100">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-md border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-600">
                            No delivery address saved yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </section>
    </main>
@endsection
