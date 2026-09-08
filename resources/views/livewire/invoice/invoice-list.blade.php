<div>
    <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
        <div class="card-body p-6">
            <p class="text-sm font-bold text-slate-400 mb-4">
                {{ number_format($this->totalResults) }} {{ Str::plural('invoice', $this->totalResults) }} found
            </p>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-slate-600 font-semibold">Document</th>
                            <th class="text-slate-600 font-semibold">Invoice #</th>
                            <th class="text-slate-600 font-semibold">Order Details</th>
                            <th class="text-slate-600 font-semibold">Amount</th>
                            <th class="text-slate-600 font-semibold">Status</th>
                            <th class="text-slate-600 font-semibold">Due Date</th>
                            <th class="text-slate-600 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 rounded-lg bg-pink-100 flex items-center justify-center">
                                            @if($invoice->isQuotation())
                                                <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $invoice->documentTypeLabel() }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-sm font-semibold text-slate-800">
                                        {{ $invoice->invoice_number ?? 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $invoice->order->product->name ?? 'Custom Order' }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            Order #{{ $invoice->order->displayNumber() ?? $invoice->order_id }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <p class="font-bold text-slate-900">₦{{ number_format($invoice->total_amount, 2) }}</p>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'overdue' => 'error',
                                            'cancelled' => 'error',
                                            'draft' => 'info',
                                        ];
                                        $statusColor = $statusColors[$invoice->status] ?? 'info';
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }} badge-sm">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->due_at)
                                        <p class="text-sm {{ $invoice->due_at < now() && $invoice->status === 'pending' ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                                            {{ $invoice->due_at->format('M d, Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-slate-400">—</p>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('user.invoices.show', $invoice) }}"
                                           class="btn btn-xs btn-ghost text-pink-600 hover:bg-pink-50"
                                           title="View Details">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('user.invoices.download', $invoice) }}"
                                           class="btn btn-xs btn-ghost text-slate-500 hover:text-pink-600"
                                           title="Download PDF">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        @if($invoice->status === 'pending')
                                            <span class="btn btn-xs bg-amber-100 text-amber-800 border-0" title="Bank Transfer">
                                                Bank Transfer
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">No invoices found</p>
                                        <p class="text-sm text-slate-400 mt-1">Your invoices and quotations will appear here</p>
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-pink-600 text-white mt-4">
                                            Start Shopping
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->isNotEmpty())
                @if($this->hasMore)
                    <div class="mt-6 flex flex-col items-center gap-3" wire:poll.visible="loadMore">
                        <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                            Loading more invoices as you scroll...
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                            Loading more invoices...
                        </span>
                        <button type="button" wire:click="loadMore" class="btn btn-outline border-slate-300 hover:border-pink-400 hover:text-pink-700 font-black">
                            Load More
                        </button>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-center">
                        <p class="text-sm font-black text-slate-700">You have reached the end of your invoices.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
