<div class="pb-table-wrapper">
    <table class="pb-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>In / Out</th>
                <th>Status</th>
                <th>Correct</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr wire:key="attendance-record-{{ $record->id }}">
                    <td class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($record->work_date)->format('D, M j Y') }}</td>
                    <td class="text-sm text-slate-600">
                        {{ $record->clock_in_at?->format('h:i A') ?? '—' }} – {{ $record->clock_out_at?->format('h:i A') ?? '—' }}
                        @if ($record->clock_in_at && $record->corrected_by_id && $record->clock_in_within_geofence === null)
                            <p class="text-xs text-violet-700 mt-0.5">Manually entered by {{ $record->correctedBy?->displayName() }}</p>
                        @endif
                        @if ($record->flagged_reason)
                            <p class="text-xs text-amber-700 mt-0.5">{{ $record->flagged_reason }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="pb-badge {{ $record->statusBadgeClass() }}">{{ $record->statusLabel() }}</span>
                        @if ($record->hasOvertime())
                            <span class="pb-badge pb-badge-purple">+{{ $record->overtimeLabel() }} OT</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.attendance.correct', $record) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="pb-select text-xs">
                                @foreach (['present', 'late', 'absent', 'on_leave', 'half_day'] as $status)
                                    <option value="{{ $status }}" @selected($record->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="text-xs font-black text-pink-600 hover:text-pink-800">Save</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-12 text-center text-sm text-slate-400 font-semibold">No attendance records yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($records->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('record', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more records as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more records...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-outline">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
