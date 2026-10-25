<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MonitoringApiController;
use App\Http\Controllers\Api\SyncApiController;
use App\Http\Controllers\Api\IotPengaliranApiController;

/*
|--------------------------------------------------------------------------
| API Routes for SIMOLI Mobile App & PWA Offline Sync
|--------------------------------------------------------------------------
*/

// Public Authentication Route (Rate limited to 15 attempts/minute to prevent brute-force attacks)
Route::middleware('throttle:15,1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Friendly informational route for GET /api/sync/push (prevents MethodNotAllowedHttpException)
Route::get('sync/push', function () {
    return response()->json([
        'success' => true,
        'status' => 'ready',
        'message' => 'Endpoint sinkronisasi offline SIMOLI siap digunakan. Gunakan metode HTTP POST berserta payload transaksi offline untuk melakukan sinkronisasi data.',
        'endpoint' => '/api/sync/push',
        'supported_methods' => ['POST'],
    ]);
});

// Protected API Routes (Requires valid Bearer Token or active Web Session, rate-limited to 120 req/min)
Route::middleware(['throttle:120,1', 'auth.api'])->group(function () {
    Route::get('user', [AuthController::class, 'user']);

    // PWA Offline-First Sync Routes
    Route::get('sync/pull', [SyncApiController::class, 'pull']);
    Route::post('sync/push', [SyncApiController::class, 'push']);
    Route::get('operator/check-today-input', [SyncApiController::class, 'checkTodayInput']);

    // Monitoring & Master Data Endpoints
    Route::get('dashboard/stats', [MonitoringApiController::class, 'dashboardStats']);
    Route::get('master-data', [MonitoringApiController::class, 'masterData']);

    Route::get('monitoring-alat-berat', [MonitoringApiController::class, 'monitoringAlatBeratList']);
    Route::get('monitoring-alat-berat/{id}', [MonitoringApiController::class, 'monitoringAlatBeratDetail']);
    Route::post('monitoring-alat-berat', [MonitoringApiController::class, 'storeMonitoringAlatBerat']);

    Route::get('pengaliran', [MonitoringApiController::class, 'pengaliranList']);
    Route::get('pemeliharaan', [MonitoringApiController::class, 'pemeliharaanList']);
});

/*
|--------------------------------------------------------------------------
| IoT Pengaliran API Routes (PKS Sei Galuh / MQTT Integration)
|--------------------------------------------------------------------------
*/
Route::prefix('iot')->middleware('throttle:180,1')->group(function () {
    // Health status check (Public)
    Route::get('status', [IotPengaliranApiController::class, 'status']);

    // Protected IoT Endpoints (Authentication & Authorization required)
    Route::middleware('auth.iot')->group(function () {
        Route::get('pengaliran/latest', [IotPengaliranApiController::class, 'latest']);
        Route::get('pengaliran', [IotPengaliranApiController::class, 'index']);
        Route::post('pengaliran', [IotPengaliranApiController::class, 'store']);
        Route::post('pengaliran/publish', [IotPengaliranApiController::class, 'publish']);
    });
});

// Aliases (Protected IoT)
Route::middleware(['throttle:180,1', 'auth.iot'])->group(function () {
    Route::get('pengaliran/iot/latest', [IotPengaliranApiController::class, 'latest']);
    Route::post('pengaliran/iot', [IotPengaliranApiController::class, 'store']);
});
