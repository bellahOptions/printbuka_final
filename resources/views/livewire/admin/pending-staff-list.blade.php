<div class="divide-y divide-slate-100">
    @forelse ($pendingStaff as $person)
        @if($canAssignRoles)
            <form action="{{ route('admin.staff.update', $person) }}" method="POST"
                  enctype="multipart/form-data"
                  wire:key="pending-staff-{{ $person->id }}"
                  class="p-6 grid gap-5 lg:grid-cols-[1fr_200px_200px_auto] lg:items-end">
                @csrf @method('PUT')
        @else
            <div wire:key="pending-staff-{{ $person->id }}" class="p-6 grid gap-5 lg:grid-cols-[1fr_200px_200px_auto] lg:items-end">
        @endif

            {{-- Staff info + photo upload --}}
            <div>
                <div class="flex items-start gap-4">
                    @if($person->profilePhotoUrl())
                        <img src="{{ $person->profilePhotoUrl() }}" alt="{{ $person->displayName() }}"
                             class="h-14 w-14 rounded-full border-2 border-slate-200 object-cover shrink-0">
                    @else
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full
                                    border-2 border-slate-200 bg-violet-100 text-sm font-bold text-violet-800">
                            {{ $person->profileInitials() }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-slate-900">{{ $person->displayName() }}</p>
                        <p class="text-sm text-slate-500">{{ $person->email }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $person->phone }}</p>
                        <span class="pb-badge pb-badge-warning mt-2 text-[10px]">
                            Requested: {{ $roles[$person->requested_role] ?? $person->requested_role ?? 'N/A' }}
                            {{ $person->other_role ? '· '.$person->other_role : '' }}
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="pb-label">Profile photo (optional)</p>
                    <livewire:uploads.secure-image-upload
                        :key="'pending-photo-'.$person->id"
                        input-name="photo_upload_path"
                        directory="staff-photos"
                        :max-size-kb="2048"
                        :multiple="false"
                    />
                </div>
            </div>

            {{-- Role select --}}
            <div class="pb-field">
                <label class="pb-label" for="role-{{ $person->id }}">Final role</label>
                <select id="role-{{ $person->id }}" name="role" @disabled(!$canAssignRoles)
                    class="pb-select @disabled(!$canAssignRoles) disabled:opacity-60">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $person->requested_role) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2">
                @if($canAssignRoles)
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Approve
                    </label>
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-primary text-sm">
                        Save & Activate
                    </button>
                @else
                    <span class="pb-badge pb-badge-secondary">Process & Technology Manager assigns role</span>
                @endif
            </div>

        @if($canAssignRoles)
            </form>
        @else
            </div>
        @endif
    @empty
        <div class="pb-empty m-6">
            <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="pb-empty-title">No pending registrations</p>
            <p class="pb-empty-body">All staff registrations have been reviewed.</p>
        </div>
    @endforelse

    <div class="border-t border-slate-100 px-6 py-4 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($pendingStaff->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('registration', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more registrations as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more registrations...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
