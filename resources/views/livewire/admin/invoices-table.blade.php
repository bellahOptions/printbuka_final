<section class="mt-8 space-y-4">
    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
        <div class="pb-alert pb-alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="pb-card p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="pb-field w-full max-w-xl">
                <label class="pb-label">Search invoices</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Invoice no, order no, customer..."
                    class="pb-input"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div class="pb-field">
                    <label class="pb-label">Batch action</label>
                    <select wire:model.live="batchAction" class="pb-input">
                        <option value="">Select action</option>
                        <option value="mark_draft">Mark as Draft</option>
                        <option value="mark_paid">Mark as Paid</option>
                        <option value="mark_unpaid">Mark as Unpaid</option>
                        <option value="mark_disputed">Mark as Disputed</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </div>
                <button
                    type="button"
                    wire:click="applyBatchAction"
                    class="pb-btn pb-btn-md pb-btn-ink"
                >
                    Apply
                </button>
            </div>
        </div>

        @error('batchAction')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[1080px]">
            <thead>
                <tr>
                    @php
                        $loadedIds = $invoices->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allLoadedSelected = $loadedIds !== [] && count(array_diff($loadedIds, $selected)) === 0;
                    @endphp
                    <th>
                        <input type="checkbox" wire:click="toggleSelectLoadedSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allLoadedSelected)>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('invoice_number')" class="inline-flex items-center gap-1">
                            Document
                            @if ($sortField === 'invoice_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>Type</th>
                    <th>Job</th>
                    <th>Client</th>
                    <th>
                        <button type="button" wire:click="sortBy('total_amount')" class="inline-flex items-center gap-1">
                            Total
                            @if ($sortField === 'total_amount')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('status')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr wire:key="invoice-row-{{ $invoice->id }}">
                        <td data-label="">
                            <input type="checkbox" value="{{ $invoice->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td data-label="Document" class="font-semibold">{{ $invoice->invoice_number }}</td>
                        <td data-label="Type">{{ $invoice->documentTypeLabel() }}</td>
                        <td data-label="Job">{{ $invoice->order?->job_order_number ?? 'No job' }}</td>
                        <td data-label="Client">{{ $invoice->order?->customer_name ?? 'Pending' }}</td>
                        <td data-label="Total">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                        <td data-label="Status">{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                        <td data-label="">
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-semibold text-pink-700">View</a>
                                <a href="{{ route('admin.invoices.download', $invoice) }}" class="font-semibold text-slate-700">Download</a>
                                @if ($invoice->status !== 'paid')
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="font-semibold text-pink-700">Edit</a>
                                @endif

                                @if ($invoice->status !== 'paid' && filled($invoice->order?->customer_email))
                                    <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-semibold text-cyan-700 transition hover:text-cyan-900">{{ $invoice->sent_at ? 'Resend' : 'Send' }}</button>
                                    </form>
                                @endif

                                @if ($invoice->status !== 'paid')
                                    <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-semibold text-emerald-700 transition hover:text-emerald-900">Mark Paid</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-semibold text-slate-500 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="pb-empty">
                                <p class="pb-empty-title">No invoices matched your search.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-semibold text-slate-400">
        Showing {{ number_format($invoices->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('invoice', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more invoices as you scroll...
            </span>
            <span class="text-xs font-semibold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more invoices...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</section>
