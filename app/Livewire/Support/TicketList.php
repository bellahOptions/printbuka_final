<?php

namespace App\Livewire\Support;

use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class TicketList extends Component
{
    public int $perPage = 10;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalResults = 0;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->ticketsQuery();

        $this->totalResults = (clone $query)->count();

        $tickets = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $tickets->count() < $this->totalResults;

        return view('livewire.support.ticket-list', [
            'tickets' => $tickets,
        ]);
    }

    private function ticketsQuery(): Builder
    {
        return Ticket::query()
            ->where('user_id', auth()->id())
            ->latest();
    }
}
