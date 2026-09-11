@php
    $badgeClass = function (string $status): string {
        return match ($status) {
            \App\Models\Training::STATUS_ACCEPTED => 'pb-badge-success',
            \App\Models\Training::STATUS_REJECTED => 'pb-badge-danger',
            default => 'pb-badge-warning',
        };
    };
@endphp

<div class="space-y-4">
    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[980px]">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Skill</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr wire:key="training-row-{{ $application->id }}">
                        <td>
                            <p class="font-black text-slate-950">{{ $application->fullName() }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $application->email }} · {{ $application->phone_whatsapp }}</p>
                        </td>
                        <td class="font-bold text-slate-700">{{ $application->desired_skill }}</td>
                        <td class="text-slate-600">{{ $application->city_state }}</td>
                        <td>
                            <span class="pb-badge {{ $badgeClass($application->status) }}">{{ $application->statusLabel() }}</span>
                        </td>
                        <td class="text-slate-600">{{ $application->created_at?->format('M j, Y g:i A') }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.training.show', $application) }}" class="font-black text-pink-700 hover:text-pink-800">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-sm font-semibold text-slate-500">No training applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-bold text-slate-400">
        Showing {{ number_format($applications->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('application', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more applications as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more applications...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
