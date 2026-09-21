@extends('layouts.admin')
@section('title', 'Company Accounts')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">Company Accounts</h1>
            <p class="text-sm text-slate-400">Bank accounts the business gets paid into. Pick which one shows on each invoice from the invoice's page.</p>
        </div>
        <a href="{{ route('admin.company-accounts.create') }}" class="pb-btn pb-btn-md pb-btn-primary self-start">
            <x-heroicon-o-plus class="w-4 h-4" /> Add Account
        </a>
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

<div class="pb-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="pb-table w-full">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Bank</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companyAccounts as $account)
                    <tr>
                        <td>
                            <p class="font-bold text-slate-900">{{ $account->label }}</p>
                            @if($account->is_default)
                                <span class="pb-badge pb-badge-info mt-1">Default on new invoices</span>
                            @endif
                        </td>
                        <td class="text-sm text-slate-600">{{ $account->account_name }}</td>
                        <td class="text-sm font-mono text-slate-600">{{ $account->account_number }}</td>
                        <td class="text-sm text-slate-600">{{ $account->bank_name }}</td>
                        <td>
                            @if($account->is_active)
                                <span class="pb-badge pb-badge-success">Active</span>
                            @else
                                <span class="pb-badge pb-badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.company-accounts.edit', $account) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                            <form method="POST" action="{{ route('admin.company-accounts.destroy', $account) }}" class="inline" onsubmit="return confirm('Delete this account? Invoices using it will fall back to the default account.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pb-btn pb-btn-sm pb-btn-ghost text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="pb-empty">No company accounts yet. Add one to choose which bank details show on invoices.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
