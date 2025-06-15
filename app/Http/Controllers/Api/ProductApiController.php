<?php
    
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage; // Untuk manajemen file

class ProductApiController extends Controller
{
    /**
     * Mengambil dan mengembalikan daftar semua produk dengan paginasi, pencarian, dan filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Mulai query Product dengan eager loading relasi kategori
        $query = Product::with('category');

        // Fitur Pencarian berdasarkan nama produk
        // Jika ada parameter 'search' di request dan tidak kosong
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter berdasarkan kategori (menggunakan nama kategori)
        // Jika ada parameter 'category' di request dan tidak kosong
        if ($request->has('category') && $request->category != '') {
            // Menggunakan whereHas untuk memfilter berdasarkan relasi
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category); // Memfilter berdasarkan nama kategori
            });
        }

        // Paginasi: Ambil sejumlah item per halaman
        // Default 10 item per halaman, bisa disesuaikan dengan parameter 'per_page' dari frontend
        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage);

        // Mengembalikan data produk dan informasi paginasi
        return response()->json([
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $products->items(), // Mengambil item data untuk halaman saat ini
            'pagination' => [
                'total' => $products->total(), // Total keseluruhan item
                'per_page' => $products->perPage(), // Jumlah item per halaman
                'current_page' => $products->currentPage(), // Halaman saat ini
                'last_page' => $products->lastPage(), // Halaman terakhir
                'from' => $products->firstItem(), // Indeks item pertama di halaman saat ini
                'to' => $products->lastItem(),     // Indeks item terakhir di halaman saat ini
            ]
        ], 200);
    }

    /**
     * Mengambil dan mengembalikan detail satu produk berdasarkan ID.
     *
     * @param  int  $id ID produk yang akan diambil.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Mencari produk berdasarkan ID, dengan memuat relasi kategori
        $product = Product::with('category')->find($id);

        // Jika produk tidak ditemukan, kembalikan respons 404
        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        // Mengembalikan data produk yang ditemukan
        return response()->json([
            'message' => 'Detail produk berhasil diambil.',
            'data' => $product
        ], 200);
    }

    /**
     * Menyimpan produk baru, termasuk upload gambar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input, termasuk file gambar
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'], // ID kategori harus ada di tabel categories
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // File harus gambar, maksimal 2MB
        ]);

        $imagePath = null;
        // Jika ada file gambar di request, simpan gambarnya ke storage
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-images', 'public'); // Disimpan di storage/app/public/product-images
        }

        // Membuat entri produk baru di database
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_path' => $imagePath, // Menyimpan path gambar di kolom database
        ]);

        // Memuat relasi kategori untuk memastikan data kategori ikut di respons
        $product->load('category');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ], 201); // Kode status HTTP 201 Created (untuk sumber daya baru)
    }

    /**
     * Memperbarui produk yang sudah ada, termasuk mengganti atau menghapus gambar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product Parameter route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        // Validasi input. 'sometimes' berarti field hanya divalidasi jika ada di request.
        $request->validate([
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // Validasi jika ada gambar baru
        ]);

        // Handle upload gambar baru atau penghapusan gambar
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('product-images', 'public');
            $product->image_path = $imagePath;
        } elseif ($request->has('image_remove') && $request->image_remove == 1) { // Jika ada flag untuk menghapus gambar
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
                $product->image_path = null; // Set path gambar di DB menjadi null
            }
        }

        // Perbarui data produk. 'except' digunakan untuk mengecualikan input yang bukan kolom database langsung.
        $product->update($request->except(['image', 'image_remove']));

        // Muat ulang relasi kategori untuk respons
        $product->load('category');

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product
        ], 200);
    }

    /**
     * Menghapus produk dari database dan storage terkait.
     *
     * @param  \App\Models\Product  $product Parameter route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        // Hapus gambar terkait dari storage jika ada
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Hapus entri produk dari database
        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.'
        ], 200);
    }
}
