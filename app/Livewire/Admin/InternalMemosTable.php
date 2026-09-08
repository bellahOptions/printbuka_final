<?php

namespace App\Livewire\Admin;

use App\Models\InternalMemo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InternalMemosTable extends Component
{
    public int $perPage = 20;

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
        $memos = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $memos->count() < $totalCount;

        return view('livewire.admin.internal-memos-table', [
            'memos' => $memos,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        return InternalMemo::query()->with('sentBy')->latest('id');
    }
}
