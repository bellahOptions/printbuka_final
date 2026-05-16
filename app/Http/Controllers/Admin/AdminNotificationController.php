<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->latest()
            ->get()
            ->groupBy(fn (DatabaseNotification $notification): string => (string) data_get($notification->data, 'broadcast_id', $notification->id))
            ->map(fn ($group) => $group->first())
            ->values();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'types' => $this->types(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audience' => ['required', Rule::in(['staff', 'customers', 'all'])],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::in(array_keys($this->types()))],
            'action_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $recipients = User::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->when($validated['audience'] === 'staff', fn ($query) => $query->whereNotIn('role', ['customer', 'staff_pending']))
            ->when($validated['audience'] === 'customers', fn ($query) => $query->where('role', 'customer'))
            ->get();

        Notification::send($recipients, new AdminBroadcastNotification(
            (string) Str::uuid(),
            $validated['title'],
            $validated['message'],
            $validated['type'],
            $validated['action_url'] ?? null,
        ));

        return back()->with('status', 'Notification sent to '.$recipients->count().' recipient(s).');
    }

    public function destroy(string $broadcastId): RedirectResponse
    {
        DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->where('data->broadcast_id', $broadcastId)
            ->delete();

        return back()->with('status', 'Notification deleted.');
    }

    private function types(): array
    {
        return [
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
        ];
    }
}
