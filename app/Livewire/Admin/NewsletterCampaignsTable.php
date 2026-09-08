<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterCampaign;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class NewsletterCampaignsTable extends Component
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
        $campaigns = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $campaigns->count() < $totalCount;

        return view('livewire.admin.newsletter-campaigns-table', [
            'campaigns' => $campaigns,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        return NewsletterCampaign::query()->with('sender')->latest('id');
    }
}
