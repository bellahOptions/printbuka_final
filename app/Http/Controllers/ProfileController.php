<?php

namespace App\Http\Controllers;

use App\Support\LivewireSecureUploads;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasAdminAccess()) {
            return redirect()->route('admin.profile.edit');
        }

        return view('profile.edit', $this->userViewData($user));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasAdminAccess()) {
            return redirect()->route('admin.profile.edit');
        }

        $this->updateProfile($request, $user);

        return back()->with('status', 'Profile updated successfully.');
    }

    public function editAdmin(Request $request): View
    {
        return view('admin.profile.edit', $this->adminViewData($request->user()));
    }

    public function updateAdmin(Request $request): RedirectResponse
    {
        $this->updateProfile($request, $request->user());

        return redirect()->route('admin.profile.edit')->with('status', 'Profile updated successfully.');
    }

    private function userViewData(User $user): array
    {
        return [
            'user' => $user,
            'deliveryAddresses' => $user->deliveryAddresses()->get(),
        ];
    }

    private function adminViewData(User $user): array
    {
        return [
            'user' => $user,
            'deliveryAddresses' => $user->deliveryAddresses()->get(),
            'roleLabels' => config('printbuka_admin.role_labels', []),
        ];
    }

    private function updateProfile(Request $request, User $user): void
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'companyName' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'remove_photo' => ['nullable', 'boolean'],
            'photo_upload_path' => ['nullable', 'string', 'max:255'],
            'photo' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'dimensions:min_width=80,min_height=80,max_width=4000,max_height=4000',
            ],
            'current_password' => ['nullable', 'string', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];

        $validated = $request->validate($rules);

        $updates = Arr::only($validated, [
            'first_name',
            'last_name',
            'phone',
            'companyName',
            'address',
            'date_of_birth',
        ]);

        // Decide photo folder by user type
        $photoFolder = $user->hasAdminAccess() ? 'staff-photos' : 'user-photos';

        if ($request->boolean('remove_photo')) {
            if (filled($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $updates['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')->store($photoFolder, 'public');

            if (filled($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $updates['photo'] = $newPhotoPath;
        } elseif (filled($validated['photo_upload_path'] ?? null) && ! $request->boolean('remove_photo')) {
            $livewirePhotoPath = LivewireSecureUploads::consumePath(
                $request,
                (string) $validated['photo_upload_path'],
                [$photoFolder]
            );

            if (! $livewirePhotoPath) {
                throw ValidationException::withMessages([
                    'photo' => 'The uploaded photo is invalid or expired. Please upload it again.',
                ]);
            }

            if (filled($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $updates['photo'] = $livewirePhotoPath;
        }

        if (filled($validated['password'] ?? null)) {
            $updates['password'] = $validated['password'];
        }

        $user->fill($updates)->save();
    }
}
