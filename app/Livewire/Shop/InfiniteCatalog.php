<?php

namespace App\Livewire\Shop;

use App\Models\ShopProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InfiniteCatalog extends Component
{
    public array $filters = [];

    public int $perPage = 12;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalResults = 0;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'search' => trim((string) ($filters['search'] ?? '')),
            'featured' => (bool) ($filters['featured'] ?? false),
            'on_sale' => (bool) ($filters['on_sale'] ?? false),
            'sort' => trim((string) ($filters['sort'] ?? 'featured')),
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
        $query = $this->catalogQuery();

        $this->totalResults = (clone $query)->count();

        $products = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $products->count() < $this->totalResults;

        return view('livewire.shop.infinite-catalog', [
            'products' => $products,
        ]);
    }

    private function catalogQuery(): Builder
    {
        $sort = $this->filters['sort'];

        return ShopProduct::query()
            ->active()
            ->when($this->filters['search'] !== '', fn ($q) => $q->where(function (Builder $builder) {
                $builder
                    ->where('name', 'like', '%'.$this->filters['search'].'%')
                    ->orWhere('short_description', 'like', '%'.$this->filters['search'].'%');
            }))
            ->when($this->filters['featured'], fn ($q) => $q->featured())
            ->when($this->filters['on_sale'], fn ($q) => $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->when($sort === 'price_asc', fn ($q) => $q->orderByRaw('COALESCE(sale_price, price) ASC'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByRaw('COALESCE(sale_price, price) DESC'))
            ->when($sort === 'newest', fn ($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('view_count'))
            ->when(! in_array($sort, ['price_asc', 'price_desc', 'newest', 'popular'], true), fn ($q) => $q->orderByDesc('is_featured')->orderBy('name'));
    }
}
