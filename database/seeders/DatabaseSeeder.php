<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Resya Anggara',
                'email' => 'resya1@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'Customer · Cluster Beji',
                'role_type' => 'customer',
                'location' => 'Beji, Depok',
                'avatar' => null,
                'phone' => '081234567890',
                'savings' => 'Rp 95.4k',
                'sessions_count' => 12,
                'co2_saved' => '28.4 kg',
                'badge' => 'Top Saver',
            ],
            [
                'name' => 'dimas',
                'email' => 'dimas1@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'Session Creator · Beji',
                'role_type' => 'creator',
                'location' => 'Margonda, Depok',
                'avatar' => null,
                'phone' => '081298765432',
                'savings' => 'Rp 142.0k',
                'sessions_count' => 24,
                'co2_saved' => '45.1 kg',
                'badge' => 'Community Hero',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
