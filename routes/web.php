<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnggotaKelasController;
use App\Http\Controllers\FlexibilityItemController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruMapelController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\PointRuleController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\TahunAjarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekapAbsenController;

/*
| AUTH
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
| PROTECTED
*/
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/guru/data', [GuruController::class, 'data']);
    Route::get('/admin/data', [AdminController::class, 'data']);
    Route::get('/murid/data', [MuridController::class, 'data']);

    /*
    | SUPERADMIN ONLY
    */
    Route::group([
        'middleware' => function ($request, $next) {
            if (auth('web')->user()->role !== 'superadmin')
                abort(403);
            return $next($request);
        }
    ], function () {

        // ADMIN
        Route::get('/admin', [AdminController::class, 'index']);
        Route::get('/admin/form', fn() => view('admin.form'));

        Route::post('/admin', [AdminController::class, 'store']);
        Route::get('/admin/{id}', [AdminController::class, 'show']);
        Route::put('/admin/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/{id}', [AdminController::class, 'destroy']);

        // GURU
        Route::get('/guru', [GuruController::class, 'index']);

        Route::get('/guru/form', fn() => view('guru.form'));
        Route::post('/guru', [GuruController::class, 'store']);
        Route::get('/guru/{id}', [GuruController::class, 'show']);
        Route::put('/guru/{id}', [GuruController::class, 'update']);
        Route::delete('/guru/{id}', [GuruController::class, 'destroy']);
        Route::get('/guru/export/excel', [GuruController::class, 'exportExcel']);

        // MURID
        Route::get('/murid', [MuridController::class, 'index']);

        Route::get('/murid/form', fn() => view('murid.form'));
        Route::post('/murid', [MuridController::class, 'store']);
        Route::get('/murid/{id}', [MuridController::class, 'show']);
        Route::put('/murid/{id}', [MuridController::class, 'update']);
        Route::delete('/murid/{id}', [MuridController::class, 'destroy']);
        Route::get('/murid/export/excel', [MuridController::class, 'exportExcel']);
    });

    /*
    | ADMIN + SUPERADMIN
    */
    Route::group([
        'middleware' => function ($request, $next) {
            if (!in_array(auth('web')->user()->role, ['admin', 'superadmin']))
                abort(403);
            return $next($request);
        }
    ], function () {

        // Tahun Ajar
        Route::get('/tahun-ajar', [TahunAjarController::class, 'index']);
        Route::get('/tahun-ajar/data', [TahunAjarController::class, 'data']);
        Route::get('/tahun-ajar/form', fn() => view('tahun-ajar.form'));
        Route::post('/tahun-ajar', [TahunAjarController::class, 'store']);
        Route::get('/tahun-ajar/{id}', [TahunAjarController::class, 'show']);
        Route::put('/tahun-ajar/{id}', [TahunAjarController::class, 'update']);
        Route::delete('/tahun-ajar/{id}', [TahunAjarController::class, 'destroy']);
        Route::get('/tahun-ajar/export/excel', [TahunAjarController::class, 'exportExcel']);

        // Kelas
        Route::get('/kelas', [KelasController::class, 'index']);
        Route::get('/kelas/data', [KelasController::class, 'data']);
        Route::get('/kelas/form', fn() => view('kelas.form'));
        Route::post('/kelas', [KelasController::class, 'store']);
        Route::get('/kelas/{id}', [KelasController::class, 'show']);
        Route::put('/kelas/{id}', [KelasController::class, 'update']);
        Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);
        Route::get('/kelas/export/excel', [KelasController::class, 'exportExcel']);

        // Anggota Kelas
        Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index']);
        Route::get('/anggota-kelas/data', [AnggotaKelasController::class, 'data']);
        Route::get('/anggota-kelas/form', fn() => view('anggota-kelas.form'));
        Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store']);
        Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy']);
        Route::get('/anggota-kelas/export/excel', [AnggotaKelasController::class, 'exportExcel']);

        // Mapel
        Route::get('/mapel', [MapelController::class, 'index']);
        Route::get('/mapel/data', [MapelController::class, 'data']);
        Route::get('/mapel/form', fn() => view('mapel.form'));
        Route::post('/mapel', [MapelController::class, 'store']);
        Route::get('/mapel/{id}', [MapelController::class, 'show']);
        Route::put('/mapel/{id}', [MapelController::class, 'update']);
        Route::delete('/mapel/{id}', [MapelController::class, 'destroy']);
        Route::get('/mapel/export/excel', [MapelController::class, 'exportExcel']);

        // Guru Mapel
        Route::get('/guru-mapel', [GuruMapelController::class, 'index']);
        Route::get('/guru-mapel/data', [GuruMapelController::class, 'data']);
        Route::get('/guru-mapel/form', fn() => view('guru-mapel.form'));
        Route::post('/guru-mapel', [GuruMapelController::class, 'store']);
        Route::get('/guru-mapel/{id}', [GuruMapelController::class, 'show']);
        Route::put('/guru-mapel/{id}', [GuruMapelController::class, 'update']);
        Route::delete('/guru-mapel/{id}', [GuruMapelController::class, 'destroy']);
        Route::get('/guru-mapel/export/excel', [GuruMapelController::class, 'exportExcel']);

        // Jadwal
        Route::get('/jadwal', [JadwalController::class, 'index']);
        Route::get('/jadwal/data', [JadwalController::class, 'data']);
        Route::get('/jadwal/form', fn() => view('jadwal.form'));
        Route::post('/jadwal', [JadwalController::class, 'store']);
        Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
        Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);
        Route::get('/jadwal/export/excel', [JadwalController::class, 'exportExcel']);

        // Point Rules
        Route::get('/point-rules', [PointRuleController::class, 'index']);
        Route::get('/point-rules/data', [PointRuleController::class, 'data']);
        Route::get('/point-rules/form', fn() => view('point-rules.form'));
        Route::post('/point-rules', [PointRuleController::class, 'store']);
        Route::get('/point-rules/{id}', [PointRuleController::class, 'show']);
        Route::put('/point-rules/{id}', [PointRuleController::class, 'update']);
        Route::delete('/point-rules/{id}', [PointRuleController::class, 'destroy']);
        Route::get('/point-rules/export/excel', [PointRuleController::class, 'exportExcel']);

        // Flexibility
        Route::get('/flexibility-items', [FlexibilityItemController::class, 'index']);
        Route::get('/flexibility-items/data', [FlexibilityItemController::class, 'data']);
        Route::get('/flexibility-items/form', fn() => view('flexibility-items.form'));
        Route::post('/flexibility-items', [FlexibilityItemController::class, 'store']);
        Route::get('/flexibility-items/{id}', [FlexibilityItemController::class, 'show']);
        Route::put('/flexibility-items/{id}', [FlexibilityItemController::class, 'update']);
        Route::delete('/flexibility-items/{id}', [FlexibilityItemController::class, 'destroy']);
        Route::get('/flexibility-items/export/excel', [FlexibilityItemController::class, 'exportExcel']);

        // Assessment
        Route::get('/assessment-categories', [AssessmentCategoryController::class, 'index']);
        Route::get('/assessment-categories/data', [AssessmentCategoryController::class, 'data']);
        Route::get('/assessment-categories/form', fn() => view('assessment-categories.form'));
        Route::post('/assessment-categories', [AssessmentCategoryController::class, 'store']);
        Route::get('/assessment-categories/{id}', [AssessmentCategoryController::class, 'show']);
        Route::put('/assessment-categories/{id}', [AssessmentCategoryController::class, 'update']);
        Route::delete('/assessment-categories/{id}', [AssessmentCategoryController::class, 'destroy']);
        Route::get('/assessment-categories/export/excel', [AssessmentCategoryController::class, 'exportExcel']);

        // Rekap
        Route::get('/rekap-absen', [RekapAbsenController::class, 'index']);
        Route::get('/rekap-absen/data', [RekapAbsenController::class, 'data']);
        Route::get('/rekap-absen/summary', [RekapAbsenController::class, 'summary']);
        Route::get('/rekap-absen/export', [RekapAbsenController::class, 'exportExcel']);
    });

});
