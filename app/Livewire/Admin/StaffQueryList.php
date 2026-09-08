<?php

namespace App\Livewire\Admin;

use App\Models\StaffQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StaffQueryList extends Component
{
    public array $filters = [];

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'status' => trim((string) ($filters['status'] ?? '')),
            'type' => trim((string) ($filters['type'] ?? '')),
            'search' => trim((string) ($filters['search'] ?? '')),
        ];
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->queriesQuery();
        $totalCount = (clone $query)->count();
        $queries = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $queries->count() < $totalCount;

        return view('livewire.admin.staff-query-list', [
            'queries' => $queries,
            'totalCount' => $totalCount,
        ]);
    }

    private function queriesQuery(): Builder
    {
        $query = StaffQuery::query()->with(['staff', 'issuedBy']);

        if ($this->filters['status'] !== '') {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['type'] !== '') {
            $query->where('query_type', $this->filters['type']);
        }

        if ($this->filters['search'] !== '') {
            $search = $this->filters['search'];

            $query->where(function (Builder $sq) use ($search): void {
                $sq->where('subject', 'like', "%{$search}%")
                    ->orWhere('query_number', 'like', "%{$search}%")
                    ->orWhereHas('staff', fn ($u) => $u->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        $query->latest();

        return $query;
    }
}
