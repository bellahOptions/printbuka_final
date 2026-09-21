<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminCompanyAccountController extends Controller
{
    public function __construct(private readonly PaystackService $paystack)
    {
    }

    public function index(): View
    {
        return view('admin.company-accounts.index', [
            'companyAccounts' => CompanyAccount::query()
                ->orderByDesc('is_default')
                ->orderBy('label')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.company-accounts.form', [
            'companyAccount' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['account_name'] = $this->resolveAccountName($validated['account_number'], $validated['bank_code']);

        DB::transaction(function () use ($validated): void {
            if ($validated['is_default']) {
                CompanyAccount::query()->update(['is_default' => false]);
            }

            CompanyAccount::query()->create($validated);
        });

        return redirect()->route('admin.company-accounts.index')->with('status', 'Company account added.');
    }

    public function edit(CompanyAccount $companyAccount): View
    {
        return view('admin.company-accounts.form', [
            'companyAccount' => $companyAccount,
        ]);
    }

    public function update(Request $request, CompanyAccount $companyAccount): RedirectResponse
    {
        $validated = $this->validated($request);

        $accountChanged = $validated['account_number'] !== $companyAccount->account_number
            || $validated['bank_code'] !== (string) $companyAccount->bank_code;

        $validated['account_name'] = $accountChanged
            ? $this->resolveAccountName($validated['account_number'], $validated['bank_code'])
            : $companyAccount->account_name;

        DB::transaction(function () use ($validated, $companyAccount): void {
            if ($validated['is_default']) {
                CompanyAccount::query()->where('id', '!=', $companyAccount->id)->update(['is_default' => false]);
            }

            $companyAccount->update($validated);
        });

        return redirect()->route('admin.company-accounts.index')->with('status', 'Company account updated.');
    }

    public function destroy(CompanyAccount $companyAccount): RedirectResponse
    {
        $companyAccount->delete();

        return back()->with('status', 'Company account deleted. Invoices that referenced it will fall back to the default account.');
    }

    /**
     * Nigerian banks with their Paystack bank codes, for the "Bank Name" picker.
     */
    public function banks(): JsonResponse
    {
        $result = $this->paystack->listBanks();

        if (! $result['ok']) {
            return response()->json(['ok' => false, 'message' => $result['message'] ?? 'Could not fetch the bank list.'], 502);
        }

        $banks = collect($result['data'])
            ->map(fn (array $bank): array => [
                'code' => (string) ($bank['code'] ?? ''),
                'name' => (string) ($bank['name'] ?? ''),
            ])
            ->filter(fn (array $bank): bool => $bank['code'] !== '' && $bank['name'] !== '')
            ->sortBy('name')
            ->values();

        return response()->json(['ok' => true, 'banks' => $banks]);
    }

    /**
     * Resolve an account number + bank code to the account holder's name via Paystack.
     */
    public function resolveAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_number' => ['required', 'string', 'size:10'],
            'bank_code' => ['required', 'string', 'max:20'],
        ]);

        $result = $this->paystack->resolveAccount($validated['account_number'], $validated['bank_code']);

        if (! $result['ok']) {
            return response()->json(['ok' => false, 'message' => $result['message'] ?? 'Could not resolve this account number.'], 422);
        }

        return response()->json([
            'ok' => true,
            'account_name' => (string) ($result['data']['account_name'] ?? ''),
        ]);
    }

    /**
     * The account holder's name is never trusted from the client — it's always
     * re-derived server-side from Paystack so it can't be tampered with (e.g. via
     * devtools) into showing a name that doesn't match the real bank account.
     */
    private function resolveAccountName(string $accountNumber, string $bankCode): string
    {
        $result = $this->paystack->resolveAccount($accountNumber, $bankCode);

        if (! $result['ok'] || blank($result['data']['account_name'] ?? null)) {
            throw ValidationException::withMessages([
                'account_number' => $result['message'] ?? 'Could not verify this account number with Paystack. Check the account number and bank, then try again.',
            ]);
        }

        return (string) $result['data']['account_name'];
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'digits:10'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_code' => ['required', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
