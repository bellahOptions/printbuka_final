<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 20;

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
        $this->resetPage();
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

        $this->resetPage();
    }

    public function toggleSelectPageSelection(): void
    {
        $pageIds = $this->currentPageIds();

        if ($pageIds === []) {
            return;
        }

        $allSelected = count(array_diff($pageIds, $this->selected)) === 0;

        if ($allSelected) {
            $this->selected = array_values(array_diff($this->selected, $pageIds));

            return;
        }

        $this->selected = array_values(array_unique([...$this->selected, ...$pageIds]));
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
        $this->resetPage();
    }

    public function render(): View
    {
        $products = $this->tableQuery()->paginate($this->perPage);

        return view('livewire.admin.products-table', [
            'products' => $products,
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
    private function currentPageIds(): array
    {
        return $this->tableQuery()
            ->paginate($this->perPage, ['*'], $this->getPageName())
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
