<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController; // Impor ProductApiController
use App\Http\Controllers\Api\CategoryApiController; // Impor CategoryApiController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini tempat yang bisa mendaftarkan rute API untuk aplikasi.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup yang berisi
| middleware "api". 
|
*/

// rute api buat kategori produk
// metode get untuk mengambil daftar kategori
Route::get('/categories', [CategoryApiController::class, 'index']);

// rute api buat produk
// metode get buat ngambil semua produk
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);
    

// catetan: secara dafault, rute di 'api.php' udah memiliki prefix '/api/'.
// jadi, rute '/categories' bakal bisa diakses di '/api/categories'

// untuk rute yang lebih lengkap (post, put, delete, dll),
// di masa depan bisa menggunakan Route::apiResource()
// contoh: Route::apiResource('products', ProductApiController::class);

    