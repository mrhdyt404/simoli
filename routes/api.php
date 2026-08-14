<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MonitoringApiController;
use App\Http\Controllers\Api\SyncApiController;

/*
|--------------------------------------------------------------------------
| API Routes for SIMOLI Mobile App & PWA Offline Sync
|--------------------------------------------------------------------------
*/

Route::post('login', [AuthController::class, 'login']);
Route::get('user', [AuthController::class, 'user']);

// PWA Offline-First Sync Routes
Route::get('sync/pull', [SyncApiController::class, 'pull']);
Route::post('sync/push', [SyncApiController::class, 'push']);

Route::get('dashboard/stats', [MonitoringApiController::class, 'dashboardStats']);
Route::get('master-data', [MonitoringApiController::class, 'masterData']);

Route::get('monitoring-alat-berat', [MonitoringApiController::class, 'monitoringAlatBeratList']);
Route::get('monitoring-alat-berat/{id}', [MonitoringApiController::class, 'monitoringAlatBeratDetail']);
Route::post('monitoring-alat-berat', [MonitoringApiController::class, 'storeMonitoringAlatBerat']);

Route::get('pengaliran', [MonitoringApiController::class, 'pengaliranList']);
Route::get('pemeliharaan', [MonitoringApiController::class, 'pemeliharaanList']);
