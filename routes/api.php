<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllers;
use App\Http\Controllers\Api\ApiControllers;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Login
Route::post('/login', [AuthControllers::class, 'login']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Logout
    Route::post('/logout', [AuthControllers::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | DATA UMUM
    |--------------------------------------------------------------------------
    */

    // Semua jadwal
    Route::get('/jadwal', [ApiControllers::class, 'jadwal']);

    // Sesi absen aktif (belum expired)
    Route::get('/sesi-absen/aktif', [ApiControllers::class, 'sesiAbsenAktif']);

    // Riwayat absensi murid sendiri
    Route::get('/absensi/murid', [ApiControllers::class, 'absensiMurid']);


    /*
    |--------------------------------------------------------------------------
    | ABSENSI
    |--------------------------------------------------------------------------
    */

    // Guru buka sesi
    Route::post('/jadwal/{jadwal}/buka-absen', [ApiControllers::class, 'bukaAbsen']);

    // Murid scan QR
    Route::post('/absensi/scan', [ApiControllers::class, 'scan']);

    // Manual QR (insert only)
    Route::post('/absensi/manual-qr', [ApiControllers::class, 'absenManualQR']);

    // Manual utama (insert / update massal)
    Route::post('/absensi/manual', [ApiControllers::class, 'absenManual']);
});