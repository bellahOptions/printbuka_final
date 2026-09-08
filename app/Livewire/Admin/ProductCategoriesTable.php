<?php

namespace App\Livewire\Admin;

use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ProductCategoriesTable extends Component
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
        $categories = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $categories->count() < $totalCount;

        return view('livewire.admin.product-categories-table', [
            'categories' => $categories,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        return ProductCategory::query()
            ->with('parent')
            ->withCount('products')
            ->latest();
    }
}
