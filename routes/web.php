<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TapelController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LiburController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\LaporanController;

// ========================================
// PUBLIC ROUTES (Guest Only)
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/signin', [AuthController::class, 'login']);
});

// ========================================
// AUTHENTICATED ROUTES
// ========================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard - semua role (kecuali santri - sudah diblok di middleware)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ========================================
    // ABSENSI - Semua role bisa akses (filter di controller)
    // ========================================
    Route::get('/absensi/hari-ini', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{subKegiatan}/form', \App\Livewire\AbsensiScanner::class)->name('absensi.create');
    // Route::post('/absensi/{subKegiatan}', [AbsensiController::class, 'store'])->name('absensi.store'); // Disabled manual store for now
    Route::post('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');

    // Santri Routes - admin, pengasuh, pengurus
    Route::middleware('role:admin,pengasuh,pengurus')->group(function () {
        Route::resource('santri', SantriController::class);
        Route::get('/santri/qrcode/{qrCode}', [SantriController::class, 'showQRCode'])->name('santri.qrcode');
        Route::get('/qr-image/{filename}', [SantriController::class, 'getQRImage'])->name('santri.qr-image');
        
        // Export & Import Routes
        Route::get('/santri-export', [\App\Http\Controllers\SantriExportController::class, 'export'])->name('santri.export');
        Route::get('/santri-template', [\App\Http\Controllers\SantriExportController::class, 'templatePage'])->name('santri.template');
        Route::get('/santri-import', [\App\Http\Controllers\SantriExportController::class, 'importPage'])->name('santri.import');
        
        // Manajemen Santri (Aktivasi & Pindah Kamar)
        Route::get('/santri-manajemen', [SantriController::class, 'manajemen'])->name('santri.manajemen');
    });

    // Kamar Routes - admin dan pengasuh
    Route::middleware('role:admin,pengasuh')->group(function () {
        Route::resource('kamar', \App\Http\Controllers\KamarController::class);
    });

    // User Management Routes - admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('user', UserController::class);
        Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
    });

    // ========================================
    // KEGIATAN MANAGEMENT
    // ========================================
    
    // Tapel - admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('tapel', TapelController::class)->except(['show']);
    });

    // Kegiatan - admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('kegiatan', KegiatanController::class);
    });

    // Sub Kegiatan - admin dan pengasuh
    Route::middleware('role:admin,pengasuh')->group(function () {
        Route::get('/kegiatan/{kegiatan}/sub', [SubKegiatanController::class, 'index'])->name('sub-kegiatan.index');
        Route::get('/kegiatan/{kegiatan}/sub/create', [SubKegiatanController::class, 'create'])->name('sub-kegiatan.create');
        Route::post('/kegiatan/{kegiatan}/sub', [SubKegiatanController::class, 'store'])->name('sub-kegiatan.store');
        Route::get('/kegiatan/{kegiatan}/sub/{subKegiatan}/edit', [SubKegiatanController::class, 'edit'])->name('sub-kegiatan.edit');
        Route::put('/kegiatan/{kegiatan}/sub/{subKegiatan}', [SubKegiatanController::class, 'update'])->name('sub-kegiatan.update');
        Route::delete('/kegiatan/{kegiatan}/sub/{subKegiatan}', [SubKegiatanController::class, 'destroy'])->name('sub-kegiatan.destroy');
        Route::get('/kegiatan/{kegiatan}/sub/{subKegiatan}/assign', [SubKegiatanController::class, 'assign'])->name('sub-kegiatan.assign');
        Route::post('/kegiatan/{kegiatan}/sub/{subKegiatan}/assign', [SubKegiatanController::class, 'storeAssign'])->name('sub-kegiatan.store-assign');
    });

    // ========================================
    // IZIN & LIBUR
    // ========================================

    // Libur - admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('libur', LiburController::class)->except(['show']);
    });

    // Izin - admin dan pengasuh
    Route::middleware('role:admin,pengasuh')->group(function () {
        Route::resource('izin', IzinController::class);
        Route::post('/izin/{izin}/approve', [IzinController::class, 'approve'])->name('izin.approve');
        Route::post('/izin/{izin}/reject', [IzinController::class, 'reject'])->name('izin.reject');
    });

    // Rekap - semua role
    Route::get('/rekap/{subKegiatan}/{tanggal}', [\App\Http\Controllers\RekapController::class, 'index'])->name('rekap.index');
    Route::post('/rekap/{subKegiatan}/{tanggal}', [\App\Http\Controllers\RekapController::class, 'finalize'])->name('rekap.finalize');

    // Laporan
    Route::get('/laporan/harian', [\App\Http\Controllers\LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan', [\App\Http\Controllers\LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('/laporan/tahunan', [\App\Http\Controllers\LaporanController::class, 'tahunan'])->name('laporan.tahunan');

    // Rekap Approval - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/rekap/approval', [\App\Http\Controllers\RekapApprovalController::class, 'index'])->name('rekap.approval');
        Route::post('/rekap/approval/{log}/approve', [\App\Http\Controllers\RekapApprovalController::class, 'approve'])->name('rekap.approval.approve');
        Route::post('/rekap/approval/{log}/reject', [\App\Http\Controllers\RekapApprovalController::class, 'reject'])->name('rekap.approval.reject');
        Route::post('/rekap/approval/approve-all', [\App\Http\Controllers\RekapApprovalController::class, 'approveAll'])->name('rekap.approval.approve-all');
    });

    // Calendar
    Route::get('/calendar', function () {
        return view('pages.calender', ['title' => 'Calendar']);
    })->name('calendar');

    // Profile
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Profile']);
    })->name('profile');

    // Form pages
    Route::get('/form-elements', function () {
        return view('pages.form.form-elements', ['title' => 'Form Elements']);
    })->name('form-elements');

    // Tables pages
    Route::get('/basic-tables', function () {
        return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
    })->name('basic-tables');

    // Laporan PDF Export
    Route::get('/laporan/harian-pdf', [LaporanController::class, 'exportPdfHarian'])->name('laporan.harian.pdf');
    Route::get('/laporan/bulanan-pdf', [LaporanController::class, 'exportPdfBulanan'])->name('laporan.bulanan.pdf');
    Route::get('/laporan/tahunan-pdf', [LaporanController::class, 'exportPdfTahunan'])->name('laporan.tahunan.pdf');

    // Blank page
    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    // Chart pages
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Line Chart']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
    })->name('bar-chart');

    // UI elements pages
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Images']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');
});

// Error pages (public)
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// Signup page (untuk referensi, tidak digunakan)
Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');
