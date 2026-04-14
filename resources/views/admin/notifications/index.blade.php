@extends('layouts.admin')

@section('title', 'Live Notifications | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Page Header -->
        <div class="fade-in-up flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-pink-600"></span>
                    </span>
                    <p class="text-xs font-black uppercase tracking-wider text-pink-700">Real-time Communication</p>
                </div>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-950">Live Notifications</h1>
                <p class="mt-2 text-sm text-slate-600 max-w-2xl">Create and manage real-time broadcasts for staff and public channels with precision timing.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-5 py-3 text-sm font-black text-slate-700 backdrop-blur-sm transition-all duration-300 hover:border-pink-300 hover:bg-pink-50/50 hover:text-pink-700">
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Main Grid -->
        <div class="grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
            <!-- Create Notification Card -->
            <section class="fade-in-up section-delay-1 card-hover rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white via-white to-pink-50/30 p-8 shadow-xl shadow-slate-200/50 backdrop-blur-sm">
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-cyan-100 to-cyan-50 border border-cyan-200">
                            <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Super Admin Broadcast</p>
                    </div>
                    <h2 class="text-3xl font-black text-slate-950">Create live notification</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Send targeted messages to staff members or broadcast to the public website.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 animate-in slide-in-from-top-2 duration-300 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Audience Selection -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Target Audience
                        </label>
                        <select name="audience" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                            <option value="staff" @selected(old('audience') === 'staff')>👥 Staff / Administrators</option>
                            <option value="public" @selected(old('audience') === 'public')>🌐 Public Website Visitors</option>
                        </select>
                    </div>

                    <!-- Type & Format Grid -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Notification Type
                            </label>
                            <select name="type" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', 'info') === $value)>
                                        @if($value === 'info') ℹ️ 
                                        @elseif($value === 'success') ✅ 
                                        @elseif($value === 'warning') ⚠️ 
                                        @elseif($value === 'error') ❌ 
                                        @endif
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                                Display Format
                            </label>
                            <select name="display_format" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                                @foreach ($displayFormats as $value => $label)
                                    <option value="{{ $value }}" @selected(old('display_format', 'alert') === $value)>
                                        @if($value === 'alert') 🔔 
                                        @elseif($value === 'banner') 📢 
                                        @elseif($value === 'modal') 🪟 
                                        @elseif($value === 'toast') 🍞 
                                        @endif
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Title Input -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 18h12"/>
                            </svg>
                            Notification Title
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20"
                               placeholder="Enter a clear, concise title...">
                    </div>

                    <!-- Message Textarea -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Message Content
                        </label>
                        <textarea name="message" rows="5" required 
                                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 resize-none"
                                  placeholder="Write your notification message here...">{{ old('message') }}</textarea>
                        <p class="text-xs text-slate-500 flex justify-end">
                            <span class="message-counter">0</span> characters
                        </p>
                    </div>

                    <!-- Date/Time Grid -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Starts At
                            </label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                            <p class="text-xs text-slate-500">Leave empty for immediate publication</p>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-black text-slate-700">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ends At
                            </label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 transition-all duration-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                            <p class="text-xs text-slate-500">Leave empty for indefinite duration</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-[1.02]">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            Publish Notification
                        </span>
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                    </button>
                </form>
            </section>

            <!-- Notification Log Section -->
            <section class="fade-in-up section-delay-2 rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white via-white to-cyan-50/30 p-8 shadow-xl shadow-slate-200/50 backdrop-blur-sm">
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50 border border-pink-200">
                            <svg class="w-5 h-5 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-black uppercase tracking-wider text-pink-700">Notification History</p>
                    </div>
                    <h2 class="text-3xl font-black text-slate-950">Recent broadcasts</h2>
                    <p class="mt-2 text-sm text-slate-600">View and manage all sent notifications</p>
                </div>

                <!-- Notifications List -->
                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse ($notifications as $notification)
                        <article class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:border-pink-200 hover:shadow-md">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-black text-slate-900 group-hover:text-pink-700 transition-colors">{{ $notification->title }}</h3>
                                        @php
                                            $typeColors = [
                                                'info' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'error' => 'bg-red-50 text-red-700 border-red-200',
                                            ];
                                            $typeColor = $typeColors[$notification->type] ?? $typeColors['info'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[0.6rem] font-black uppercase tracking-wider {{ $typeColor }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ $notification->type }}
                                        </span>
                                    </div>
                                    <p class="text-sm leading-relaxed text-slate-600">{{ $notification->message }}</p>
                                    <div class="flex flex-wrap items-center gap-3 text-xs font-black uppercase tracking-wider">
                                        <span class="flex items-center gap-1 text-cyan-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            {{ ucfirst($notification->audience) }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="flex items-center gap-1 text-slate-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                            </svg>
                                            {{ $displayFormats[$notification->display_format] ?? $notification->display_format ?? 'Alert' }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span class="flex items-center gap-1 text-slate-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button class="group/btn flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-600 transition-all duration-300 hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                                        <svg class="w-4 h-4 transition-transform duration-300 group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="flex flex-col items-center justify-center py-16 px-4">
                            <div class="rounded-full bg-slate-100 p-4 mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-500 text-center">No notifications have been created yet.</p>
                            <p class="text-xs text-slate-400 mt-1">Create your first notification using the form.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="mt-6 border-t border-slate-200 pt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    <style>
        /* Custom scrollbar for light theme */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #ec4899;
        }
        
        /* Animation keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .section-delay-1 { animation-delay: 0.05s; }
        .section-delay-2 { animation-delay: 0.1s; }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.02);
            border-color: #fbcfe8;
        }
        
        .btn-primary {
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .btn-primary:hover::after {
            opacity: 1;
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
    </style>

    <script>
        // Character counter for message textarea
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[name="message"]');
            const counter = document.querySelector('.message-counter');
            
            if (textarea && counter) {
                const updateCounter = () => {
                    counter.textContent = textarea.value.length;
                };
                
                textarea.addEventListener('input', updateCounter);
                updateCounter();
            }
        });
    </script>
@endsection
