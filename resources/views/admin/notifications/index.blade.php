@extends('layouts.admin')

@section('title', 'Notifications | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="pb-page-header">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Laravel Database Notifications</p>
                <h1 class="pb-page-title">Notifications</h1>
                <p class="pb-page-subtitle">Send standard Laravel database notifications to staff, customers, or all active users.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="pb-card">
                <div class="pb-card-header">
                    <h2 class="pb-card-title">Send notification</h2>
                </div>
                <form action="{{ route('admin.notifications.store') }}" method="POST" class="pb-card-content space-y-5">
                    @csrf

                    <div class="pb-field">
                        <label class="pb-label">Audience</label>
                        <select name="audience" class="pb-select" required>
                            <option value="staff" @selected(old('audience') === 'staff')>Staff</option>
                            <option value="customers" @selected(old('audience') === 'customers')>Customers</option>
                            <option value="all" @selected(old('audience') === 'all')>All active users</option>
                        </select>
                        @error('audience') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Type</label>
                        <select name="type" class="pb-select" required>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', 'info') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Title</label>
                        <input name="title" value="{{ old('title') }}" class="pb-input" required>
                        @error('title') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Message</label>
                        <textarea name="message" rows="5" data-rich-editor class="pb-textarea" required>{{ old('message') }}</textarea>
                        @error('message') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Action URL</label>
                        <input name="action_url" type="url" value="{{ old('action_url') }}" placeholder="https://..." class="pb-input">
                        @error('action_url') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>

                    <button class="pb-btn pb-btn-lg pb-btn-primary w-full">Send Notification</button>
                </form>
            </section>

            <section class="pb-card">
                <div class="pb-card-header">
                    <h2 class="pb-card-title">Recent sent notifications</h2>
                </div>
                <div class="pb-card-content pt-0 space-y-3">
                    @forelse ($notifications as $notification)
                        @php($data = $notification->data)
                        <article class="pb-card p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $data['title'] ?? 'Notification' }}</p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $data['type'] ?? 'info' }} · {{ $notification->created_at->diffForHumans() }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-700">{{ $data['message'] ?? '' }}</p>
                                </div>
                                <form action="{{ route('admin.notifications.destroy', $data['broadcast_id'] ?? $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="pb-btn pb-btn-sm pb-btn-outline text-xs">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="pb-empty">
                            <p class="pb-empty-title">No notifications sent yet.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
