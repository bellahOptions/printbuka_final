<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = env('PRINTBUKA_ADMIN_PASSWORD', 'PrintbukaAdmin2026!');

        $users = [
            ['name' => 'Printbuka Management', 'email' => 'management@printbuka.local', 'role' => 'management', 'department' => 'Management'],
            ['name' => 'Production Supervisor', 'email' => 'supervisor@printbuka.local', 'role' => 'supervisor', 'department' => 'Supervision'],
            ['name' => 'Afolabi Taiwo', 'email' => 'afolabi.taiwo@printbuka.local', 'role' => 'designer', 'department' => 'Design'],
            ['name' => 'Adeniyi Michael', 'email' => 'adeniyi.michael@printbuka.local', 'role' => 'designer', 'department' => 'Design'],
            ['name' => 'Tosin Mercy', 'email' => 'tosin.mercy@printbuka.local', 'role' => 'production', 'department' => 'Production'],
            ['name' => 'Suru Damilola', 'email' => 'suru.damilola@printbuka.local', 'role' => 'production', 'department' => 'Production'],
            ['name' => 'Saheed Balogun', 'email' => 'saheed.balogun@printbuka.local', 'role' => 'production', 'department' => 'Production'],
            ['name' => 'Lawal Soliu', 'email' => 'lawal.soliu@printbuka.local', 'role' => 'customer_service', 'department' => 'Customer Service'],
            ['name' => 'QC Officer', 'email' => 'qc@printbuka.local', 'role' => 'qc', 'department' => 'Quality Control'],
            ['name' => 'Logistics Officer', 'email' => 'logistics@printbuka.local', 'role' => 'logistics', 'department' => 'Logistics'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'password' => $password,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        }
    }
}
