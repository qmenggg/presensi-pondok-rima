<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change status enum from 'alpha' to 'alfa' for consistency
     */
    public function up(): void
    {
        // Update existing data
        DB::table('absensis')->where('status', 'alpha')->update(['status' => 'alfa']);
        
        // Change enum values - MySQL specific
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alfa') DEFAULT 'alfa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data
        DB::table('absensis')->where('status', 'alfa')->update(['status' => 'alpha']);
        
        // Change back to original enum
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') DEFAULT 'alpha'");
    }
};
