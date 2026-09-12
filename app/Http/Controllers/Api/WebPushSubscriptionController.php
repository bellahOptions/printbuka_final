<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebPushSubscriptionController extends Controller
{
    /**
     * Register (or refresh) a browser's Push subscription for the current
     * staff member. Called with the object from PushManager.subscribe().
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $request->user()->updateWebPushSubscription(
            endpoint: $validated['endpoint'],
            key: $validated['keys']['p256dh'],
            token: $validated['keys']['auth'],
            contentEncoding: null,
        );

        return response()->json(['message' => 'Browser subscribed.'], 201);
    }

    /**
     * Unsubscribe this browser (staff member turned notifications off, or
     * the browser's own subscription expired/rotated).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $request->user()->deleteWebPushSubscription($request->string('endpoint')->toString());

        return response()->json(['message' => 'Browser unsubscribed.']);
    }
}
