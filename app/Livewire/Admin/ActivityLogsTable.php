<?php

namespace App\Livewire\Admin;

use App\Models\AdminActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ActivityLogsTable extends Component
{
    public array $filters = [];

    public int $perPage = 40;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'search' => trim((string) ($filters['search'] ?? '')),
            'role' => trim((string) ($filters['role'] ?? '')),
            'route_name' => trim((string) ($filters['route_name'] ?? '')),
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
        $logs = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $logs->count() < $totalCount;

        return view('livewire.admin.activity-logs-table', [
            'logs' => $logs,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $search = $this->filters['search'];
        $role = $this->filters['role'];
        $route = $this->filters['route_name'];

        $query = AdminActivityLog::query()
            ->with('user')
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('action', 'like', '%'.$search.'%')
                        ->orWhere('method', 'like', '%'.$search.'%')
                        ->orWhere('route_name', 'like', '%'.$search.'%')
                        ->orWhere('url', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                            $userQuery
                                ->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($role !== '', fn ($builder) => $builder->where('role', $role))
            ->when($route !== '', fn ($builder) => $builder->where('route_name', $route));

        $query->latest();

        return $query;
    }
}
