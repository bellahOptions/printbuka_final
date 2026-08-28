{{-- Bare fragment — loaded via fetch() into the Staff Spotlight modal
     (see resources/views/admin/_partials/staff-spotlight-modal.blade.php).
     Not a full page: no @extends, no layout. --}}
@php($roleLabels = config('printbuka_admin.role_labels', []))

<div class="space-y-6">
    @foreach ([['label' => 'Staff of the Week', 'leaders' => $weekLeaders, 'icon' => '⭐'], ['label' => 'Staff of the Month', 'leaders' => $monthLeaders, 'icon' => '🏆']] as $section)
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-2">{{ $section['icon'] }} {{ $section['label'] }}</p>

            @if ($section['leaders']->isEmpty())
                <p class="text-sm text-slate-400">Not enough data yet — check back once attendance has been recorded.</p>
            @else
                <div class="space-y-2">
                    @foreach ($section['leaders'] as $entry)
                        <div class="flex items-center justify-between gap-3 rounded-xl border p-3
                            {{ $entry->rank === 1 ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-black
                                    {{ $entry->rank === 1 ? 'bg-amber-400 text-amber-950' : 'bg-slate-100 text-slate-600' }}">
                                    #{{ $entry->rank }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-slate-900">{{ $entry->user?->displayName() ?? 'Former staff' }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $roleLabels[$entry->user?->role] ?? $entry->user?->role }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-black text-slate-900">{{ number_format((float) $entry->total_score, 1) }}<span class="text-xs font-bold text-slate-400">/100</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    <p class="text-[11px] leading-5 text-slate-400">
        Score blends attendance (40%), latest supervisor evaluation (30%), portal activity relative to the team (20%), and overtime (10%).
    </p>
</div>
