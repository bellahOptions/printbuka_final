<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateStaffExportToken extends Command
{
    protected $signature = 'staff:export-token:create {--email= : Email of the admin user to attach the token to (defaults to the first super_admin)}';

    protected $description = 'Mint a short-lived Sanctum token scoped only to the staff-export API, for the Alet migration.';

    public function handle(): int
    {
        $user = $this->option('email')
            ? User::where('email', $this->option('email'))->first()
            : User::where('role', 'super_admin')->orderBy('id')->first();

        if (! $user) {
            $this->error('No matching user found to attach the token to.');
            return self::FAILURE;
        }

        $token = $user->createToken(
            name: 'alet-staff-export-' . now()->format('Ymd-His'),
            abilities: ['staff-export'],
            expiresAt: now()->addDay(),
        );

        $this->info('Export token created (expires in 24h, ability: staff-export only).');
        $this->line('Attached to: ' . $user->email);
        $this->newLine();
        $this->line($token->plainTextToken);
        $this->newLine();
        $this->warn('Copy this now — it will not be shown again. Revoke it after the migration with: php artisan staff:revoke-export-tokens');

        return self::SUCCESS;
    }
}
