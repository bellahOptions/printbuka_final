<?php

namespace App\Livewire\Admin;

use App\Models\Training;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class TrainingApplicationsTable extends Component
{
    public array $filters = [];

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'status' => trim((string) ($filters['status'] ?? '')),
            'skill' => trim((string) ($filters['skill'] ?? '')),
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
        $query = $this->tableQuery();
        $totalCount = (clone $query)->count();
        $applications = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $applications->count() < $totalCount;

        return view('livewire.admin.training-applications-table', [
            'applications' => $applications,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $status = $this->filters['status'];
        $skill = $this->filters['skill'];
        $search = $this->filters['search'];

        $query = Training::query()
            ->with('decidedBy:id,first_name,last_name,email')
            ->when($status !== '', fn ($builder) => $builder->where('status', $status))
            ->when($skill !== '', fn ($builder) => $builder->where('desired_skill', $skill))
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $inner) use ($search): void {
                    $inner
                        ->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone_whatsapp', 'like', '%'.$search.'%');
                });
            });

        $query->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'accepted' THEN 1 WHEN 'rejected' THEN 2 ELSE 3 END")
            ->latest();

        return $query;
    }
}
