<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllers;
use App\Http\Controllers\Api\ApiControllers;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthControllers::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthControllers::class, 'logout']);

    //================== GET DATA ================
    
    Route::get('/jadwal', [ApiControllers::class,'jadwal']);

    // GET sesi absen aktif (QR belum expired)
    Route::get('/sesi-absen/aktif', [ApiControllers::class,'sesiAbsenAktif']);

    // GET absensi murid sendiri
    Route::get('/absensi/murid', [ApiControllers::class,'absensiMurid']);
    // ================= ABSENSI =================
    
    // Buka sesi absen (guru)
    Route::post('/jadwal/{jadwal}/buka-absen', [ApiControllers::class, 'bukaAbsen']);

    // Scan QR (murid)
    Route::post('/absensi/scan', [ApiControllers::class, 'scan']);
});
