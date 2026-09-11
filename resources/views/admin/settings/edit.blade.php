@extends('layouts.admin')

@section('title', 'Site Settings | Printbuka')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <!-- Page Header -->
        <div class="pb-page-header">
            <div>
                <h1 class="pb-page-title">Site settings</h1>
                <p class="pb-page-subtitle max-w-3xl">Manage notifications, announcements, contact details and maintenance mode.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="pb-btn pb-btn-md pb-btn-outline">← Dashboard</a>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="pb-alert pb-alert-success fade-in-up">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="fade-in-up section-delay-1">
            @csrf
            @method('PUT')

            <div class="pb-card p-6 lg:p-8 space-y-6">
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
                        <div class="pb-field">
                            <label class="pb-label">Site Name</label>
                            <input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Printbuka') }}" class="pb-input w-full" placeholder="Printbuka">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'sales@printbuka.com.ng') }}" class="pb-input w-full" placeholder="sales@printbuka.com.ng">
                        </div>
                        <div class="pb-field sm:col-span-2">
                            <label class="pb-label">Contact Phone</label>
                            <input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="pb-input w-full" placeholder="+234 XXX XXX XXXX">
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
                        <div class="pb-field">
                            <label class="pb-label">Maintenance Message</label>
                            <textarea name="maintenance_message" rows="4" data-rich-editor class="pb-textarea w-full" placeholder="Message shown to visitors during maintenance">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->role === 'super_admin')
                <!-- OTP Verification -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Two-Factor Verification (Email OTP)</h2>
                            <p class="text-sm text-slate-500">Super admin only — controls sign-in verification for staff without an authenticator app</p>
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-slate-200 px-5 py-4 transition-all duration-300 hover:border-pink-200 hover:bg-pink-50/30">
                        <input type="checkbox" name="otp_enabled" value="1" @checked(old('otp_enabled', ($settings['otp_enabled'] ?? '1') === '1')) class="h-5 w-5 rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                        <div>
                            <p class="text-sm font-black text-slate-900">Enable email OTP verification</p>
                            <p class="text-xs text-slate-500 mt-0.5">When on, staff without a confirmed authenticator app are sent a one-time code by email at login instead of being forced to set one up. Staff who already use an authenticator app are unaffected either way. Turning this on or off notifies every staff/admin account by email.</p>
                        </div>
                    </label>
                </div>
                @endif

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
                        <div class="pb-field">
                            <label class="pb-label">Notification Message</label>
                            <textarea name="notification_message" rows="3" data-rich-editor class="pb-textarea w-full" placeholder="Short notification banner message">{{ old('notification_message', $settings['notification_message'] ?? '') }}</textarea>
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Announcement</label>
                            <textarea name="announcement" rows="4" data-rich-editor class="pb-textarea w-full" placeholder="Detailed announcement content">{{ old('announcement', $settings['announcement'] ?? '') }}</textarea>
                        </div>
                        @if (auth()->user()?->role === 'super_admin')
                            <div class="pb-field">
                                <label class="pb-label">Important Action Email Recipients</label>
                                <textarea name="important_action_notification_emails" rows="3" class="pb-textarea w-full" placeholder="owner@example.com, finance@example.com">{{ old('important_action_notification_emails', $settings['important_action_notification_emails'] ?? '') }}</textarea>
                                <p class="text-xs font-bold text-slate-500">Comma, space, or line separated emails that receive important action alerts like invoice creation and staff access changes.</p>
                            </div>
                        @endif
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
                        <div class="pb-field">
                            <label class="pb-label">Paper Types</label>
                            <textarea name="paper_types" rows="4" class="pb-textarea w-full" placeholder="One per line">{{ old('paper_types', $settings['paper_types'] ?? implode(PHP_EOL, config('printbuka_admin.materials', []))) }}</textarea>
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Paper Sizes</label>
                            <textarea name="paper_sizes" rows="4" class="pb-textarea w-full" placeholder="One per line">{{ old('paper_sizes', $settings['paper_sizes'] ?? implode(PHP_EOL, config('printbuka_admin.sizes', []))) }}</textarea>
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Finishing Options</label>
                            <textarea name="finishings" rows="4" class="pb-textarea w-full" placeholder="One per line">{{ old('finishings', $settings['finishings'] ?? implode(PHP_EOL, config('printbuka_admin.finishes', []))) }}</textarea>
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Paper Densities</label>
                            <textarea name="paper_densities" rows="4" class="pb-textarea w-full" placeholder="One per line">{{ old('paper_densities', $settings['paper_densities'] ?? implode(PHP_EOL, ['100gsm', '115gsm', '150gsm', '170gsm', '200gsm', '250gsm', '300gsm', '350gsm', 'Self Adhesive', 'Gift Item', 'Custom'])) }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 border border-indigo-200">
                            <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Reminders</h2>
                            <p class="text-sm text-slate-500">Pricing is now managed from the <a href="{{ route('admin.pricelist.index') }}" class="text-pink-600 underline">Pricelist</a> page.</p>
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="pb-field sm:col-span-2">
                            <label class="pb-label">Pending Job Reminder Hours</label>
                            <input type="number" min="1" max="240" name="pending_job_reminder_hours" value="{{ old('pending_job_reminder_hours', $settings['pending_job_reminder_hours'] ?? 24) }}" class="pb-input w-full" />
                            <p class="text-xs font-bold text-slate-500">Staff reminder emails include jobs that have remained in a phase for at least this number of hours.</p>
                        </div>
                    </div>
                </div>

                @if (auth()->user()?->role === 'super_admin')
                    <!-- Homepage Images -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                                <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Homepage Images</h2>
                                <p class="text-sm text-slate-500">Hero slider, category fallback and promo banner images shown on the homepage. Leave blank to keep the default image.</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-3">Hero Slider (5 slides)</p>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="pb-field">
                                            <label class="pb-label">Hero Image {{ $i }}</label>
                                            <input type="url" name="home_hero_image_{{ $i }}" value="{{ old('home_hero_image_'.$i, $settings['home_hero_image_'.$i] ?? '') }}" class="pb-input w-full" placeholder="https://...">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-3">Category Fallback Images (6, used when a category has no image)</p>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="pb-field">
                                            <label class="pb-label">Category Fallback {{ $i }}</label>
                                            <input type="url" name="home_category_fallback_image_{{ $i }}" value="{{ old('home_category_fallback_image_'.$i, $settings['home_category_fallback_image_'.$i] ?? '') }}" class="pb-input w-full" placeholder="https://...">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-3">Promotional Banners (2)</p>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    @for ($i = 1; $i <= 2; $i++)
                                        <div class="pb-field">
                                            <label class="pb-label">Promo Banner {{ $i }}</label>
                                            <input type="url" name="home_promo_image_{{ $i }}" value="{{ old('home_promo_image_'.$i, $settings['home_promo_image_'.$i] ?? '') }}" class="pb-input w-full" placeholder="https://...">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()?->role === 'super_admin')
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-violet-100 to-violet-50 border border-violet-200">
                                <svg class="w-5 h-5 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2m-2 0h14l-1 11H6L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-950">Company Account Details (Process & Technology Manager)</h2>
                                <p class="text-sm text-slate-500">Used on quotations, invoices and invoice-related emails.</p>
                            </div>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2 mb-8">
                            <div class="pb-field">
                                <label class="pb-label">Account Name</label>
                                <input name="company_account_name" value="{{ old('company_account_name', $settings['company_account_name'] ?? '') }}" class="pb-input w-full" placeholder="Printbuka Limited">
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Account Number</label>
                                <input name="company_account_number" value="{{ old('company_account_number', $settings['company_account_number'] ?? '') }}" class="pb-input w-full" placeholder="0123456789">
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Bank Name</label>
                                <input name="company_account_bank_name" value="{{ old('company_account_bank_name', $settings['company_account_bank_name'] ?? '') }}" class="pb-input w-full" placeholder="GTBank">
                            </div>
                            <div class="pb-field sm:col-span-2">
                                <label class="pb-label">Account Note (Optional)</label>
                                <textarea name="company_account_note" rows="3" data-rich-editor class="pb-textarea w-full" placeholder="Use your invoice number as payment reference">{{ old('company_account_note', $settings['company_account_note'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                @endif

                <!-- Form Actions -->
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                    <button type="submit" class="pb-btn pb-btn-lg pb-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Settings
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
