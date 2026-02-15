<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Alice Williams',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Eve Davis',
                'email' => 'eve@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Frank Miller',
                'email' => 'frank@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Grace Lee',
                'email' => 'grace@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
            [
                'name' => 'Henry Wilson',
                'email' => 'henry@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'timezone' => 'Asia/Singapore',
                'player_token' => base64_encode(\Illuminate\Support\Str::random(64)),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Users created successfully!');
        $this->command->info('');
        $this->command->info('=== SUPERADMIN ACCOUNT ===');
        $this->command->info('Email: superadmin@example.com');
        $this->command->info('Password: superadmin123');
        $this->command->info('');
        $this->command->info('=== REGULAR USER ACCOUNTS ===');
        $this->command->info('All users have password: password123');
        $this->command->info('');
        $this->command->info('Users:');
        foreach ($users as $user) {
            if ($user['role'] === 'user') {
                $this->command->info('  - ' . $user['email'] . ' (' . $user['name'] . ')');
            }
        }
    }
}
