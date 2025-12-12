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
            // ============ ASATID PUTRA (K.) ============
            [
                'username' => 'asatid1',
                'password' => Hash::make('password123'),
                'nama' => 'K. Ali Zuhdi',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid2',
                'password' => Hash::make('password123'),
                'nama' => 'K. M. Hanifuddin',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid3',
                'password' => Hash::make('password123'),
                'nama' => 'K. Ahmad Rofiq',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid4',
                'password' => Hash::make('password123'),
                'nama' => 'K. Abdul Mahin',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid5',
                'password' => Hash::make('password123'),
                'nama' => 'K. Isthifaul Ibad',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid6',
                'password' => Hash::make('password123'),
                'nama' => 'K. Noor Edy Maghfur',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid7',
                'password' => Hash::make('password123'),
                'nama' => 'K. Abdul Khakim',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid8',
                'password' => Hash::make('password123'),
                'nama' => 'K. Ainun Najib',
                'jenis_kelamin' => 'L',
                'role' => 'asatid',
                'aktif' => true,
            ],
            // ============ ASATID PUTRI (Ustadzah) ============
            [
                'username' => 'asatid9',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Yusrotun Ni\'mah Rodli',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid10',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Minan Nur Ida',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid11',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Farikhah Nailil Muna',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid12',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Nur Inayah',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid13',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Yusrotun Ni\'mah Aris',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid14',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Shofiyatun',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
                'aktif' => true,
            ],
            [
                'username' => 'asatid15',
                'password' => Hash::make('password123'),
                'nama' => 'Ustadzah Ainun Nadhiroh',
                'jenis_kelamin' => 'P',
                'role' => 'asatid',
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
