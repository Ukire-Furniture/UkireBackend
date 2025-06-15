<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart; // Model untuk keranjang belanja
use App\Models\Order; // Model untuk pesanan
use App\Models\OrderItem; // Model untuk item pesanan
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang sedang login
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Validation\ValidationException; // Untuk validasi kustom

class OrderApiController extends Controller
{
    /**
     * Membuat pesanan baru dari keranjang belanja user yang sedang login.
     * Menggunakan transaksi database untuk memastikan atomicity (semua berhasil atau semua gagal).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input data pengiriman dari frontend
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_province' => ['required', 'string', 'max:255'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            // 'shipping_method' => ['required', 'string'], // Bisa ditambahkan jika ingin menyimpan metode pengiriman spesifik
        ]);

        $user = $request->user(); // Dapatkan user yang sedang login
        // Ambil keranjang user beserta item dan detail produknya
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        // Validasi: Pastikan keranjang tidak kosong
        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Keranjang belanja Anda kosong. Silakan belanja terlebih dahulu.']);
        }

        // Mulai transaksi database untuk memastikan integritas data
        DB::beginTransaction();

        try {
            $totalAmount = 0; // Inisialisasi total jumlah pesanan
            $orderItemsData = []; // Array untuk menyimpan data item pesanan

            // Iterasi setiap item di keranjang untuk menghitung total dan validasi stok
            foreach ($cart->items as $item) {
                $product = $item->product; // Dapatkan detail produk dari item keranjang

                // Validasi: Pastikan produk valid dan stok mencukupi
                if (!$product || $product->stock < $item->quantity) {
                    DB::rollBack(); // Batalkan transaksi jika stok tidak cukup
                    throw ValidationException::withMessages(['quantity' => "Stok untuk produk '{$product->name}' tidak mencukupi atau produk tidak valid."]);
                }

                // Hitung harga total untuk item ini dan tambahkan ke total pesanan
                $itemPrice = $product->price * $item->quantity;
                $totalAmount += $itemPrice;

                // Siapkan data untuk OrderItem
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price_at_order' => $product->price, // Simpan harga produk saat dipesan
                    'created_at' => now(), // Timestamp otomatis
                    'updated_at' => now(), // Timestamp otomatis
                ];

                // Kurangi stok produk dari tabel products
                $product->stock -= $item->quantity;
                $product->save(); // Simpan perubahan stok
            }

            // Buat entri pesanan utama di tabel orders
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending', // Status awal pesanan (bisa diupdate setelah pembayaran)
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_province' => $request->shipping_province,
                'shipping_postal_code' => $request->shipping_postal_code,
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
            ]);

            // Tambahkan semua item pesanan ke pesanan yang baru dibuat
            $order->items()->createMany($orderItemsData);

            // Kosongkan keranjang belanja user setelah pesanan berhasil dibuat
            $cart->items()->delete(); // Hapus semua item dari tabel cart_items
            $cart->delete(); // Hapus entri keranjang dari tabel carts

            DB::commit(); // Komit (terapkan) semua perubahan ke database

            // Muat detail pesanan beserta item dan produknya untuk respons
            return response()->json([
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order->load('items.product.category'), // Sertakan kategori produk juga
            ], 201); // Kode status HTTP 201 Created
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi kesalahan apa pun
            // Re-throw exception untuk ditangani oleh Exception Handler Laravel atau ditampilkan di frontend
            throw $e; 
        }
    }

    /**
     * Mengambil daftar pesanan user yang sedang login.
     * Termasuk paginasi dan detail item pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user(); // Dapatkan user yang sedang login
        // Ambil pesanan user, muat item dan produknya, urutkan berdasarkan tanggal terbaru
        $orders = $user->orders()->with('items.product')->orderBy('created_at', 'desc')->paginate(10);

        // Mengembalikan daftar pesanan beserta informasi paginasi
        return response()->json([
            'message' => 'Daftar pesanan berhasil diambil.',
            'data' => $orders->items(), // Item data untuk halaman saat ini
            'pagination' => [
                'total' => $orders->total(), // Total keseluruhan pesanan
                'per_page' => $orders->perPage(), // Jumlah item per halaman
                'current_page' => $orders->currentPage(), // Halaman saat ini
                'last_page' => $orders->lastPage(), // Halaman terakhir
                'from' => $orders->firstItem(), // Indeks item pertama di halaman saat ini
                'to' => $orders->lastItem(),     // Indeks item terakhir di halaman saat ini
            ]
        ], 200);
    }

    /**
     * Mengambil detail satu pesanan user yang sedang login.
     * Menggunakan Route Model Binding untuk Order.
     *
     * @param  \App\Models\Order  $order Model Order yang otomatis di-resolve dari ID di route
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        // Pastikan pesanan ini milik user yang sedang login untuk alasan keamanan
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized. Anda tidak memiliki akses ke pesanan ini.'], 403);
        }

        // Muat detail item pesanan beserta produk dan kategorinya
        $order->load('items.product.category');

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil.',
            'data' => $order
        ], 200);
    }

    // Metode lain seperti update status pesanan (oleh admin) atau pembatalan pesanan (oleh user)
    // bisa ditambahkan di sini di masa mendatang.
}
