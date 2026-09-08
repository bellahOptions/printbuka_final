<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CustomersTable extends Component
{
    public array $filters = [];

    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalCount = 0;

    public function mount(array $filters = []): void
    {
        $this->filters = [
            'search' => trim((string) ($filters['search'] ?? '')),
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
        $query = $this->customersQuery();

        $this->totalCount = (clone $query)->count();

        $customers = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $customers->count() < $this->totalCount;

        return view('livewire.admin.customers-table', [
            'customers' => $customers,
        ]);
    }

    private function customersQuery(): Builder
    {
        $search = $this->filters['search'];

        return User::query()
            ->where('role', 'customer')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('companyName', 'like', '%'.$search.'%');
                });
            })
            ->withCount('orders')
            ->withCount([
                'orders as invoices_count' => fn (Builder $query) => $query->whereHas('invoice'),
            ])
            ->withSum('orders as total_paid', 'amount_paid')
            ->latest();
    }
}
