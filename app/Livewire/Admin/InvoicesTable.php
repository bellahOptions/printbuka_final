<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Services\InvoiceLifecycleService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InvoicesTable extends Component
{
    protected $listeners = [
        'invoices-imported' => 'refreshAfterImport',
    ];

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
        'invoice_number',
        'total_amount',
        'status',
        'issued_at',
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

        if (! $user || ! $user->canAdmin('invoices.manage')) {
            abort(403);
        }

        $selectedIds = $this->normalizedSelectedIds();

        if ($selectedIds === []) {
            $this->addError('selected', 'Select at least one invoice to run a batch action.');

            return;
        }

        $statusActions = [
            'mark_draft' => 'draft',
            'mark_paid' => 'paid',
            'mark_unpaid' => 'unpaid',
            'mark_disputed' => 'disputed',
        ];

        if (! in_array($this->batchAction, ['mark_draft', 'mark_paid', 'mark_unpaid', 'mark_disputed', 'delete'], true)) {
            $this->addError('batchAction', 'Choose a valid batch action.');

            return;
        }

        $affected = 0;
        $invoices = Invoice::query()
            ->with('order.product')
            ->whereKey($selectedIds)
            ->get();

        if ($this->batchAction === 'delete') {
            foreach ($invoices as $invoice) {
                $invoice->delete();
                $affected++;
            }
        } else {
            $targetStatus = (string) ($statusActions[$this->batchAction] ?? '');
            $lifecycleService = app(InvoiceLifecycleService::class);

            foreach ($invoices as $invoice) {
                $previousStatus = (string) $invoice->status;

                if ($previousStatus === $targetStatus) {
                    continue;
                }

                $invoice->forceFill(['status' => $targetStatus])->save();
                $lifecycleService->handleStatusChange($invoice->fresh(['order.product']), $previousStatus);
                $affected++;
            }
        }

        $this->selected = [];
        $this->batchAction = '';

        session()->flash('status', $affected.' '.str('invoice')->plural($affected).' updated.');
        $this->page = 1;
    }

    public function refreshAfterImport(): void
    {
        $this->selected = [];
        $this->batchAction = '';
        $this->page = 1;
    }

    public function render(): View
    {
        $query = $this->tableQuery();
        $totalCount = (clone $query)->count();
        $invoices = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $invoices->count() < $totalCount;

        return view('livewire.admin.invoices-table', [
            'invoices' => $invoices,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $query = Invoice::query()->with('order');

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('invoice_number', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('order', function (Builder $orderQuery) use ($search): void {
                        $orderQuery
                            ->where('job_order_number', 'like', '%'.$search.'%')
                            ->orWhere('customer_name', 'like', '%'.$search.'%')
                            ->orWhere('customer_email', 'like', '%'.$search.'%');
                    });
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
