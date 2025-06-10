<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category; // impor model Category
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // impor jsonresponse buat response json yang jelas

class CategoryApiController extends Controller
{
    /**
     * mengambil dan mengembalikan daftar semua kategori
     * endpoint ini akan mengembalikan daftar kategori 
     * 
    * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // mengambil semua data kategori dari tabel 'categories'
        $categories = Category::all();

        // megembalikan data kategori dalam format json
        // array 'data' berisi daftar kategori
        // status http 200 menandakan permintaan berhasil
        return response()->json([
            'message' => 'Daftar Kategori',
            'data' => $categories,
        ], 200);
    }

    /**
     * buat fitur di masa deoan, kayak buat kategori baru, update, delete
     * edit atau hapus, nah bisa nambahin method lain di sini
     * contoh:
     * public function store(Request $request): JsonResponse { ... }
     * public function show($Category $category): JsonResponse { ... }
     */
}
