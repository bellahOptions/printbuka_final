<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imports the office "vendors list" CSV export into the vendors table.
 *
 * The source file is a hand-maintained office spreadsheet, not a clean export:
 * columns drift between rows (bank/account text sometimes lands in the VENDOR
 * column instead of ACCOUNT DETAILS), header rows repeat mid-file, typos are
 * common ("moinepoint"), and blank rows are used as visual spacers. This
 * service is deliberately defensive about all of that rather than assuming a
 * clean fixed layout.
 *
 * For every row with a plausible account number, the bank name text is
 * matched against Paystack's live bank list and, when a match is found, the
 * account number + bank code pair is sent to Paystack's account-resolve
 * endpoint so the vendor's bank_account_name is the bank's own record of the
 * account holder — never trusted from free-text in the spreadsheet.
 */
class VendorCsvImportService
{
    /**
     * Normalized (letters-only, uppercased) trigger => normalized fragment to
     * look for in a Paystack bank name. Longest triggers are checked first so
     * a specific alias (e.g. FIRSTCITYMONUMENT) wins over a shorter one that
     * would also match (e.g. FIRST).
     *
     * @var array<string, string>
     */
    private const BANK_ALIASES = [
        'OPAY' => 'OPAY',
        'PAYCOM' => 'OPAY',
        'PALMPAY' => 'PALMPAY',
        'MONIEPOINT' => 'MONIEPOINT',
        'MOINEPOINT' => 'MONIEPOINT',
        'MONEYPOINT' => 'MONIEPOINT',
        'MONIPOINT' => 'MONIEPOINT',
        'KUDA' => 'KUDA',
        'CARBON' => 'CARBON',
        'FAIRMONEY' => 'FAIRMONEY',
        'SPARKLE' => 'SPARKLE',
        'EYOWO' => 'EYOWO',
        'VFD' => 'VFD',
        'MINTFINEX' => 'MINT',
        'RUBIES' => 'RUBIES',
        'ALAT' => 'ALAT',
        'GTBANK' => 'GUARANTYTRUST',
        'GTB' => 'GUARANTYTRUST',
        'GUARANTYTRUST' => 'GUARANTYTRUST',
        'WEMABANK' => 'WEMA',
        'WEMA' => 'WEMA',
        'UNITEDBANKFORAFRICA' => 'UNITEDBANKFORAFRICA',
        'UBA' => 'UNITEDBANKFORAFRICA',
        'FIRSTCITYMONUMENT' => 'FIRSTCITYMONUMENT',
        'FCMB' => 'FIRSTCITYMONUMENT',
        'FIRSTBANKOFNIGERIA' => 'FIRSTBANKOFNIGERIA',
        'FIRSTBANK' => 'FIRSTBANKOFNIGERIA',
        'FBN' => 'FIRSTBANKOFNIGERIA',
        'ACCESSBANK' => 'ACCESS',
        'ACCESS' => 'ACCESS',
        'ZENITHBANK' => 'ZENITH',
        'ZENITH' => 'ZENITH',
        'UNIONBANK' => 'UNION',
        'STERLINGBANK' => 'STERLING',
        'STERLING' => 'STERLING',
        'STANBICIBTC' => 'STANBICIBTC',
        'STANBIC' => 'STANBICIBTC',
        'IBTC' => 'STANBICIBTC',
        'ECOBANK' => 'ECOBANK',
        'FIDELITYBANK' => 'FIDELITY',
        'FIDELITY' => 'FIDELITY',
        'POLARISBANK' => 'POLARIS',
        'POLARIS' => 'POLARIS',
        'PROVIDUS' => 'PROVIDUS',
        'JAIZBANK' => 'JAIZ',
        'JAIZ' => 'JAIZ',
        'HERITAGEBANK' => 'HERITAGE',
        'HERITAGE' => 'HERITAGE',
        'KEYSTONEBANK' => 'KEYSTONE',
        'KEYSTONE' => 'KEYSTONE',
        'UNITYBANK' => 'UNITY',
        'GLOBUSBANK' => 'GLOBUS',
        'GLOBUS' => 'GLOBUS',
        'TITANTRUST' => 'TITANTRUST',
        'TITAN' => 'TITANTRUST',
        'CORONATION' => 'CORONATION',
        'SIGNATUREBANK' => 'SIGNATURE',
        'SIGNATURE' => 'SIGNATURE',
        'OPTIMUS' => 'OPTIMUS',
        'PARALLEX' => 'PARALLEX',
        'PREMIUMTRUST' => 'PREMIUMTRUST',
        'CITIBANK' => 'CITI',
        'STANDARDCHARTERED' => 'STANDARDCHARTERED',
        'SUNTRUST' => 'SUNTRUST',
    ];

