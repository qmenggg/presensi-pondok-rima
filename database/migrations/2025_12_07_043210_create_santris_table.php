<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->comment('One-to-one dengan users');
            $table->foreignId('kamar_id')->nullable()->constrained('kamars');
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('nama_wali', 100);
            $table->string('foto', 255)->nullable();
            $table->string('qr_code', 50)->unique()->comment('Format: QR-24L0010 (tahun+gender+userid)');
            $table->string('qr_code_file', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
