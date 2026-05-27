<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserInvoiceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $invoices = Invoice::query()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('order.product')
            ->latest('issued_at')
            ->paginate(10);

        // Calculate statistics
        $totalInvoices = Invoice::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $pendingAmount = Invoice::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->sum('total_amount');

        $paidAmount = Invoice::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'paid')->sum('total_amount');

        $overdueInvoices = Invoice::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')
          ->where('due_at', '<', now())
          ->count();

        return view('user-invoice.index', [
            'invoices' => $invoices,
            'totalInvoices' => $totalInvoices,
            'pendingAmount' => $pendingAmount,
            'paidAmount' => $paidAmount,
            'overdueInvoices' => $overdueInvoices,
        ]);
    }

    public function show(Invoice $invoice): View|RedirectResponse
    {
        $user = Auth::user();

        // Ensure the invoice belongs to the authenticated user
        if ($invoice->order->user_id !== $user->id) {
            return redirect()->route('user.invoices.index')
                ->with('error', 'You do not have permission to view this invoice.');
        }

        $invoice->load('order.product');
        $settings = \App\Support\SiteSettings::all();

        $companyName = (string) ($settings['site_name'] ?? config('app.name', 'Printbuka'));
        $companyEmail = (string) ($settings['contact_email'] ?? 'sales@printbuka.com.ng');
        $companyPhone = (string) ($settings['contact_phone'] ?? '08035245784, 09054784526');
        $companyAccountName = trim((string) ($settings['company_account_name'] ?? '')) ?: 'Alet Inspirationz';
        $companyAccountNumber = trim((string) ($settings['company_account_number'] ?? '')) ?: '0062999338';
        $companyAccountBankName = trim((string) ($settings['company_account_bank_name'] ?? '')) ?: 'Access bank';
        $companyAccountNote = trim((string) ($settings['company_account_note'] ?? ''));
        $hasCompanyAccountDetails = $companyAccountName !== '' || $companyAccountNumber !== '' || $companyAccountBankName !== '' || $companyAccountNote !== '';

        return view('user-invoice.show', [
            'invoice' => $invoice,
            'companyName' => $companyName,
            'companyEmail' => $companyEmail,
            'companyPhone' => $companyPhone,
            'companyAccountName' => $companyAccountName,
            'companyAccountNumber' => $companyAccountNumber,
            'companyAccountBankName' => $companyAccountBankName,
            'companyAccountNote' => $companyAccountNote,
            'hasCompanyAccountDetails' => $hasCompanyAccountDetails,
        ]);
    }

    public function download(Invoice $invoice)
    {
        $user = Auth::user();

        // Ensure the invoice belongs to the authenticated user
        if ($invoice->order->user_id !== $user->id) {
            return redirect()->route('user.invoices.index')
                ->with('error', 'You do not have permission to download this invoice.');
        }

        // This would typically generate a PDF
        // For now, redirect to show page
        return redirect()->route('user.invoices.show', $invoice)
            ->with('info', 'PDF download feature coming soon!');
    }
}