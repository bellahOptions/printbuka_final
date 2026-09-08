<div>
    <div class="table-scroll-container overflow-x-auto">
        <table class="pb-table pb-table--cards w-full md:min-w-[1200px]">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Employment</th>
                    <th>KYC</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $person)
                    <tr wire:key="staff-row-{{ $person->id }}">
                        <td data-label="Employee">
                            <div class="flex items-center gap-3">
                                @if($person->profilePhotoUrl())
                                    <img src="{{ $person->profilePhotoUrl() }}" alt="{{ $person->displayName() }}"
                                         class="h-10 w-10 rounded-full border border-slate-200 object-cover shrink-0">
                                @else
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                                                bg-violet-100 text-xs font-bold text-violet-800">
                                        {{ $person->profileInitials() }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $person->displayName() }}</p>
                                    <p class="text-xs text-slate-400">{{ $person->email }}</p>
                                </div>
                            </div>
                            @if($canAssignRoles)
                                <form id="staff-role-form-{{ $person->id }}" action="{{ route('admin.staff.update', $person) }}" method="POST" class="hidden">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="is_active" value="{{ $person->is_active ? 1 : 0 }}">
                                </form>
                            @endif
                        </td>
                        <td data-label="Role">
                            @if($canAssignRoles)
                                <select name="role" form="staff-role-form-{{ $person->id }}" class="pb-select text-xs h-9 py-0">
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}" @selected($person->role === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            @else
                                <span class="text-sm font-medium text-slate-700">{{ $roles[$person->role] ?? $person->role }}</span>
                            @endif
                        </td>
                        <td data-label="Department">
                            @if($canAssignRoles)
                                <input type="text" name="department" form="staff-role-form-{{ $person->id }}"
                                       list="dept-options" value="{{ $person->department }}"
                                       placeholder="e.g. Creative" maxlength="100"
                                       class="pb-input text-xs h-9 py-0 w-full">
                                <button type="submit" form="staff-role-form-{{ $person->id }}" class="pb-btn pb-btn-sm pb-btn-outline text-[10px] mt-1.5 w-full">
                                    Save role & dept
                                </button>
                            @else
                                <span class="text-sm text-slate-600">{{ $person->department ?? '—' }}</span>
                            @endif
                        </td>
                        <td data-label="Status">
                            <span class="pb-badge {{ $person->is_active ? 'pb-badge-success' : 'pb-badge-danger' }} text-[10px]">
                                {{ $person->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if ($person->access_restricted)
                                <span class="pb-badge pb-badge-danger text-[10px] mt-1 block">Access Restricted</span>
                            @endif
                        </td>
                        <td data-label="Employment">
                            <span class="pb-badge {{ match($person->employment_status ?? 'active') { 'active'=>'pb-badge-success','suspended'=>'pb-badge-warning','terminated'=>'pb-badge-danger', default=>'pb-badge-secondary' } }} text-[10px]">{{ $person->employmentStatusLabel() }}</span>
                            @if($person->employment_status_changed_at)
                                <p class="text-[10px] text-slate-400 mt-1">{{ $person->employment_status_changed_at->format('M j, Y') }}</p>
                            @endif
                        </td>
                        <td data-label="KYC">
                            @php($personKycStatus = $person->staffProfile?->kyc_status ?? 'pending')
                            <span class="pb-badge text-[10px] {{ match($personKycStatus) { 'approved' => 'pb-badge-success', 'correction_requested' => 'pb-badge-warning', default => 'pb-badge-danger' } }}">
                                {{ $person->staffProfile?->kycStatusLabel() ?? 'Pending Review' }}
                            </span>
                            @if($canManageKyc && $personKycStatus !== 'approved')
                                <a href="{{ route('admin.staff.profile.show', $person) }}" class="block text-[10px] font-semibold text-violet-700 hover:text-violet-900 mt-1">
                                    Review KYC →
                                </a>
                            @endif
                        </td>
                        <td data-label="Approved">
                            <span class="text-xs text-slate-500">{{ $person->approved_at?->format('M j, Y') ?? '—' }}</span>
                        </td>
                        <td data-label="Actions">
                            <div class="space-y-2 min-w-[200px]">
                                @if($canManageEmployment)
                                    <form action="{{ route('admin.staff.employment-status', $person) }}" method="POST"
                                          class="space-y-2">
                                        @csrf @method('PATCH')
                                        <select name="employment_status" class="pb-select text-xs h-9 py-0">
                                            <option value="active"     @selected(($person->employment_status ?? 'active') === 'active')>Onboard / Active</option>
                                            <option value="suspended"  @selected(($person->employment_status ?? '') === 'suspended')>Suspend</option>
                                            <option value="terminated" @selected(($person->employment_status ?? '') === 'terminated')>Terminate</option>
                                        </select>
                                        <input name="employment_status_reason" value="{{ $person->employment_status_reason }}"
                                               class="pb-input text-xs h-9 py-0" placeholder="Reason (optional)">
                                        <button type="submit" class="pb-btn pb-btn-sm pb-btn-ink text-xs w-full">Apply</button>
                                    </form>
                                @else
                                    <span class="pb-badge pb-badge-secondary text-[10px]">HR / Process & Technology Manager</span>
                                @endif

                                @if(auth()->user()?->role === 'super_admin' && $person->role !== 'super_admin')
                                    <form action="{{ route('admin.staff.access-restriction', $person) }}" method="POST"
                                          onsubmit="return confirm('{{ $person->access_restricted ? 'Restore access for '.$person->displayName().'?' : 'Restrict access for '.$person->displayName().'? They will be logged out immediately.' }}')">
                                        @csrf @method('PATCH')
                                        @if (!$person->access_restricted)
                                            <input name="reason" class="pb-input text-xs h-9 py-0 w-full" placeholder="Reason (optional)">
                                        @endif
                                        <button type="submit"
                                            class="pb-btn pb-btn-sm text-xs w-full mt-1 {{ $person->access_restricted ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                                            {{ $person->access_restricted ? '✓ Restore Access' : '⊘ Restrict Access' }}
                                        </button>
                                    </form>
                                    @if ($person->access_restricted)
                                        <p class="text-[10px] text-red-600 font-semibold">
                                            Restricted {{ $person->access_restricted_at?->diffForHumans() }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="pb-empty border-0 bg-transparent">
                                <p class="pb-empty-title">No approved staff yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($staff->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('staff member', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more staff as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more staff...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
