<div class="space-y-4">
    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[1220px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-4">When</th>
                    <th class="px-5 py-4">Admin</th>
                    <th class="px-5 py-4">Action</th>
                    <th class="px-5 py-4">Route</th>
                    <th class="px-5 py-4">Method</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Subject</th>
                    <th class="px-5 py-4">IP</th>
                    <th class="px-5 py-4">Context</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr wire:key="activity-log-row-{{ $log->id }}">
                        <td class="px-5 py-4">
                            <p class="font-black text-slate-900">{{ $log->created_at->format('M j, Y') }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-black text-slate-900">{{ $log->user?->displayName() ?? 'Unknown' }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ $log->user?->email ?? 'No email' }}</p>
                            <p class="text-xs font-semibold text-slate-500">{{ config('printbuka_admin.role_labels.'.$log->role, $log->role) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($log->description)
                                <p class="font-bold text-slate-900">
                                    {{ $log->user?->displayName() ?? 'Someone' }} {{ $log->description }}
                                </p>
                                <p class="mt-0.5 text-xs font-semibold text-slate-400">{{ $log->action }}</p>
                            @else
                                <p class="font-semibold text-slate-800">{{ $log->action }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ $log->route_name ?? 'n/a' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-black {{ $log->method === 'DELETE' ? 'bg-red-100 text-red-700' : ($log->method === 'POST' || $log->method === 'PUT' || $log->method === 'PATCH' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-black">{{ $log->status_code ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs font-semibold text-slate-600">
                            {{ $log->subject_type ?? 'n/a' }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}
                        </td>
                        <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ $log->ip_address ?? 'n/a' }}</td>
                        <td class="px-5 py-4">
                            <details>
                                <summary class="cursor-pointer text-xs font-black text-pink-700 hover:text-pink-900">View</summary>
                                <pre class="mt-2 max-h-48 overflow-auto rounded-md bg-slate-900 p-3 text-[11px] font-semibold text-slate-100">{{ json_encode($log->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-slate-500">No logs found for this filter.</td>
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
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
