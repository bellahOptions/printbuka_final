<div class="pb-card overflow-hidden">
    <div class="pb-table-wrapper border-0 rounded-none">
        <table class="pb-table pb-table--cards">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Entries</th>
                    <th>Total Net Payroll</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Created By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($runs as $run)
                <tr wire:key="payroll-run-row-{{ $run->id }}">
                    <td data-label="Period" class="font-semibold text-slate-900">{{ $run->periodLabel() }}</td>
                    <td data-label="Entries" class="text-sm text-slate-600">{{ $run->entries()->count() }} staff</td>
                    <td data-label="Total Net Payroll" class="font-semibold text-slate-900">₦{{ number_format($run->totalNetPayroll(), 2) }}</td>
                    <td data-label="Status">
                        <span class="pb-badge {{ $run->statusBadgeClass() }}">{{ ucfirst($run->status) }}</span>
                    </td>
                    <td data-label="Payment Date" class="text-sm text-slate-600">{{ $run->payment_date?->format('M j, Y') ?? '—' }}</td>
                    <td data-label="Created By" class="text-sm text-slate-600">{{ $run->createdBy?->displayName() }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.payroll.run', $run) }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600">View</a>
                            @if ($run->status === 'draft' && (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*')))
                                <form method="POST" action="{{ route('admin.payroll.destroy-run', $run) }}" onsubmit="return confirm('Cancel and permanently delete the payroll run for {{ $run->periodLabel() }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="pb-empty"><p class="pb-empty-title">No payroll runs yet.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-semibold text-slate-400">
            Showing {{ number_format($runs->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('run', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more runs as you scroll...
                </span>
                <span class="text-xs font-semibold uppercase tracking-wide text-brand-600" wire:loading wire:target="loadMore">
                    Loading more runs...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-sm pb-btn-outline">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
