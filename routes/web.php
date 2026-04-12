<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnggotaKelasController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\TahunAjarController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/admin/form', function () {
    return view('admin.form');
});

Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin', [AdminController::class, 'store']);
Route::get('/admin/data', [AdminController::class, 'data']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);


Route::get('/guru', [GuruController::class, 'index']);
Route::get('/guru/data', [GuruController::class, 'data']);
Route::post('/guru', [GuruController::class, 'store']);

Route::get('/guru/form', function () {
    return view('guru.form');
});

Route::get('/guru/{id}', [GuruController::class, 'show']);
Route::put('/guru/{id}', [GuruController::class, 'update']);
Route::delete('/guru/{id}', [GuruController::class, 'destroy']);



Route::get('/murid', [MuridController::class, 'index']);
Route::get('/murid/data', [MuridController::class, 'data']);
Route::post('/murid', [MuridController::class, 'store']);

Route::get('/murid/form', function () {
    return view('murid.form');
});

Route::get('/murid/{id}', [MuridController::class, 'show']);
Route::put('/murid/{id}', [MuridController::class, 'update']);
Route::delete('/murid/{id}', [MuridController::class, 'destroy']);


Route::get('/tahun-ajar', [TahunAjarController::class, 'index']);
Route::get('/tahun-ajar/data', [TahunAjarController::class, 'data']);
Route::post('/tahun-ajar', [TahunAjarController::class, 'store']);

Route::get('/tahun-ajar/form', function () {
    return view('tahun-ajar.form');
});

Route::get('/tahun-ajar/{id}', [TahunAjarController::class, 'show']);
Route::put('/tahun-ajar/{id}', [TahunAjarController::class, 'update']);
Route::delete('/tahun-ajar/{id}', [TahunAjarController::class, 'destroy']);

Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/data', [KelasController::class, 'data']);
Route::post('/kelas', [KelasController::class, 'store']);

Route::get('/kelas/form', function () {
    return view('kelas.form');
});

Route::get('/kelas/{id}', [KelasController::class, 'show']);
Route::put('/kelas/{id}', [KelasController::class, 'update']);
Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index']);
Route::get('/anggota-kelas/data', [AnggotaKelasController::class, 'data']);
Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store']);

Route::get('/anggota-kelas/form', function () {
    return view('anggota-kelas.form');
});

Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy']);
