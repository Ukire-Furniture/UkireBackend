<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini tempat kamu bisa mendaftarkan rute API untuk aplikasimu.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang berisi
| middleware "api". Nikmati membangun API kamu!
|
*/

// Rute Autentikasi (Publik)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Publik untuk Produk dan Kategori
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);

// Rute yang Dilindungi oleh Sanctum (Membutuhkan token autentikasi)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute API untuk Manajemen Produk
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::post('/products/{product}', [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);
});
