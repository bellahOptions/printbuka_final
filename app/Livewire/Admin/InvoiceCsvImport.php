<?php

namespace App\Livewire\Admin;

use App\Services\QuoteCsvImportService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class InvoiceCsvImport extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $csvFile = null;

    public function import(QuoteCsvImportService $quoteCsvImportService): void
    {
        abort_unless((auth()->user()?->role ?? null) === 'super_admin', 403);

        $this->validate([
            'csvFile' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $stats = $quoteCsvImportService->import($this->csvFile, auth()->user());

        $this->reset('csvFile');
        $this->dispatch('invoices-imported');

        session()->flash('status', "CSV import complete: {$stats['customers']} customer(s), {$stats['jobs']} delivered job(s), and {$stats['invoices']} paid invoice/quote record(s) processed.");
    }

    public function render(): View
    {
        return view('livewire.admin.invoice-csv-import');
    }
}
