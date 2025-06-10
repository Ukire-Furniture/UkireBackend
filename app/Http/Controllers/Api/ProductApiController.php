<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; // impor model Product
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // impor JsonResponse untuk response JSON yang jelas

class ProductApiController extends Controller
{
    /**
     * mengambil dan mengembalikan daftar semua produk
     * relasi 'category' dimuat supaya informasi kategori juga ikut terambil
     * endpoint ini bakal diakses sama frontend buat dapetin daftar produk
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // ngambil semua data produk dari tabel 'products'
        // 'with('category')' mastiin data kategori terkait juga diambil dalam satu query
        $products = Product::with('category')->get();

        // ngembaliin data produk dalam format JSON
        // status HTTP 200 nunjukin permintaan berhasil
        return response()->json([
            'message' => 'Daftar produk Berhasil Diambil',
            'data' => $products,
        ], 200);
    }

    /**
     * ngambil dan ngembaliin detail satu produk berdasakan ID
     * jika produk gak ditemukan, bakal ngembaliin error 404
     * 
     * @param int $id ID produk yang dicari
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // nyoba ngambil produk berdasarkan ID
        $product = Product::with('category')->find($id);

        // meriksa apakah data produk ditemuin
        // kalo produk gak ditemuin, ngembaliin error 404
        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // ngembaliin data produk dalam format JSON
        return response()->json([
            'message' => 'Detail Produk berhasil diambil',
            'data' => $product,
        ], 200);
    }

    //buat fitur di masa deoan, kayak buat produk baru
    // edit atau hapus, nah bisa nambahin method lain di sini
}
