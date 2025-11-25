<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@pos.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole(RolesEnum::SUPER_ADMIN);
        $this->command->info('Created Super Admin: superadmin');

        // Create Admin
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RolesEnum::ADMIN);
        $this->command->info('Created Admin: admin');

        // Create Cashiers
        $cashier1 = User::create([
            'name' => 'cashier1',
            'email' => 'cashier1@pos.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $cashier1->assignRole(RolesEnum::CASHIER);
        $this->command->info('Created Cashier: cashier1');

        $cashier2 = User::create([
            'name' => 'cashier2',
            'email' => 'cashier2@pos.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $cashier2->assignRole(RolesEnum::CASHIER);
        $this->command->info('Created Cashier: cashier2');

        $this->command->warn('All test users created with password: password');
        $this->command->warn('IMPORTANT: Change these passwords in production!');
    }
}
