<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\WishlistApiController; // <-- Tambahkan ini

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
    // Autentikasi User
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Manajemen Produk (CRUD)
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::post('/products/{product}', [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);

    // Rute API untuk Keranjang Belanja
    Route::get('/cart', [CartApiController::class, 'index']);
    Route::post('/cart/add', [CartApiController::class, 'add']);
    Route::put('/cart/update/{cartItem}', [CartApiController::class, 'updateQuantity']);
    Route::delete('/cart/remove/{cartItem}', [CartApiController::class, 'remove']);
    Route::post('/cart/clear', [CartApiController::class, 'clear']);

    // Rute API untuk Pemesanan
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);

    // Rute API untuk Wishlist (Tambahkan ini)
    Route::get('/wishlist', [WishlistApiController::class, 'index']); // Mendapatkan isi wishlist
    Route::post('/wishlist/add', [WishlistApiController::class, 'add']); // Menambahkan produk ke wishlist
    Route::delete('/wishlist/remove/{wishlistItem}', [WishlistApiController::class, 'remove']); // Menghapus item dari wishlist
    Route::post('/wishlist/clear', [WishlistApiController::class, 'clear']); // Mengosongkan wishlist
});
