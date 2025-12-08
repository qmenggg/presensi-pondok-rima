<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to include ALL option for pengurus who can manage all santri
        DB::statement("ALTER TABLE users MODIFY jenis_kelamin ENUM('L', 'P', 'ALL') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any ALL values back to L (or handle as needed)
        DB::statement("UPDATE users SET jenis_kelamin = 'L' WHERE jenis_kelamin = 'ALL'");
        DB::statement("ALTER TABLE users MODIFY jenis_kelamin ENUM('L', 'P') NOT NULL");
    }
};
