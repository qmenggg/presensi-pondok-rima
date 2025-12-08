<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans');
            $table->string('nama_sub_kegiatan', 100)->comment('Contoh: Tahfidz Pagi Kelas A');
            $table->text('keterangan')->nullable();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('untuk_jenis_santri', ['putra', 'putri', 'campur']);
            $table->string('lokasi', 100)->nullable();
            $table->boolean('wajib_hadir')->default(true);
            $table->boolean('rutin_mingguan')->default(true);
            $table->foreignId('guru_penanggung_jawab')->nullable()->constrained('users')->comment('FK ke users (asatid)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatans');
    }
};
