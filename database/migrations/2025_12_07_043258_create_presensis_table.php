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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris');
            $table->foreignId('sub_kegiatan_id')->constrained('sub_kegiatans');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'alfa', 'sakit', 'libur']);
            $table->text('keterangan')->nullable();
            $table->enum('metode_input', ['qr_scan', 'manual']);
            $table->foreignId('diinput_oleh')->constrained('users')->comment('Pengurus/asatid yang input');
            $table->timestamps();

            $table->unique(['santri_id', 'sub_kegiatan_id', 'tanggal']);
            $table->index(['santri_id', 'tanggal']);
            $table->index(['sub_kegiatan_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
