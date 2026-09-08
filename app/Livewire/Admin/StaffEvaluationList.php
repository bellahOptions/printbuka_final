<?php

namespace App\Livewire\Admin;

use App\Models\StaffEvaluation;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StaffEvaluationList extends Component
{
    public array $filters = [];

    public int $perPage = 24;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'staff_id' => $filters['staff_id'] ?? null,
            'month' => $filters['month'] ?? null,
            'year' => $filters['year'] ?? null,
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
        $query = $this->evaluationsQuery();
        $totalCount = (clone $query)->count();
        $evaluations = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $evaluations->count() < $totalCount;

        return view('livewire.admin.staff-evaluation-list', [
            'evaluations' => $evaluations,
            'totalCount' => $totalCount,
        ]);
    }

    private function evaluationsQuery(): Builder
    {
        $query = StaffEvaluation::query()->with(['staff', 'evaluatedBy']);

        if (filled($this->filters['staff_id'] ?? null)) {
            $query->where('staff_id', $this->filters['staff_id']);
        }

        if (filled($this->filters['month'] ?? null)) {
            $query->where('period_month', (int) $this->filters['month']);
        }

        if (filled($this->filters['year'] ?? null)) {
            $query->where('period_year', (int) $this->filters['year']);
        }

        $query->orderByDesc('period_year')->orderByDesc('period_month');

        return $query;
    }
}
