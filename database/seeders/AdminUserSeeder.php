<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'ahmed@printbuka.com.ng'],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Bello',
                'phone' => '08108671804',
                'companyName' => 'Printbuka',
                'role' => 'super_admin',
                'department' => 'IT',
                'requested_role' => null,
                'other_role' => null,
                'address' => null,
                'date_of_birth' => null,
                'photo' => null,
                'approved_by_id' => null,
                'approved_at' => now(),
                'password' => Hash::make('#Panaman247'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
