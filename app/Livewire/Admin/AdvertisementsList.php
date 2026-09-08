<?php

namespace App\Livewire\Admin;

use App\Models\Advertisement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AdvertisementsList extends Component
{
    public int $perPage = 15;

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
        $advertisements = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $advertisements->count() < $totalCount;

        return view('livewire.admin.advertisements-list', [
            'advertisements' => $advertisements,
            'totalCount' => $totalCount,
            'placements' => Advertisement::placements(),
        ]);
    }

    private function tableQuery(): Builder
    {
        return Advertisement::query()->latest();
    }
}
