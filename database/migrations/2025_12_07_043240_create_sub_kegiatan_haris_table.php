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
        Schema::create('sub_kegiatan_haris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_kegiatan_id');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->timestamps();

            $table->foreign('sub_kegiatan_id')->references('id')->on('sub_kegiatans')->onDelete('cascade');
            $table->unique(['sub_kegiatan_id', 'hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatan_haris');
    }
};
