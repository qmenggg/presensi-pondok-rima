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
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['sakit', 'izin'])->default('sakit');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->comment('Asatid/admin yang approve/reject');
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('alasan_reject')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
