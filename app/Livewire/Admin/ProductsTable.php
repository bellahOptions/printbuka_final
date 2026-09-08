<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProductsTable extends Component
{
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    /**
     * @var array<int, int>
     */
    public array $selected = [];

    public string $batchAction = '';

    /**
     * @var array<int, string>
     */
    private array $allowedSortFields = [
        'name',
        'moq',
        'price',
        'is_active',
        'created_at',
    ];

    public function updatedSelected(): void
    {
        $this->selected = $this->normalizedSelectedIds();
    }

    public function updatedBatchAction(): void
    {
        $this->resetErrorBag('batchAction');
    }

    public function updatingSearch(): void
    {
        $this->page = 1;
        $this->selected = [];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->allowedSortFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->page = 1;
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function toggleSelectLoadedSelection(): void
    {
        $loadedIds = $this->loadedIds();

        if ($loadedIds === []) {
            return;
        }

        $allSelected = count(array_diff($loadedIds, $this->selected)) === 0;

        if ($allSelected) {
            $this->selected = array_values(array_diff($this->selected, $loadedIds));

            return;
        }

        $this->selected = array_values(array_unique([...$this->selected, ...$loadedIds]));
    }

    public function applyBatchAction(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->canAdmin('products.manage')) {
            abort(403);
        }

        $selectedIds = $this->normalizedSelectedIds();

        if ($selectedIds === []) {
            $this->addError('selected', 'Select at least one product to run a batch action.');

            return;
        }

        if (! in_array($this->batchAction, ['activate', 'hide', 'delete'], true)) {
            $this->addError('batchAction', 'Choose a valid batch action.');

            return;
        }

        $productsQuery = Product::query()->whereKey($selectedIds);
        $affected = 0;

        if ($this->batchAction === 'activate') {
            $affected = (int) $productsQuery->update(['is_active' => true]);
        }

        if ($this->batchAction === 'hide') {
            $affected = (int) $productsQuery->update(['is_active' => false]);
        }

        if ($this->batchAction === 'delete') {
            $products = $productsQuery->get();
            $affected = $products->count();

            foreach ($products as $product) {
                if (filled($product->featured_image)) {
                    Storage::disk('public')->delete((string) $product->featured_image);
                }

                foreach ((array) $product->additional_images as $path) {
                    if (is_string($path) && filled($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                $product->delete();
            }
        }

        $this->selected = [];
        $this->batchAction = '';

        session()->flash('status', $affected.' '.str('product')->plural($affected).' updated.');
        $this->page = 1;
    }

    public function render(): View
    {
        $query = $this->tableQuery();
        $totalCount = (clone $query)->count();
        $products = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $products->count() < $totalCount;

        return view('livewire.admin.products-table', [
            'products' => $products,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $query = Product::query()->with('category');

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%')
                    ->orWhere('service_type', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * @return array<int, int>
     */
    private function loadedIds(): array
    {
        return $this->tableQuery()
            ->limit($this->page * $this->perPage)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function normalizedSelectedIds(): array
    {
        return collect($this->selected)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