    /** Noise words stripped from Paystack bank names before comparison. */
    private const BANK_NAME_NOISE = [
        'MICROFINANCEBANK', 'MICROFINANCE', 'PAYMENTSERVICEBANK', 'DIGITALSERVICES',
        'FINANCIALSERVICES', 'BANKPLC', 'NIGERIALIMITED', 'NIGERIAPLC', 'LIMITED',
        'NIGERIA', 'PLC', 'BANK', 'MFB', 'PSB',
    ];

    public function __construct(private readonly PaystackService $paystack)
    {
    }

    /**
     * @return array{processed:int,created:int,updated:int,resolved:int,unresolved:int,skipped:int,rows:array<int,array<string,mixed>>}
     */
    public function import(UploadedFile $file, ?User $actor = null): array
    {
        $banks = $this->bankDirectory();
        $rawRows = $this->readRows($file->getRealPath());

        $stats = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'resolved' => 0,
            'unresolved' => 0,
            'skipped' => 0,
            'rows' => [],
        ];

        DB::transaction(function () use ($rawRows, $banks, $actor, $file, &$stats): void {
            foreach ($rawRows as $lineNumber => $columns) {
                $parsed = $this->parseRow($columns);

                if ($parsed === null) {
                    $stats['skipped']++;

                    continue;
                }

                $stats['processed']++;

                $bankMatch = $parsed['bank_text'] !== ''
                    ? $this->matchBank($parsed['bank_text'], $banks)
                    : null;

                $resolution = null;
                if ($parsed['account_number'] !== null && strlen($parsed['account_number']) === 10 && $bankMatch !== null) {
                    $resolution = $this->safeResolveAccount($parsed['account_number'], $bankMatch['code']);
                }

                $resolved = $resolution !== null && ($resolution['ok'] ?? false) && filled($resolution['data']['account_name'] ?? null);

                [$vendor, $wasRecentlyCreated] = $this->upsertVendor($parsed, $bankMatch, $resolved ? $resolution['data']['account_name'] : null, $actor, $file->getClientOriginalName());

                $wasRecentlyCreated ? $stats['created']++ : $stats['updated']++;

                if ($resolved) {
                    $stats['resolved']++;
                } elseif ($parsed['account_number'] !== null || $parsed['bank_text'] !== '') {
                    $stats['unresolved']++;
                }

                $stats['rows'][] = [
                    'line' => $lineNumber,
                    'name' => $parsed['name'],
                    'vendor_id' => $vendor->id,
                    'vendor_uuid' => $vendor->uuid ?? null,
                    'action' => $wasRecentlyCreated ? 'created' : 'updated',
                    'account_number' => $parsed['account_number'],
                    'bank_name' => $bankMatch['name'] ?? ($parsed['bank_text'] ?: null),
                    'resolved' => $resolved,
                    'account_name' => $resolved ? $resolution['data']['account_name'] : null,
                    'message' => $this->rowMessage($parsed, $bankMatch, $resolution, $resolved),
                ];
            }
        });

