<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="border-b border-slate-200 bg-slate-50">
            <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                <th class="px-5 py-3.5 text-left">Period</th>
                <th class="px-5 py-3.5 text-left">Entries</th>
                <th class="px-5 py-3.5 text-left">Total Net Payroll</th>
                <th class="px-5 py-3.5 text-left">Status</th>
                <th class="px-5 py-3.5 text-left">Payment Date</th>
                <th class="px-5 py-3.5 text-left">Created By</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($runs as $run)
            <tr wire:key="payroll-run-row-{{ $run->id }}" class="hover:bg-slate-50/70 transition">
                <td class="px-5 py-4 font-black text-slate-900">{{ $run->periodLabel() }}</td>
                <td class="px-5 py-4 text-sm text-slate-600">{{ $run->entries()->count() }} staff</td>
                <td class="px-5 py-4 font-black text-slate-900">₦{{ number_format($run->totalNetPayroll(), 2) }}</td>
                <td class="px-5 py-4">
                    <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $run->statusBadgeClass() }}">{{ ucfirst($run->status) }}</span>
                </td>
                <td class="px-5 py-4 text-sm text-slate-600">{{ $run->payment_date?->format('M j, Y') ?? '—' }}</td>
                <td class="px-5 py-4 text-sm text-slate-600">{{ $run->createdBy?->displayName() }}</td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.payroll.run', $run) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">View</a>
                        @if ($run->status === 'draft' && (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*')))
                            <form method="POST" action="{{ route('admin.payroll.destroy-run', $run) }}" onsubmit="return confirm('Cancel and permanently delete the payroll run for {{ $run->periodLabel() }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-black text-red-600 hover:text-red-800">Cancel</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No payroll runs yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($runs->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('run', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more runs as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more runs...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
