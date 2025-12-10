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
        // Create sample users
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'nama' => 'Administrator',
                'jenis_kelamin' => 'L',
                'role' => 'admin',
                'aktif' => true,
            ],
            [
                'username' => 'pengasuh1',
                'password' => Hash::make('password123'),
                'nama' => 'KH. Ulil Albab S.Ag M.Si',
                'jenis_kelamin' => 'L',
                'role' => 'pengasuh',
                'aktif' => true,
            ],
            [
                'username' => 'pengasuh2',
                'password' => Hash::make('password123'),
                'nama' => 'Hj. Isma Rodliyati S.Ag',
                'jenis_kelamin' => 'P',
                'role' => 'pengasuh',
                'aktif' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
            $this->command->info("User processed: {$userData['username']}");
        }
    }
}
