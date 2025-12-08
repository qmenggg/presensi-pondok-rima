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
            $table->string('nama_sub_kegiatan', 100)->comment('Contoh: Sholat Subuh Berjamaah');
            $table->text('keterangan')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->enum('untuk_jenis_santri', ['putra', 'putri', 'campur']);
            $table->string('lokasi', 100)->nullable();
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
