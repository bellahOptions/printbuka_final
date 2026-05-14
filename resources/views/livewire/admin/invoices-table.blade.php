<section class="mt-8 space-y-4">
    @if (session('status'))
        <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
    @endif
    @if (session('warning'))
        <p class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">{{ session('warning') }}</p>
    @endif

    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <label class="w-full max-w-xl">
                <span class="text-xs font-black uppercase tracking-wide text-slate-500">Search invoices</span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Invoice no, order no, customer..."
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"
                >
            </label>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <label>
                    <span class="text-xs font-black uppercase tracking-wide text-slate-500">Batch action</span>
                    <select wire:model.live="batchAction" class="mt-2 h-11 rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        <option value="">Select action</option>
                        <option value="mark_draft">Mark as Draft</option>
                        <option value="mark_paid">Mark as Paid</option>
                        <option value="mark_unpaid">Mark as Unpaid</option>
                        <option value="mark_disputed">Mark as Disputed</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </label>
                <button
                    type="button"
                    wire:click="applyBatchAction"
                    class="h-11 rounded-md bg-slate-900 px-4 text-sm font-black text-white transition hover:bg-pink-700"
                >
                    Apply
                </button>
            </div>
        </div>

        @error('batchAction')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[1080px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    @php
                        $pageIds = $invoices->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allPageSelected = $pageIds !== [] && count(array_diff($pageIds, $selected)) === 0;
                    @endphp
                    <th class="px-4 py-4">
                        <input type="checkbox" wire:click="toggleSelectPageSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allPageSelected)>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('invoice_number')" class="inline-flex items-center gap-1">
                            Document
                            @if ($sortField === 'invoice_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">Type</th>
                    <th class="px-5 py-4">Job</th>
                    <th class="px-5 py-4">Client</th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('total_amount')" class="inline-flex items-center gap-1">
                            Total
                            @if ($sortField === 'total_amount')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('status')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($invoices as $invoice)
                    <tr wire:key="invoice-row-{{ $invoice->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $invoice->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td class="px-5 py-4 font-black">{{ $invoice->invoice_number }}</td>
                        <td class="px-5 py-4">{{ $invoice->documentTypeLabel() }}</td>
                        <td class="px-5 py-4">{{ $invoice->order?->job_order_number ?? 'No job' }}</td>
                        <td class="px-5 py-4">{{ $invoice->order?->customer_name ?? 'Pending' }}</td>
                        <td class="px-5 py-4">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                        <td class="px-5 py-4">{{ str($invoice->status)->replace('_', ' ')->title() }}</td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="font-black text-pink-700">Edit</a>

                                @if ($invoice->status !== 'paid')
                                    <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-black text-cyan-700 transition hover:text-cyan-900">{{ $invoice->sent_at ? 'Resend' : 'Send' }}</button>
                                    </form>
                                @endif

                                @if ($invoice->status !== 'paid')
                                    <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="font-black text-emerald-700 transition hover:text-emerald-900">Mark Paid</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-black text-slate-500 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-500">No invoices matched your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</section>
