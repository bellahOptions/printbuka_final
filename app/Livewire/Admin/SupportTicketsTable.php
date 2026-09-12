<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SupportTicketsTable extends Component
{
    public int $perPage = 12;

    public int $page = 1;

    public bool $hasMore = true;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->tableQuery();
        $totalCount = (clone $query)->count();
        $tickets = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $tickets->count() < $totalCount;

        return view('livewire.admin.support-tickets-table', [
            'tickets' => $tickets,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $user = auth()->user();

        $query = Ticket::query()
            ->with(['user:id,first_name,last_name,email,role', 'assignedStaff:id,first_name,last_name,email,role'])
            ->when(
                ! $this->isResolver($user),
                fn ($builder) => $builder->where('user_id', $user?->id)
            );

        $query->latest();

        return $query;
    }

    private function isResolver(?User $user): bool
    {
        return in_array((string) ($user?->role ?? ''), ['super_admin', 'managing_director'], true);
    }
}
