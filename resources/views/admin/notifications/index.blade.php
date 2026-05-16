@extends('layouts.admin')

@section('title', 'Notifications | Printbuka')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-pink-700">Laravel Database Notifications</p>
            <h1 class="mt-2 text-4xl font-black text-slate-950">Notifications</h1>
            <p class="mt-2 text-sm font-semibold text-slate-600">Send standard Laravel database notifications to staff, customers, or all active users.</p>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Send notification</h2>
                <form action="{{ route('admin.notifications.store') }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="text-sm font-black text-slate-700">Audience</label>
                        <select name="audience" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold" required>
                            <option value="staff" @selected(old('audience') === 'staff')>Staff</option>
                            <option value="customers" @selected(old('audience') === 'customers')>Customers</option>
                            <option value="all" @selected(old('audience') === 'all')>All active users</option>
                        </select>
                        @error('audience') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Type</label>
                        <select name="type" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold" required>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', 'info') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Title</label>
                        <input name="title" value="{{ old('title') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold" required>
                        @error('title') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Message</label>
                        <textarea name="message" rows="5" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-3 text-sm font-semibold" required>{{ old('message') }}</textarea>
                        @error('message') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Action URL</label>
                        <input name="action_url" type="url" value="{{ old('action_url') }}" placeholder="https://..." class="mt-2 min-h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        @error('action_url') <p class="mt-2 text-sm font-semibold text-pink-700">{{ $message }}</p> @enderror
                    </div>

                    <button class="min-h-12 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Send Notification</button>
                </form>
            </section>

            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Recent sent notifications</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($notifications as $notification)
                        @php($data = $notification->data)
                        <article class="rounded-md border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-black text-slate-950">{{ $data['title'] ?? 'Notification' }}</p>
                                    <p class="mt-1 text-xs font-black uppercase tracking-wide text-slate-500">{{ $data['type'] ?? 'info' }} · {{ $notification->created_at->diffForHumans() }}</p>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-700">{{ $data['message'] ?? '' }}</p>
                                </div>
                                <form action="{{ route('admin.notifications.destroy', $data['broadcast_id'] ?? $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-md border border-slate-200 px-3 py-2 text-xs font-black text-slate-600 transition hover:border-pink-300 hover:text-pink-700">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500">No notifications sent yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
