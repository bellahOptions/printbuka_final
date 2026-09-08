<div>
    <div class="table-scroll-container overflow-x-auto">
        <table class="pb-table pb-table--cards w-full md:min-w-[1100px]">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Company</th>
                    <th>
                        <abbr title="Total orders placed" class="no-underline">Orders</abbr>
                    </th>
                    <th>Invoices</th>
                    <th>Lifetime Value</th>
                    <th>Lifecycle</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            @php
                $lifecycleFn = static function(int $orders, float $ltv): array {
                    return match(true) {
                        $orders === 0 && $ltv == 0                        => ['label' => 'Lead',       'badge' => 'pb-badge-secondary'],
                        $orders >= 1  && $ltv == 0                        => ['label' => 'Prospect',   'badge' => 'pb-badge-info'],
                        $orders >= 1  && $ltv > 0 && $orders < 3         => ['label' => 'New Client', 'badge' => 'pb-badge-primary'],
                        $orders >= 3  && $ltv < 100000                    => ['label' => 'Regular',    'badge' => 'pb-badge-success'],
                        $orders >= 3  && $ltv >= 100000                   => ['label' => 'VIP Client', 'badge' => 'pb-badge-warning'],
                        default                                            => ['label' => 'Client',     'badge' => 'pb-badge-secondary'],
                    };
                };
            @endphp
            <tbody>
                @forelse($customers as $customer)
                    @php($lifecycle = $lifecycleFn((int)($customer->orders_count ?? 0), (float)($customer->total_paid ?? 0)))
                    <tr wire:key="customer-row-{{ $customer->id }}">
                        <td data-label="Customer">
                            <div class="flex items-center gap-3">
                                <div class="pb-avatar pb-avatar-md shrink-0">
                                    <div class="pb-avatar-fallback bg-blue-100 text-blue-800 font-semibold text-xs">
                                        {{ strtoupper(substr($customer->displayName(), 0, 2)) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $customer->displayName() }}</p>
                                    <p class="text-xs text-slate-400">{{ $customer->email }}</p>
                                    <p class="text-xs text-slate-400">{{ $customer->phone ?: '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Company">
                            <span class="text-sm text-slate-600">{{ $customer->companyName ?: '—' }}</span>
                        </td>
                        <td data-label="Orders">
                            <span class="font-semibold text-slate-900">{{ number_format((int)($customer->orders_count ?? 0)) }}</span>
                        </td>
                        <td data-label="Invoices">
                            <span class="font-semibold text-slate-700">{{ number_format((int)($customer->invoices_count ?? 0)) }}</span>
                        </td>
                        <td data-label="Lifetime Value">
                            <span class="font-semibold text-slate-900">
                                ₦{{ number_format((float)($customer->total_paid ?? 0), 0) }}
                            </span>
                        </td>
                        <td data-label="Lifecycle">
                            <span class="pb-badge {{ $lifecycle['badge'] }} text-[10px]">
                                {{ $lifecycle['label'] }}
                            </span>
                        </td>
                        <td data-label="Joined">
                            <span class="text-xs text-slate-500">{{ $customer->created_at->format('M j, Y') }}</span>
                        </td>
                        <td data-label="Status">
                            <span class="pb-badge {{ $customer->is_active ? 'pb-badge-success' : 'pb-badge-warning' }} text-[10px]">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end items-center gap-2">
                                {{-- Toggle status --}}
                                <form action="{{ route('admin.customers.update-status', $customer) }}"
                                      method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $customer->is_active ? 0 : 1 }}">
                                    <button type="submit"
                                        class="pb-btn pb-btn-sm {{ $customer->is_active ? 'pb-btn-outline text-amber-700 border-amber-200 hover:bg-amber-50 hover:border-amber-400' : 'pb-btn-success' }} text-xs">
                                        {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Message popover --}}
                                <div class="relative pb-popover-wrapper">
                                    <button type="button"
                                        data-popover-btn
                                        class="pb-btn pb-btn-sm pb-btn-outline text-xs">
                                        Message
                                    </button>
                                    <div data-popover-panel
                                         class="absolute right-0 z-30 mt-2 w-80 pb-card shadow-xl p-4 hidden">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-3">
                                            Send to {{ $customer->displayName() }}
                                        </h4>
                                        <form action="{{ route('admin.customers.send-message', $customer) }}"
                                              method="POST" class="space-y-3">
                                            @csrf
                                            <div class="pb-field">
                                                <label class="pb-label">Subject</label>
                                                <input type="text" name="subject" required class="pb-input text-sm">
                                            </div>
                                            <div class="pb-field">
                                                <label class="pb-label">Message</label>
                                                <textarea name="message" rows="3" required
                                                          class="pb-textarea text-sm"></textarea>
                                            </div>
                                            <button type="submit" class="pb-btn pb-btn-md pb-btn-primary text-sm w-full">
                                                Send Email
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if(auth()->user()?->role === 'super_admin')
                                    <form action="{{ route('admin.customers.destroy', $customer) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Permanently delete this customer? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="pb-btn pb-btn-sm pb-btn-ghost text-red-600 hover:bg-red-50 text-xs">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div class="pb-empty border-0 bg-transparent">
                                <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6"/>
                                </svg>
                                <p class="pb-empty-title">No customers found</p>
                                <p class="pb-empty-body">Try adjusting your search filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4 space-y-3">
        <p class="text-xs text-slate-400">
            Showing {{ number_format($customers->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('customer', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more customers as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more customers...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline text-sm">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
