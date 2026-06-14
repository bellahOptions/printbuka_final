<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffPushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffDeviceController extends Controller
{
    /**
     * Register or refresh a device token.
     * Call this every time the NativePHP app receives a new FCM token
     * (tokens rotate after app reinstall or OS refresh).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => ['required', 'string', 'max:500'],
            'platform'     => ['required', 'in:ios,android'],
        ]);

        // upsert on device_token so re-registering the same device refreshes
        // last_active_at and updates the user_id if the token was transferred.
        StaffPushSubscription::updateOrCreate(
            ['device_token' => $request->device_token],
            [
                'user_id'        => $request->user()->id,
                'platform'       => $request->platform,
                'last_active_at' => now(),
            ]
        );

        return response()->json(['message' => 'Device registered.'], 201);
    }

    /**
     * Unregister a device (logout from this device / user revoked push permission).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => ['required', 'string'],
        ]);

        StaffPushSubscription::where('user_id', $request->user()->id)
            ->where('device_token', $request->device_token)
            ->delete();

        return response()->json(['message' => 'Device unregistered.']);
    }
}
