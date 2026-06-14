<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        if (! $user->hasAdminAccess()) {
            return response()->json(['message' => 'Your account does not have staff access.'], 403);
        }

        if ($user->employment_status === 'suspended' || $user->employment_status === 'terminated') {
            return response()->json(['message' => 'Your account has been '.$user->employment_status.'.'], 403);
        }

        // One active mobile token per user — revoke previous before issuing new one
        $user->tokens()->where('name', 'mobile')->delete();

        $token = $user->createToken('mobile', ['staff'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->displayName(),
                'email'      => $user->email,
                'role'       => $user->role,
                'department' => $user->department,
                'avatar'     => $user->getProfilePhotoUrlAttribute(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'               => $user->id,
            'name'             => $user->displayName(),
            'email'            => $user->email,
            'role'             => $user->role,
            'department'       => $user->department,
            'avatar'           => $user->getProfilePhotoUrlAttribute(),
            'employment_status'=> $user->employmentStatusLabel(),
        ]);
    }
}
