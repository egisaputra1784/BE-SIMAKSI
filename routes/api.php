<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllers;
use App\Http\Controllers\Api\ApiControllers;
use App\Http\Controllers\Api\RekapController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthControllers::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED (JWT)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthControllers::class, 'logout']);
    Route::post('/change-password', [AuthControllers::class, 'changePassword']);

    /*
    |--------------------------------------------------------------------------
    | ABSENSI
    |--------------------------------------------------------------------------
    */

    // buka sesi otomatis berdasarkan jam
    Route::post('/buka-absen', [ApiControllers::class, 'bukaAbsen']);

    // scan qr
    Route::post('/absensi/scan', [ApiControllers::class, 'scan']);

    // manual satu murid
    Route::post('/absensi/manual-qr', [ApiControllers::class, 'absenManualQR']);

    // manual massal
    Route::post('/absensi/manual', [ApiControllers::class, 'absenManual']);

    // murid dalam sesi
    Route::get('/sesi/{id}/murid', [ApiControllers::class, 'getMuridSesi']);


    /*
    |--------------------------------------------------------------------------
    | REKAP
    |--------------------------------------------------------------------------
    */

    Route::get('/rekap/siswa', [RekapController::class, 'siswaRekap']);


    /*
    |--------------------------------------------------------------------------
    | DATA UMUM
    |--------------------------------------------------------------------------
    */

    Route::get('/tahun-ajar', [ApiControllers::class, 'tahunAjar']);

    Route::get('/kelas', [ApiControllers::class, 'kelas']);


    /*
    |--------------------------------------------------------------------------
    | PENILAIAN SISWA
    |--------------------------------------------------------------------------
    */

    // kategori penilaian
    Route::get('/assessment/categories', [ApiControllers::class, 'assessmentCategories']);

    // daftar murid dalam kelas
    Route::get('/kelas/{kelasId}/murid', [ApiControllers::class, 'muridKelas']);

    // simpan penilaian
    Route::post('/assessment/simpan', [ApiControllers::class, 'simpanAssessment']);

    // lihat nilai murid
    Route::get('/assessment/murid/{muridId}', [ApiControllers::class, 'nilaiMurid']);
});
