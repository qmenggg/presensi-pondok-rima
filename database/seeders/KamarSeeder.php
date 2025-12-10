<?php

namespace Database\Seeders;

use App\Models\Kamar;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kamars = [
            // Putra - Hamzawie 1-7
            ['nama_kamar' => 'Hamzawie 1', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 2', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 3', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 4', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 5', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 6', 'jenis' => 'putra'],
            ['nama_kamar' => 'Hamzawie 7', 'jenis' => 'putra'],

            // Putri
            ['nama_kamar' => 'An-Nawa', 'jenis' => 'putri'],
            ['nama_kamar' => 'Al-Husna', 'jenis' => 'putri'],
            ['nama_kamar' => 'Az-Zahra', 'jenis' => 'putri'],
            ['nama_kamar' => "Al-Ma'la", 'jenis' => 'putri'],
            ['nama_kamar' => 'Tahfidz', 'jenis' => 'putri'],
        ];

        foreach ($kamars as $kamar) {
            Kamar::updateOrCreate(
                ['nama_kamar' => $kamar['nama_kamar']],
                $kamar
            );
        }
    }
}
