@extends('layouts.admin')

@section('title', 'Site Settings | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8"><a href="{{ route('admin.dashboard') }}" class="text-sm font-black text-cyan-300">Admin Dashboard</a><h1 class="mt-3 text-4xl">Site settings.</h1><p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Manage notifications, announcements, contact details and maintenance mode.</p></div>
        @if (session('status'))<p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>@endif
        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-black">Site Name<input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Printbuka') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Contact Email<input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'sales@printbuka.com.ng') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="text-sm font-black">Contact Phone<input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                <label class="flex items-center gap-3 rounded-md border border-slate-200 px-4 py-3 text-sm font-black"><input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', ($settings['maintenance_mode'] ?? '0') === '1')) class="h-5 w-5 rounded border-slate-300 text-pink-600"> Maintenance Mode</label>
                <label class="text-sm font-black sm:col-span-2">Notification Message<textarea name="notification_message" rows="3" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('notification_message', $settings['notification_message'] ?? '') }}</textarea></label>
                <label class="text-sm font-black sm:col-span-2">Announcement<textarea name="announcement" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('announcement', $settings['announcement'] ?? '') }}</textarea></label>
                <label class="text-sm font-black sm:col-span-2">Maintenance Message<textarea name="maintenance_message" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea></label>
            </div>
            <button class="mt-6 rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Save Settings</button>
        </form>
    </div>
@endsection
