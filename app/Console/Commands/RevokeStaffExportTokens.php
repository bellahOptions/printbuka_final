<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class RevokeStaffExportTokens extends Command
{
    protected $signature = 'staff:revoke-export-tokens';

    protected $description = 'Revoke every Sanctum token scoped to the staff-export ability (run after the Alet migration finishes).';

    public function handle(): int
    {
        $tokens = PersonalAccessToken::query()
            ->whereJsonContains('abilities', 'staff-export')
            ->get();

        if ($tokens->isEmpty()) {
            $this->info('No staff-export tokens found.');
            return self::SUCCESS;
        }

        $count = $tokens->count();
        PersonalAccessToken::destroy($tokens->pluck('id'));

        $this->info("Revoked {$count} staff-export token(s).");

        return self::SUCCESS;
    }
}
