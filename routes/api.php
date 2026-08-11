<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MonitoringApiController;

/*
|--------------------------------------------------------------------------
| API Routes for SIMOLI Mobile App (Monitoring Module)
|--------------------------------------------------------------------------
*/

Route::post('login', [AuthController::class, 'login']);
Route::get('user', [AuthController::class, 'user']);

Route::get('dashboard/stats', [MonitoringApiController::class, 'dashboardStats']);
Route::get('master-data', [MonitoringApiController::class, 'masterData']);

Route::get('monitoring-alat-berat', [MonitoringApiController::class, 'monitoringAlatBeratList']);
Route::get('monitoring-alat-berat/{id}', [MonitoringApiController::class, 'monitoringAlatBeratDetail']);
Route::post('monitoring-alat-berat', [MonitoringApiController::class, 'storeMonitoringAlatBerat']);

Route::get('pengaliran', [MonitoringApiController::class, 'pengaliranList']);
Route::get('pemeliharaan', [MonitoringApiController::class, 'pemeliharaanList']);
