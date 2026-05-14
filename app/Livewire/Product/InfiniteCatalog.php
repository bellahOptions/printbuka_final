<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class InfiniteCatalog extends Component
{
    public array $filters = [];

    public int $perPage = 16;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalResults = 0;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'search' => trim((string) ($filters['search'] ?? '')),
            'category' => trim((string) ($filters['category'] ?? '')),
            'sort' => trim((string) ($filters['sort'] ?? 'name_asc')),
            'min_price' => $this->numericOrNull($filters['min_price'] ?? null),
            'max_price' => $this->numericOrNull($filters['max_price'] ?? null),
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

        return view('livewire.product.infinite-catalog', [
            'products' => $products,
        ]);
    }

    private function catalogQuery()
    {
        $catalogQuery = Product::query()
            ->with('category')
            ->where('is_active', true);

        if ($this->filters['search'] !== '') {
            $search = $this->filters['search'];

            $catalogQuery->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        if ($this->filters['category'] !== '') {
            $selectedCategory = ProductCategory::query()
                ->where('is_active', true)
                ->where('slug', $this->filters['category'])
                ->first();

            if ($selectedCategory) {
                $categoryIds = collect([$selectedCategory->id]);

                if ($selectedCategory->parent_id === null) {
                    $categoryIds = $categoryIds->merge(
                        $selectedCategory->children()
                            ->where('is_active', true)
                            ->pluck('id')
                    );
                }

                $catalogQuery->whereIn('product_category_id', $categoryIds->unique()->values()->all());
            }
        }

        if ($this->filters['min_price'] !== null) {
            $catalogQuery->where('price_unavailable', false)
                ->where('price', '>=', $this->filters['min_price']);
        }

        if ($this->filters['max_price'] !== null) {
            $catalogQuery->where('price_unavailable', false)
                ->where('price', '<=', $this->filters['max_price']);
        }

        match ($this->filters['sort']) {
            'name_desc' => $catalogQuery->orderByDesc('name'),
            'price_low_high' => $catalogQuery->orderBy('price_unavailable')->orderBy('price'),
            'price_high_low' => $catalogQuery->orderBy('price_unavailable')->orderByDesc('price'),
            'latest' => $catalogQuery->latest(),
            'most_viewed' => $catalogQuery->orderByDesc('view_count')->orderBy('name'),
            default => $catalogQuery->orderBy('name'),
        };

        return $catalogQuery;
    }

    private function numericOrNull(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
