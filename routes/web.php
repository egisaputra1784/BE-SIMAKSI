<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/admin', function () {
    return view('admin.index');
});


Route::get('/admin/form', function () {
    return view('admin.form');
});


Route::get('/guru', function () {
    return view('guru.index');
});

Route::get('/guru/form', function () {
    return view('guru.form');
});

