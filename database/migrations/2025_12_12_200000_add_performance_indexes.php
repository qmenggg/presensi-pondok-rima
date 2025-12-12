<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds indexes to frequently queried columns
     * for improved query performance.
     */
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('aktif', 'idx_users_aktif');
            $table->index('jenis_kelamin', 'idx_users_jenis_kelamin');
        });

        // Santris table indexes
        Schema::table('santris', function (Blueprint $table) {
            $table->index('kamar_id', 'idx_santris_kamar_id');
            $table->index('qr_code', 'idx_santris_qr_code');
        });

        // Absensis table indexes
        Schema::table('absensis', function (Blueprint $table) {
            $table->index(['sub_kegiatan_id', 'tanggal'], 'idx_absensis_subkegiatan_tanggal');
            $table->index('santri_id', 'idx_absensis_santri_id');
            $table->index('status', 'idx_absensis_status');
            $table->index('tanggal', 'idx_absensis_tanggal');
        });

        // Sub Kegiatans table indexes
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->index('kegiatan_id', 'idx_subkegiatans_kegiatan_id');
            $table->index('untuk_jenis_santri', 'idx_subkegiatans_jenis_santri');
        });

        // Izins table indexes
        Schema::table('izins', function (Blueprint $table) {
            $table->index('santri_id', 'idx_izins_santri_id');
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_izins_tanggal_range');
            $table->index('status', 'idx_izins_status');
        });

        // Liburs table indexes
        Schema::table('liburs', function (Blueprint $table) {
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_liburs_tanggal_range');
            $table->index('jenis', 'idx_liburs_jenis');
        });

        // Kegiatans table indexes
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->index('tapel_id', 'idx_kegiatans_tapel_id');
        });

        // Sub Kegiatan Haris table indexes
        Schema::table('sub_kegiatan_haris', function (Blueprint $table) {
            $table->index('sub_kegiatan_id', 'idx_subkegiatanharis_subkegiatan_id');
            $table->index('hari', 'idx_subkegiatanharis_hari');
        });

        // Sub Kegiatan Kamars table indexes
        Schema::table('sub_kegiatan_kamars', function (Blueprint $table) {
            $table->index('sub_kegiatan_id', 'idx_subkegiatankamars_subkegiatan_id');
            $table->index('kamar_id', 'idx_subkegiatankamars_kamar_id');
        });

        // Sub Kegiatan Santris table indexes
        Schema::table('sub_kegiatan_santris', function (Blueprint $table) {
            $table->index('sub_kegiatan_id', 'idx_subkegiatansantris_subkegiatan_id');
            $table->index('santri_id', 'idx_subkegiatansantris_santri_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_aktif');
            $table->dropIndex('idx_users_jenis_kelamin');
        });

        Schema::table('santris', function (Blueprint $table) {
            $table->dropIndex('idx_santris_kamar_id');
            $table->dropIndex('idx_santris_qr_code');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensis_subkegiatan_tanggal');
            $table->dropIndex('idx_absensis_santri_id');
            $table->dropIndex('idx_absensis_status');
            $table->dropIndex('idx_absensis_tanggal');
        });

        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->dropIndex('idx_subkegiatans_kegiatan_id');
            $table->dropIndex('idx_subkegiatans_jenis_santri');
        });

        Schema::table('izins', function (Blueprint $table) {
            $table->dropIndex('idx_izins_santri_id');
            $table->dropIndex('idx_izins_tanggal_range');
            $table->dropIndex('idx_izins_status');
        });

        Schema::table('liburs', function (Blueprint $table) {
            $table->dropIndex('idx_liburs_tanggal_range');
            $table->dropIndex('idx_liburs_jenis');
        });

        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropIndex('idx_kegiatans_tapel_id');
        });

        Schema::table('sub_kegiatan_haris', function (Blueprint $table) {
            $table->dropIndex('idx_subkegiatanharis_subkegiatan_id');
            $table->dropIndex('idx_subkegiatanharis_hari');
        });

        Schema::table('sub_kegiatan_kamars', function (Blueprint $table) {
            $table->dropIndex('idx_subkegiatankamars_subkegiatan_id');
            $table->dropIndex('idx_subkegiatankamars_kamar_id');
        });

        Schema::table('sub_kegiatan_santris', function (Blueprint $table) {
            $table->dropIndex('idx_subkegiatansantris_subkegiatan_id');
            $table->dropIndex('idx_subkegiatansantris_santri_id');
        });
    }
};
