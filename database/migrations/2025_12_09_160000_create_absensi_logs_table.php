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
        Schema::create('absensi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->constrained()->onDelete('cascade');
            $table->foreignId('santri_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_kegiatan_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            $table->foreignId('diubah_oleh')->constrained('users');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'auto_saved'])->default('pending');
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'approval_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_logs');
    }
};
