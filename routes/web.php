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

