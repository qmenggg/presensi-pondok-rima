<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin if not exists
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'nama' => 'Administrator',
                'jenis_kelamin' => 'L',
                'role' => 'admin',
                'aktif' => true,
            ]);
            $this->command->info('Admin user created: admin / password123');
        } else {
            $this->command->info('Admin user already exists, skipping...');
        }

        // Create sample users for each role (optional - for testing)
        $sampleUsers = [
            [
                'username' => 'pengasuh1',
                'nama' => 'Ulil ALbab Muhubbi',
                'jenis_kelamin' => 'L',
                'role' => 'pengasuh',
            ],
            [
                'username' => 'pengasuh1',
                'nama' => 'Ulil ALbab Muhubbi',
                'jenis_kelamin' => 'L',
                'role' => 'pengasuh',
            ],
            [
                'username' => 'pengurus1',
                'nama' => 'Pengurus',
                'jenis_kelamin' => 'P',
                'role' => 'pengurus',
            ],
            [
                'username' => 'asatid1',
                'nama' => 'Ustadz Mahmud',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
            ],
        ];

        foreach ($sampleUsers as $userData) {
            if (!User::where('username', $userData['username'])->exists()) {
                User::create([
                    'username' => $userData['username'],
                    'password' => Hash::make('password123'),
                    'nama' => $userData['nama'],
                    'jenis_kelamin' => $userData['jenis_kelamin'],
                    'role' => $userData['role'],
                    'aktif' => true,
                ]);
                $this->command->info("Created user: {$userData['username']} / password123");
            }
        }
    }
}
