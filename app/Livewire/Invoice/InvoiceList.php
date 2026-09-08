<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class InvoiceList extends Component
{
    public int $perPage = 10;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalResults = 0;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->invoicesQuery();

        $this->totalResults = (clone $query)->count();

        $invoices = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $invoices->count() < $this->totalResults;

        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices,
        ]);
    }

    private function invoicesQuery(): Builder
    {
        $userId = auth()->id();

        return Invoice::query()
            ->whereHas('order', function (Builder $query) use ($userId): void {
                $query->where('user_id', $userId);
            })
            ->with('order.product')
            ->latest('issued_at');
    }
}
