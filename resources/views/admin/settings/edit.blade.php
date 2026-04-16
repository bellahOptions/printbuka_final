@extends('layouts.admin')

@section('title', 'Site Settings | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center gap-2 text-sm font-black text-cyan-300 transition-colors hover:text-cyan-200">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Admin Dashboard
                </a>
            </div>
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">Site settings</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">Manage notifications, announcements, contact details and maintenance mode.</p>
                </div>
                <div class="hidden sm:block">
                    <div class="rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 p-3 border border-cyan-500/20">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="fade-in-up rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="fade-in-up section-delay-1">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm lg:p-8 space-y-6">
                <!-- Basic Settings -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                            <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">General Settings</h2>
                            <p class="text-sm text-slate-500">Basic site information</p>
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Site Name</label>
                            <input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Printbuka') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="Printbuka">
                        </div>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'sales@printbuka.com.ng') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="sales@printbuka.com.ng">
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">Contact Phone</label>
                            <input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20" placeholder="+234 XXX XXX XXXX">
                        </div>
                    </div>
                </div>

                <!-- Maintenance Mode -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 border border-amber-200">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Maintenance Mode</h2>
                            <p class="text-sm text-slate-500">Control site availability</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                            <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', ($settings['maintenance_mode'] ?? '0') === '1')) class="h-5 w-5 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                            <div>
                                <p class="text-sm font-black text-slate-900">Enable Maintenance Mode</p>
                                <p class="text-xs text-slate-500 mt-0.5">Site will be inaccessible to visitors</p>
                            </div>
                        </label>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Maintenance Message</label>
                            <textarea name="maintenance_message" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Message shown to visitors during maintenance">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                            <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Public Notifications</h2>
                            <p class="text-sm text-slate-500">Messages displayed on the website</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Notification Message</label>
                            <textarea name="notification_message" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Short notification banner message">{{ old('notification_message', $settings['notification_message'] ?? '') }}</textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Announcement</label>
                            <textarea name="announcement" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="Detailed announcement content">{{ old('announcement', $settings['announcement'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Print Options -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Print Options</h2>
                            <p class="text-sm text-slate-500">Configure available print specifications</p>
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Paper Types</label>
                            <textarea name="paper_types" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="One per line">{{ old('paper_types', $settings['paper_types'] ?? implode(PHP_EOL, config('printbuka_admin.materials', []))) }}</textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Paper Sizes</label>
                            <textarea name="paper_sizes" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="One per line">{{ old('paper_sizes', $settings['paper_sizes'] ?? implode(PHP_EOL, config('printbuka_admin.sizes', []))) }}</textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Finishing Options</label>
                            <textarea name="finishings" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="One per line">{{ old('finishings', $settings['finishings'] ?? implode(PHP_EOL, config('printbuka_admin.finishes', []))) }}</textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-black text-slate-700">Paper Densities</label>
                            <textarea name="paper_densities" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 resize-none" placeholder="One per line">{{ old('paper_densities', $settings['paper_densities'] ?? implode(PHP_EOL, ['100gsm', '115gsm', '150gsm', '170gsm', '200gsm', '250gsm', '300gsm', '350gsm', 'Self Adhesive', 'Gift Item', 'Custom'])) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                    <button type="submit" class="btn-primary group relative overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-8 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Settings
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
        .section-delay-1 { animation-delay: 0.05s; }
    </style>
@endsection