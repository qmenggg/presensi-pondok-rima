<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom 'aktif' pada tabel tapels
     * Hanya boleh ada 1 tapel aktif dalam satu waktu
     */
    public function up(): void
    {
        Schema::table('tapels', function (Blueprint $table) {
            $table->boolean('aktif')->default(false)->after('tanggal_selesai');
        });

        // Set tapel terakhir sebagai aktif jika ada
        DB::table('tapels')->orderBy('id', 'desc')->limit(1)->update(['aktif' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tapels', function (Blueprint $table) {
            $table->dropColumn('aktif');
        });
    }
};
