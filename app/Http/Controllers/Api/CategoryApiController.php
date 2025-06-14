<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => $categories
        ], 200);
    }
}
