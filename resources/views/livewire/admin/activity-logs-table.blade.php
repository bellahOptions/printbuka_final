<div class="space-y-4">
    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[1220px]">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Route</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Subject</th>
                    <th>IP</th>
                    <th>Context</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr wire:key="activity-log-row-{{ $log->id }}">
                        <td>
                            <p class="font-black text-slate-900">{{ $log->created_at->format('M j, Y') }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>
                        <td>
                            <p class="font-black text-slate-900">{{ $log->user?->displayName() ?? 'Unknown' }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ $log->user?->email ?? 'No email' }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ config('printbuka_admin.role_labels.'.$log->role, $log->role) }}</p>
                        </td>
                        <td>
                            @if ($log->description)
                                <p class="font-bold text-slate-900">
                                    {{ $log->user?->displayName() ?? 'Someone' }} {{ $log->description }}
                                </p>
                                <p class="mt-0.5 text-xs font-semibold text-slate-400">{{ $log->action }}</p>
                            @else
                                <p class="font-semibold text-slate-800">{{ $log->action }}</p>
                            @endif
                        </td>
                        <td class="text-xs font-semibold text-slate-600">{{ $log->route_name ?? 'n/a' }}</td>
                        <td>
                            <span class="pb-badge {{ $log->method === 'DELETE' ? 'pb-badge-danger' : ($log->method === 'POST' || $log->method === 'PUT' || $log->method === 'PATCH' ? 'pb-badge-warning' : 'pb-badge-secondary') }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="font-black">{{ $log->status_code ?? '—' }}</td>
                        <td class="text-xs font-semibold text-slate-600">
                            {{ $log->subject_type ?? 'n/a' }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}
                        </td>
                        <td class="text-xs font-semibold text-slate-600">{{ $log->ip_address ?? 'n/a' }}</td>
                        <td>
                            <details>
                                <summary class="cursor-pointer text-xs font-black text-pink-700 hover:text-pink-900">View</summary>
                                <pre class="mt-2 max-h-48 overflow-auto rounded-md bg-slate-900 p-3 text-[11px] font-semibold text-slate-100">{{ json_encode($log->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="pb-empty">No logs found for this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-bold text-slate-400">
        Showing {{ number_format($logs->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('log', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more logs as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more logs...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
