<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTable extends Component
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

    public string $targetStatus = '';

    public string $targetPaymentStatus = '';

    /**
     * @var array<int, string>
     */
    private array $allowedSortFields = [
        'job_order_number',
        'customer_name',
        'channel',
        'priority',
        'payment_status',
        'status',
        'created_at',
    ];

    public function updatedSelected(): void
    {
        $this->selected = $this->normalizedSelectedIds();
    }

    public function updatedBatchAction(): void
    {
        $this->resetErrorBag('batchAction');

        if ($this->batchAction !== 'status') {
            $this->targetStatus = '';
        }

        if ($this->batchAction !== 'payment_status') {
            $this->targetPaymentStatus = '';
        }
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

        if (! $user || ! $user->canAdmin('orders.view')) {
            abort(403);
        }

        $selectedIds = $this->normalizedSelectedIds();

        if ($selectedIds === []) {
            $this->addError('selected', 'Select at least one order to run a batch action.');

            return;
        }

        $orders = Order::query()->whereKey($selectedIds)->get();
        $affected = 0;
        $blockedByPaymentRule = 0;

        if (! in_array($this->batchAction, ['priority_urgent', 'priority_normal', 'status', 'payment_status'], true)) {
            $this->addError('batchAction', 'Choose a valid batch action.');

            return;
        }

        if ($this->batchAction === 'priority_urgent') {
            $affected = (int) Order::query()->whereKey($selectedIds)->update(['priority' => '🔴 Urgent']);
        }

        if ($this->batchAction === 'priority_normal') {
            $affected = (int) Order::query()->whereKey($selectedIds)->update(['priority' => '🟡 Normal']);
        }

        if ($this->batchAction === 'status') {
            if (! $user->canAdmin('workflow.approve')) {
                abort(403);
            }

            $allowedStatuses = (array) config('printbuka_admin.job_statuses', []);

            if (! in_array($this->targetStatus, $allowedStatuses, true)) {
                $this->addError('targetStatus', 'Choose a valid status for this batch update.');

                return;
            }

            $phaseOneStatus = (string) (config('printbuka_admin.workflow_phases.0.status') ?? 'Analyzing Job Brief');
            $settledPaymentStatuses = ['Invoice Settled (70%)', 'Invoice Settled (100%)'];

            foreach ($orders as $order) {
                if (
                    (string) $order->status === $phaseOneStatus
                    && $this->targetStatus !== $phaseOneStatus
                    && ! in_array((string) $order->payment_status, $settledPaymentStatuses, true)
                ) {
                    $blockedByPaymentRule++;

                    continue;
                }

                $order->forceFill([
                    'status' => $this->targetStatus,
                    'phase_approval_status' => 'Approved',
                    'requested_next_status' => null,
                    'phase_approval_comment' => 'Batch-updated from admin order list.',
                    'phase_approved_by_id' => $user->id,
                    'phase_approved_at' => now(),
                ])->save();

                $affected++;
            }
        }

        if ($this->batchAction === 'payment_status') {
            if (! $user->canAdmin('invoices.manage')) {
                abort(403);
            }

            $allowedPaymentStatuses = (array) config('printbuka_admin.payment_statuses', []);

            if (! in_array($this->targetPaymentStatus, $allowedPaymentStatuses, true)) {
                $this->addError('targetPaymentStatus', 'Choose a valid payment status for this batch update.');

                return;
            }

            $affected = (int) Order::query()->whereKey($selectedIds)->update([
                'payment_status' => $this->targetPaymentStatus,
            ]);
        }

        $this->selected = [];
        $this->batchAction = '';
        $this->targetStatus = '';
        $this->targetPaymentStatus = '';

        if ($blockedByPaymentRule > 0) {
            session()->flash('warning', $blockedByPaymentRule.' '.str('order')->plural($blockedByPaymentRule).' skipped. Phase 1 jobs require at least 70% payment before status can move forward.');
        }

        session()->flash('status', $affected.' '.str('order')->plural($affected).' updated.');
        $this->resetPage();
    }

    public function render(): View
    {
        $orders = $this->tableQuery()->paginate($this->perPage);
        $user = auth()->user();

        return view('livewire.admin.orders-table', [
            'orders' => $orders,
            'canApproveWorkflow' => (bool) ($user?->canAdmin('workflow.approve') ?? false),
            'canManageInvoices' => (bool) ($user?->canAdmin('invoices.manage') ?? false),
            'statusOptions' => (array) config('printbuka_admin.job_statuses', []),
            'paymentStatusOptions' => (array) config('printbuka_admin.payment_statuses', []),
        ]);
    }

    private function tableQuery(): Builder
    {
        $query = Order::query()->with('invoice');

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('job_order_number', 'like', '%'.$search.'%')
                    ->orWhere('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_email', 'like', '%'.$search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('payment_status', 'like', '%'.$search.'%')
                    ->orWhereHas('invoice', fn (Builder $invoiceQuery) => $invoiceQuery->where('invoice_number', 'like', '%'.$search.'%'));
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
