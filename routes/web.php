<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengaliranController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\RencanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\ReportPengaliranController;
use App\Http\Controllers\ReportPemeliharaanController;
use App\Http\Controllers\AlatBeratController;
use App\Http\Controllers\MonitoringAlatBeratController;
use App\Http\Controllers\ReportAlatBeratController;
use App\Http\Controllers\PerizinanLaController;
use App\Http\Controllers\PemetaanLaController;

// Login Routes
Route::get('/', fn() => redirect('/login'));
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::match(['get', 'post'], 'logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (harus login)
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Arsip Dokumen Peta Land Application CRUD
    Route::post('pemetaan-la/bulk-lock', [PemetaanLaController::class, 'bulkLock'])->name('pemetaan-la.bulk-lock');
    Route::post('pemetaan-la/bulk-lock-all', [PemetaanLaController::class, 'bulkLockAll'])->name('pemetaan-la.bulk-lock-all');
    Route::post('pemetaan-la/{id}/toggle-lock', [PemetaanLaController::class, 'toggleLock'])->name('pemetaan-la.toggle-lock');
    Route::resource('pemetaan-la', PemetaanLaController::class);

    // Perizinan Land Application CRUD
    Route::post('perizinan-la/bulk-lock', [PerizinanLaController::class, 'bulkLock'])->name('perizinan-la.bulk-lock');
    Route::post('perizinan-la/{id}/toggle-lock', [PerizinanLaController::class, 'toggleLock'])->name('perizinan-la.toggle-lock');
    Route::resource('perizinan-la', PerizinanLaController::class);

    // Pengaliran CRUD
    Route::resource('pengaliran', PengaliranController::class);
    Route::get('/pengaliran/{id}', [PengaliranController::class, 'show'])
        ->name('pengaliran.show');

    // Pemeliharaan CRUD
    Route::resource('pemeliharaan', PemeliharaanController::class);
    Route::get('/pemeliharaan/{id}', [PemeliharaanController::class, 'show'])
        ->name('pemeliharaan.show');

    // Rencana Pengaliran & Pemeliharaan CRUD
    Route::resource('rencana', RencanaController::class);
    Route::get('report-rencana', [RencanaController::class, 'report'])->name('report-rencana');

    // Master Data Alat Berat CRUD
    Route::resource('alat-berat', AlatBeratController::class);

    // Monitoring Log Alat Berat CRUD
    Route::resource('monitoring-alat-berat', MonitoringAlatBeratController::class);

    // Data Pengguna (Admin)
    Route::post('pengguna/pks/{id}/asisten', [UserController::class, 'updatePksAsisten'])->name('pengguna.pks.asisten');
    Route::post('pengguna/pks/{id}/test-wa', [UserController::class, 'testPksWa'])->name('pengguna.pks.test-wa');
    Route::resource('pengguna', UserController::class);

    // Report
    Route::get('laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian');
    Route::get('report-pengaliran', [ReportPengaliranController::class, 'index'])->name('report-pengaliran');
    Route::get('report-pemeliharaan', [ReportPemeliharaanController::class, 'index'])->name('report-pemeliharaan');
    Route::get('report-alat-berat', [ReportAlatBeratController::class, 'index'])->name('report-alat-berat');

    Route::get('/dashboard/grafik-volume', [DashboardController::class, 'grafikVolume'])
        ->name('dashboard.grafik-volume');

    Route::get('/dashboard/pilihan-filter', [DashboardController::class, 'pilihanFilter'])
        ->name('dashboard.pilihan-filter');

    Route::get('/api/sync/pull', [\App\Http\Controllers\Api\SyncApiController::class, 'pull']);

    // Dedicated Field Operator Routes (Web Laporan Kerja Operator Lapangan)
    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OperatorMonitoringController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\OperatorMonitoringController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\OperatorMonitoringController::class, 'store'])->name('store');
        Route::get('/report/{id}', [\App\Http\Controllers\OperatorMonitoringController::class, 'show'])->name('show');
        Route::get('/report/{id}/edit', [\App\Http\Controllers\OperatorMonitoringController::class, 'edit'])->name('edit');
        Route::put('/report/{id}', [\App\Http\Controllers\OperatorMonitoringController::class, 'update'])->name('update');

        // Kelola Master & Status Alat Berat (Operator)
        Route::get('/alat-berat', [\App\Http\Controllers\OperatorMonitoringController::class, 'alatBeratIndex'])->name('alat-berat.index');
        Route::post('/alat-berat', [\App\Http\Controllers\OperatorMonitoringController::class, 'alatBeratStore'])->name('alat-berat.store');
        Route::put('/alat-berat/{id}', [\App\Http\Controllers\OperatorMonitoringController::class, 'alatBeratUpdate'])->name('alat-berat.update');
        Route::patch('/alat-berat/{id}/status', [\App\Http\Controllers\OperatorMonitoringController::class, 'alatBeratUpdateStatus'])->name('alat-berat.update-status');
        Route::delete('/alat-berat/{id}', [\App\Http\Controllers\OperatorMonitoringController::class, 'alatBeratDestroy'])->name('alat-berat.destroy');

        // PWA Notification & Shift Input Status
        Route::get('/check-today-input', [\App\Http\Controllers\OperatorMonitoringController::class, 'checkTodayInput'])->name('check-today-input');
    });

});
