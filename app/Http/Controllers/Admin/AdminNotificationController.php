<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Support\NotificationAudience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->canAdmin('*'), 403);

        return view('admin.notifications.index', [
            'notifications' => AppNotification::query()->latest()->paginate(15),
            'displayFormats' => NotificationAudience::displayFormats(),
            'types' => NotificationAudience::types(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canAdmin('*'), 403);

        $validated = $request->validate([
            'audience' => ['required', Rule::in(['public', 'staff'])],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::in(array_keys(NotificationAudience::types()))],
            'display_format' => ['required', Rule::in(array_keys(NotificationAudience::displayFormats()))],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        AppNotification::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Live notification published.');
    }

    public function destroy(Request $request, AppNotification $notification): RedirectResponse
    {
        abort_unless($request->user()->canAdmin('*'), 403);

        $notification->delete();

        return back()->with('status', 'Notification deleted.');
    }
}
