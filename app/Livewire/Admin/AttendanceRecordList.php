<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AttendanceRecordList extends Component
{
    public int $staffId;

    public int $perPage = 31;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(int $staffId): void
    {
        $this->staffId = $staffId;
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
        $query = $this->recordsQuery();
        $totalCount = (clone $query)->count();
        $records = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $records->count() < $totalCount;

        return view('livewire.admin.attendance-record-list', [
            'records' => $records,
            'totalCount' => $totalCount,
        ]);
    }

    private function recordsQuery(): Builder
    {
        return AttendanceRecord::query()
            ->where('user_id', $this->staffId)
            ->orderByDesc('work_date');
    }
}
