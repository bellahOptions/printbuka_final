<?php

namespace App\Livewire\Admin;

use App\Models\FinanceEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class FinanceEntriesTable extends Component
{
    public array $filters = [];

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalCount = 0;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'type' => trim((string) ($filters['type'] ?? '')),
            'entry_type' => trim((string) ($filters['entry_type'] ?? '')),
            'category' => trim((string) ($filters['category'] ?? '')),
            'date_from' => trim((string) ($filters['date_from'] ?? '')),
            'date_to' => trim((string) ($filters['date_to'] ?? '')),
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
        $query = $this->entriesQuery();

        $this->totalCount = (clone $query)->count();

        $entries = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $entries->count() < $this->totalCount;

        return view('livewire.admin.finance-entries-table', [
            'entries' => $entries,
        ]);
    }

    private function entriesQuery(): Builder
    {
        $query = FinanceEntry::query()->with('order', 'recorder');

        if ($this->filters['type'] !== '') {
            $query->where('type', $this->filters['type']);
        }
        if ($this->filters['entry_type'] !== '') {
            $query->where('entry_type', $this->filters['entry_type']);
        }
        if ($this->filters['category'] !== '') {
            $query->where('category', 'like', '%'.$this->filters['category'].'%');
        }
        if ($this->filters['date_from'] !== '') {
            $query->whereDate('entry_date', '>=', $this->filters['date_from']);
        }
        if ($this->filters['date_to'] !== '') {
            $query->whereDate('entry_date', '<=', $this->filters['date_to']);
        }

        $query->orderByDesc('entry_date')->orderByDesc('created_at');

        return $query;
    }
}