        return $stats;
    }

    /**
     * @param  array<int, string>  $columns
     * @return array{name:string,street:string,phone:string,category:string,account_number:?string,bank_text:string,raw:string}|null
     */
    private function parseRow(array $columns): ?array
    {
        $name = trim((string) ($columns[0] ?? ''));

        // Repeated header row (the sheet has two header blocks) or a blank
        // spacer row used purely for visual separation in the office file.
        if ($name === '' || Str::upper($name) === 'NAMES') {
            return null;
        }

        $street = trim((string) ($columns[3] ?? ''));
        $phoneDigits = preg_replace('/\D+/', '', (string) ($columns[5] ?? '')) ?? '';
        $vendorCol = trim((string) ($columns[6] ?? ''));
        $accountCol1 = trim((string) ($columns[7] ?? ''));
        $accountCol2 = trim((string) ($columns[8] ?? ''));

        // The office file isn't consistent about which column carries the
        // "<account number> <bank name>" text — it's usually ACCOUNT DETAILS,
        // but several rows have it folded into the VENDOR column instead, or
        // spilling into the trailing 9th column. Try the dedicated columns
        // first and only fall back to the VENDOR column.
        $source = $accountCol1 !== '' ? $accountCol1 : ($accountCol2 !== '' ? $accountCol2 : $vendorCol);

        $accountNumber = $this->extractAccountNumber($source);
        $bankText = $this->extractBankText($source, $accountNumber);

        // Whichever column supplied the account/bank text, strip that same
        // information back out of the VENDOR column so it doesn't pollute
        // the imported "category" label.
        $category = $this->stripAccountNoise($vendorCol, $accountNumber, $bankText);

        return [
            'name' => $name,
            'street' => $street,
            'phone' => $this->normalizePhone($phoneDigits),
            'category' => $category !== '' ? $category : $vendorCol,
            'account_number' => $accountNumber,
            'bank_text' => $bankText,
            'raw' => implode(',', array_map(fn (string $c): string => trim($c), $columns)),
        ];
    }

    private function normalizePhone(string $digits): ?string
    {
        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 10) {
            return '0'.$digits;
        }

        return $digits;
    }

    private function extractAccountNumber(string $text): ?string
    {
        if ($text === '' || ! preg_match_all('/\d{6,}/', $text, $matches)) {
            return null;
        }

        $candidates = $matches[0];

        // Nigerian NUBAN account numbers are exactly 10 digits — prefer that
        // if present, otherwise fall back to the longest digit run found so
        // the raw number is still captured for manual review.
        foreach ($candidates as $candidate) {
            if (strlen($candidate) === 10) {
                return $candidate;
            }
        }

        usort($candidates, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $longest = $candidates[0];

        return strlen($longest) <= 11 ? $longest : substr($longest, 0, 11);
    }

    private function extractBankText(string $text, ?string $accountNumber): string
    {
        $stripped = $accountNumber !== null ? str_replace($accountNumber, ' ', $text) : $text;
        $stripped = preg_replace('/\d+/', ' ', $stripped) ?? '';

        $words = array_filter(
            preg_split('/[\s,;\/]+/', $stripped) ?: [],
            fn (string $word): bool => mb_strlen($word) > 1
        );

        return trim(implode(' ', $words));
    }

    private function stripAccountNoise(string $text, ?string $accountNumber, string $bankText): string
    {
        if ($text === '') {
            return '';
        }

        $result = $accountNumber !== null ? str_replace($accountNumber, ' ', $text) : $text;

        foreach (preg_split('/\s+/', $bankText) ?: [] as $bankWord) {
            if ($bankWord === '') {
                continue;
            }
            $result = preg_replace('/\b'.preg_quote($bankWord, '/').'\b/i', ' ', $result) ?? $result;
        }

        $result = preg_replace('/\s+/', ' ', $result) ?? $result;

        return trim($result, " \t\n\r\0\x0B,");
    }

    /**
     * @return array{code:string,name:string}|null
     */
    private function matchBank(string $bankText, array $banks): ?array
    {
        if ($banks === [] || $bankText === '') {
            return null;
        }

        $normalizedText = $this->normalizeBankToken($bankText);

        if ($normalizedText === '') {
            return null;
        }

        // 1. Alias table — longest trigger wins so specific shorthand beats
        // a shorter ambiguous one (e.g. FCMB before a generic "FIRST").
        $triggers = self::BANK_ALIASES;
        uksort($triggers, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($triggers as $trigger => $fragment) {
            if (str_contains($normalizedText, $trigger)) {
                $match = $this->findBankByFragment($fragment, $banks);
                if ($match !== null) {
                    return $match;
                }
            }
        }

        // 2. Direct contains match against the live bank list.
        if (mb_strlen($normalizedText) >= 4) {
            $match = $this->findBankByFragment($normalizedText, $banks);
            if ($match !== null) {
                return $match;
            }
        }

        // 3. Typo-tolerant fallback (e.g. "moinepoint").
        if (mb_strlen($normalizedText) >= 6) {
            $best = null;
            $bestDistance = 3; // max edit distance accepted

            foreach ($banks as $bank) {
                $distance = levenshtein($normalizedText, mb_substr($bank['normalized'], 0, mb_strlen($normalizedText) + 3));
                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $best = $bank;
                }
            }

            if ($best !== null) {
                return ['code' => $best['code'], 'name' => $best['name']];
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{code:string,name:string,normalized:string}>  $banks
     * @return array{code:string,name:string}|null
     */
    private function findBankByFragment(string $fragment, array $banks): ?array
    {
        $exact = null;
        $shortestContains = null;

        foreach ($banks as $bank) {
            if ($bank['normalized'] === $fragment) {
                $exact = $bank;

                break;
            }

            if (str_contains($bank['normalized'], $fragment)) {
                if ($shortestContains === null || mb_strlen($bank['normalized']) < mb_strlen($shortestContains['normalized'])) {
                    $shortestContains = $bank;
                }
            }
        }

        $found = $exact ?? $shortestContains;

        return $found !== null ? ['code' => $found['code'], 'name' => $found['name']] : null;
    }

    private function normalizeBankToken(string $text): string
    {
        $text = Str::upper($text);
        $text = preg_replace('/[^A-Z]/', '', $text) ?? '';

        foreach (self::BANK_NAME_NOISE as $noise) {
            $text = str_replace($noise, '', $text);
        }

        return $text;
    }

    /**
     * @return array{ok:bool,data?:array<string,mixed>,message?:string}
     */
    private function safeResolveAccount(string $accountNumber, string $bankCode): array
    {
        // A network-level failure (DNS/TLS/timeout) throws a ConnectionException
        // rather than returning ok:false — never let one flaky row abort the
        // whole import transaction and roll back rows already resolved.
        try {
            return $this->paystack->resolveAccount($accountNumber, $bankCode);
        } catch (\Throwable $e) {
            report($e);

            return ['ok' => false, 'message' => 'Could not reach Paystack to verify this account: '.$e->getMessage()];
        }
    }

    /**
     * @return array<int, array{code:string,name:string,normalized:string}>
     */
    private function bankDirectory(): array
    {
        try {
            $result = $this->paystack->listBanks();
        } catch (\Throwable $e) {
            report($e);

            return [];
        }

        if (! ($result['ok'] ?? false)) {
            return [];
        }

        return collect($result['data'])
            ->map(fn (array $bank): array => [
                'code' => (string) ($bank['code'] ?? ''),
                'name' => (string) ($bank['name'] ?? ''),
                'normalized' => $this->normalizeBankToken((string) ($bank['name'] ?? '')),
            ])
            ->filter(fn (array $bank): bool => $bank['code'] !== '' && $bank['normalized'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array{name:string,street:string,phone:?string,category:string,account_number:?string,bank_text:string,raw:string}  $parsed
     * @param  array{code:string,name:string}|null  $bankMatch
     * @return array{0:Vendor,1:bool}
     */
    private function upsertVendor(array $parsed, ?array $bankMatch, ?string $resolvedAccountName, ?User $actor, string $sourceFileName): array
    {
        $criteria = $parsed['account_number'] !== null
            ? ['bank_account_number' => $parsed['account_number']]
            : ['name' => $parsed['name']];

        /** @var Vendor|null $existing */
        $existing = Vendor::query()->where($criteria)->first();

        if ($existing === null) {
            $vendor = new Vendor();
            $vendor->fill([
                'name' => $parsed['name'],
                'vendor_type' => 'Service Provider',
                'status' => 'active',
                'created_by' => $actor?->id,
                'imported_from' => 'csv:'.$sourceFileName,
            ]);
        } else {
            $vendor = $existing;
        }

        // Fill-if-blank for descriptive fields so a re-import (or a match
        // against a vendor someone already curated by hand) never clobbers
        // data that's already there.
        if (blank($vendor->category) && $parsed['category'] !== '') {
            $vendor->category = $parsed['category'];
        }
        if (blank($vendor->address) && $parsed['street'] !== '') {
            $vendor->address = $parsed['street'];
        }
        if (blank($vendor->phone) && $parsed['phone'] !== null) {
            $vendor->phone = $parsed['phone'];
        }
        if (blank($vendor->country)) {
            $vendor->country = 'Nigeria';
        }

        // Bank details: only overwrite when this import produced a
        // Paystack-verified account name, or the vendor has no bank details
        // yet at all. A previously verified account is never overwritten by
        // an unverified guess from a later row.
        $shouldSetBank = $resolvedAccountName !== null || ! $vendor->isAccountVerified();

        if ($resolvedAccountName !== null) {
            $vendor->bank_name = $bankMatch['name'];
            $vendor->bank_code = $bankMatch['code'];
            $vendor->bank_account_number = $parsed['account_number'];
            $vendor->bank_account_name = $resolvedAccountName;
            $vendor->account_verified_at = now();
        } elseif ($shouldSetBank) {
            if (blank($vendor->bank_account_number) && $parsed['account_number'] !== null) {
                $vendor->bank_account_number = $parsed['account_number'];
            }
            if (blank($vendor->bank_name) && ($bankMatch['name'] ?? $parsed['bank_text']) !== '') {
                $vendor->bank_name = $bankMatch['name'] ?? $parsed['bank_text'];
            }
            if (blank($vendor->bank_code) && $bankMatch !== null) {
                $vendor->bank_code = $bankMatch['code'];
            }
        }

        $wasRecentlyCreated = ! $vendor->exists;

        if ($wasRecentlyCreated) {
            $note = 'Imported from CSV ('.$sourceFileName.') on '.now()->toDateString().". Raw row: {$parsed['raw']}";
            $vendor->notes = trim(($vendor->notes ? $vendor->notes."\n\n" : '').$note);
        }

        $vendor->save();

        return [$vendor, $wasRecentlyCreated];
    }

    /**
     * @param  array{name:string,street:string,phone:?string,category:string,account_number:?string,bank_text:string,raw:string}  $parsed
     * @param  array{code:string,name:string}|null  $bankMatch
     * @param  array{ok:bool,data?:array<string,mixed>,message?:string}|null  $resolution
     */
    private function rowMessage(array $parsed, ?array $bankMatch, ?array $resolution, bool $resolved): string
    {
        if ($resolved) {
            return 'Verified via Paystack as "'.$resolution['data']['account_name'].'".';
        }

        if ($parsed['account_number'] === null) {
            return 'No account number found in this row — add bank details manually.';
        }

        if (strlen($parsed['account_number']) !== 10) {
            return "Account number \"{$parsed['account_number']}\" is not a standard 10-digit NUBAN — could not auto-verify.";
        }

        if ($bankMatch === null) {
            $hint = $parsed['bank_text'] !== '' ? " (\"{$parsed['bank_text']}\")" : '';

            return "Could not match the bank name{$hint} to a known Nigerian bank — set it manually.";
        }

        return 'Paystack could not verify this account: '.($resolution['message'] ?? 'unknown error').'.';
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return [];
        }

        // Strip a UTF-8 BOM if present so the first NAMES header is detected.
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];
        $lineNumber = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if ($line === [null] || $line === false) {
                continue;
            }

            $rows[$lineNumber] = array_map(fn (mixed $cell): string => (string) $cell, $line);
        }

        fclose($handle);

        return $rows;
    }
}
