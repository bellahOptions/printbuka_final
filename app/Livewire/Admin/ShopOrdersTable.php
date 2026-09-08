<?php

namespace App\Livewire\Admin;

use App\Models\ShopOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShopOrdersTable extends Component
{
    public string $search = '';

    public string $status = '';

    public string $payment = '';

    public int $perPage = 25;

    public int $page = 1;

    public bool $hasMore = true;

    public function mount(array $filters = []): void
    {
        $this->search = trim((string) ($filters['search'] ?? ''));
        $this->status = trim((string) ($filters['status'] ?? ''));
        $this->payment = trim((string) ($filters['payment'] ?? ''));
    }

    public function updatingSearch(): void
    {
        $this->page = 1;
    }

    public function updatingStatus(): void
    {
        $this->page = 1;
    }

    public function updatingPayment(): void
    {
        $this->page = 1;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->payment = '';
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
        $orders = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $orders->count() < $totalCount;

        return view('livewire.admin.shop-orders-table', [
            'orders' => $orders,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        $query = ShopOrder::query()->withCount('items');

        if ($this->status !== '') {
            $query->where('fulfillment_status', $this->status);
        }

        if ($this->payment !== '') {
            $query->where('payment_status', $this->payment);
        }

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('reference', 'like', '%'.$search.'%')
                    ->orWhere('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_email', 'like', '%'.$search.'%');
            });
        }

        $query->latest();

        return $query;
    }
}
