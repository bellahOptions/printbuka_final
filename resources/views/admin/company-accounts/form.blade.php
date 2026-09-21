@extends('layouts.admin')
@section('title', $companyAccount ? 'Edit Company Account' : 'Add Company Account')
@section('content')

<div class="pb-page-header">
    <h1 class="pb-page-title text-xl font-black mb-2">{{ $companyAccount ? 'Edit Company Account' : 'Add Company Account' }}</h1>
    <p class="text-sm text-slate-400">{{ $companyAccount ? 'Update these bank details.' : 'Add a bank account that can be selected as the pay-to account on invoices.' }}</p>
</div>

@if ($errors->any())
    <div class="pb-alert pb-alert-error items-start mb-5">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 mt-0.5 flex-shrink-0" />
        <div>
            <p class="font-semibold">Please review the following issues:</p>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center gap-2">
                        <span class="h-1 w-1 rounded-full bg-red-400"></span>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST"
      action="{{ $companyAccount ? route('admin.company-accounts.update', $companyAccount) : route('admin.company-accounts.store') }}"
      class="max-w-2xl">
    @csrf
    @if($companyAccount)
        @method('PUT')
    @endif

    <div class="pb-card overflow-hidden">
        <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
        <div class="p-5 space-y-5">
            <div>
                <label class="pb-label">Label (internal reference)</label>
                <input type="text" name="label" value="{{ old('label', $companyAccount?->label) }}" required
                       placeholder="e.g. Access Bank — Main"
                       class="pb-input w-full" />
                @error('label') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="pb-label">Account Number</label>
                    <input type="text" id="company-account-number" name="account_number"
                           value="{{ old('account_number', $companyAccount?->account_number) }}" required
                           inputmode="numeric" maxlength="10"
                           class="pb-input w-full" />
                    @error('account_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pb-label">Bank</label>
                    <select id="company-account-bank-select" class="pb-input w-full">
                        <option value="">— Select bank —</option>
                    </select>
                    <p id="company-account-bank-hint" class="mt-1 text-xs text-slate-400">Loading banks from Paystack…</p>
                </div>
            </div>

            <input type="hidden" name="bank_name" id="company-account-bank-name" value="{{ old('bank_name', $companyAccount?->bank_name) }}">
            <input type="hidden" name="bank_code" id="company-account-bank-code" value="{{ old('bank_code', $companyAccount?->bank_code) }}">
            @error('bank_name') <p class="text-xs text-red-600 -mt-2">{{ $message }}</p> @enderror
            @error('bank_code') <p class="text-xs text-red-600 -mt-2">{{ $message }}</p> @enderror

            <div>
                <label class="pb-label">Account Name</label>
                <input type="text" id="company-account-name"
                       value="{{ old('account_name', $companyAccount?->account_name) }}"
                       readonly tabindex="-1"
                       placeholder="Verified automatically from the account number and bank"
                       class="pb-input w-full bg-slate-50 text-slate-700 cursor-not-allowed" />
                <p id="company-account-verify-hint" class="mt-1 text-xs text-slate-400">This is verified against Paystack and can't be edited directly.</p>
            </div>

            <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="checkbox checkbox-sm"
                           @checked(old('is_active', $companyAccount?->is_active ?? true))>
                    Active (selectable on invoices)
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_default" value="1" class="checkbox checkbox-sm"
                           @checked(old('is_default', $companyAccount?->is_default ?? false))>
                    Default account for new invoices
                </label>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">
                    <x-heroicon-o-check class="w-4 h-4" />
                    {{ $companyAccount ? 'Save Changes' : 'Add Account' }}
                </button>
                <a href="{{ route('admin.company-accounts.index') }}" class="pb-btn pb-btn-md pb-btn-ghost">Cancel</a>
            </div>
        </div>
    </div>
</form>

<script>
(() => {
    const bankSelect = document.getElementById('company-account-bank-select');
    const bankNameInput = document.getElementById('company-account-bank-name');
    const bankCodeInput = document.getElementById('company-account-bank-code');
    const bankHint = document.getElementById('company-account-bank-hint');
    const accountNumberInput = document.getElementById('company-account-number');
    const accountNameInput = document.getElementById('company-account-name');
    const verifyHint = document.getElementById('company-account-verify-hint');

    const existingBankCode = bankCodeInput?.value || '';
    const existingBankName = (bankNameInput?.value || '').trim().toLowerCase();

    fetch('{{ route('admin.company-accounts.banks') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.ok || !Array.isArray(data.banks) || data.banks.length === 0) {
                if (bankHint) bankHint.textContent = 'Could not load the bank list from Paystack. Please try again shortly.';
                if (bankSelect) bankSelect.disabled = true;
                return;
            }

            data.banks.forEach(bank => {
                const option = document.createElement('option');
                option.value = bank.code;
                option.textContent = bank.name;
                if (bank.code === existingBankCode || (!existingBankCode && bank.name.trim().toLowerCase() === existingBankName)) {
                    option.selected = true;
                    bankCodeInput.value = bank.code;
                }
                bankSelect.appendChild(option);
            });

            if (bankHint) bankHint.textContent = 'Selecting a bank fills in the name below and enables account name lookup.';
        })
        .catch(() => {
            if (bankHint) bankHint.textContent = 'Could not load the bank list from Paystack. Please try again shortly.';
            if (bankSelect) bankSelect.disabled = true;
        });

    bankSelect?.addEventListener('change', () => {
        const option = bankSelect.selectedOptions[0];
        bankCodeInput.value = bankSelect.value;
        if (bankSelect.value !== '') {
            bankNameInput.value = option.textContent;
        }
        attemptResolve();
    });

    const attemptResolve = () => {
        const accountNumber = (accountNumberInput?.value || '').trim();
        const bankCode = bankCodeInput?.value || '';

        accountNameInput.value = '';

        if (accountNumber.length !== 10 || bankCode === '') {
            if (verifyHint) verifyHint.textContent = 'This is verified against Paystack and can\'t be edited directly.';
            return;
        }

        if (verifyHint) verifyHint.textContent = 'Looking up account name…';

        const url = new URL('{{ route('admin.company-accounts.resolve-account') }}', window.location.origin);
        url.searchParams.set('account_number', accountNumber);
        url.searchParams.set('bank_code', bankCode);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (!data.ok || !data.account_name) {
                    if (verifyHint) verifyHint.textContent = data.message || 'Could not verify this account. Double-check the number and bank.';
                    return;
                }
                accountNameInput.value = data.account_name;
                if (verifyHint) verifyHint.textContent = '✓ Verified via Paystack';
            })
            .catch(() => {
                if (verifyHint) verifyHint.textContent = 'Could not verify this account. Double-check the number and bank.';
            });
    };

    let resolveTimer = null;
    accountNumberInput?.addEventListener('input', () => {
        if (verifyHint) verifyHint.textContent = '';
        clearTimeout(resolveTimer);
        resolveTimer = setTimeout(attemptResolve, 400);
    });
})();
</script>

@endsection
