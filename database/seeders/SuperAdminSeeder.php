<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin account
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
            ]
        );

        // Update existing admin to regular user
        User::where('email', 'admin@example.com')->update([
            'role' => 'user',
        ]);

        $this->command->info('Superadmin account created!');
        $this->command->info('Email: superadmin@example.com');
        $this->command->info('Password: superadmin123');
    }
}
