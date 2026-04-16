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

    // manual massal (guru input status)
    Route::post('/absensi/manual', [ApiControllers::class, 'absenManual']);

    // murid dalam sesi
    Route::get('/sesi/{id}/murid', [ApiControllers::class, 'getMuridSesi']);

    // close sesi (WAJIB ADA karena ada function di controller)
    Route::post('/sesi/{id}/close', [ApiControllers::class, 'closeSesi']);


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
| JADWAL
|--------------------------------------------------------------------------
*/

    // jadwal guru hari ini
    Route::get('/jadwal/guru/hari-ini', [ApiControllers::class, 'jadwalHariIni']);

    // jadwal murid hari ini
    Route::get('/jadwal/murid/hari-ini', [ApiControllers::class, 'jadwalMuridHariIni']);

    Route::get('/murid/jadwal-minggu', [ApiControllers::class, 'jadwalMingguMurid']);


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


    /*
|--------------------------------------------------------------------------
| MARKETPLACE
|--------------------------------------------------------------------------
*/

    Route::get('/marketplace/items', [ApiControllers::class, 'getItems']);
    Route::post('/marketplace/buy', [ApiControllers::class, 'buyToken']);
    Route::get('/marketplace/tokens', [ApiControllers::class, 'myTokens']);

    // POINT SYSTEM
    Route::get('/point/me', [ApiControllers::class, 'myPoint']);
    Route::get('/point/history', [ApiControllers::class, 'pointHistory']);
    Route::get('/leaderboard', [ApiControllers::class, 'leaderboard']);
});
