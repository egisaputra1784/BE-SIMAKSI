<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllers;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthControllers::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthControllers::class, 'logout']);
});
