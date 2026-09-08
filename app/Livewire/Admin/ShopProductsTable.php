<?php

namespace App\Livewire\Admin;

use App\Models\ShopProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShopProductsTable extends Component
{
    public string $search = '';

    public string $status = '';

    public string $stock = '';

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->search = trim((string) ($filters['search'] ?? ''));
        $this->status = trim((string) ($filters['status'] ?? ''));
        $this->stock = trim((string) ($filters['stock'] ?? ''));
    }

    public function updatingSearch(): void
    {
        $this->page = 1;
    }

    public function updatingStatus(): void
    {
        $this->page = 1;
    }

    public function updatingStock(): void
    {
        $this->page = 1;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->stock = '';
        $this->page = 1;
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
        $products = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $products->count() < $totalCount;

        return view('livewire.admin.shop-products-table', [
            'products' => $products,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $query = ShopProduct::query()->withCount('optionGroups');

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%');
            });
        }

        if ($this->status === 'active') {
            $query->where('is_active', true);
        }

        if ($this->status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->status === 'featured') {
            $query->where('is_featured', true);
        }

        if ($this->stock === 'out') {
            $query->where('manage_stock', true)->where('stock_quantity', 0);
        }

        $query->latest();

        return $query;
    }
}
