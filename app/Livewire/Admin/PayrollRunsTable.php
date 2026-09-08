<?php

namespace App\Livewire\Admin;

use App\Models\PayrollRun;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PayrollRunsTable extends Component
{
    public int $perPage = 12;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalCount = 0;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = PayrollRun::query()->with('createdBy')->orderByDesc('payroll_year')->orderByDesc('payroll_month');

        $this->totalCount = (clone $query)->count();

        $runs = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $runs->count() < $this->totalCount;

        return view('livewire.admin.payroll-runs-table', [
            'runs' => $runs,
        ]);
    }
}
