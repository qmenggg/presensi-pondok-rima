<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TapelController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\SubKegiatanController;

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
    Route::get('/', function () {
        return view('pages.dashboard.ecommerce', ['title' => 'Dashboard']);
    })->name('dashboard');

    // Santri Routes - admin, pengasuh, pengurus
    Route::middleware('role:admin,pengasuh,pengurus')->group(function () {
        Route::resource('santri', SantriController::class);
        Route::get('/santri/qrcode/{qrCode}', [SantriController::class, 'showQRCode'])->name('santri.qrcode');
        Route::get('/qr-image/{filename}', [SantriController::class, 'getQRImage'])->name('santri.qr-image');
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
