<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderApiController extends Controller
{
    /**
     * Membuat pesanan baru dari keranjang belanja user yang sedang login.
     * Menggunakan transaksi database untuk memastikan atomicity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_province' => ['required', 'string', 'max:255'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Keranjang belanja Anda kosong. Silakan belanja terlebih dahulu.']);
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($cart->items as $item) {
                $product = $item->product;

                if (!$product || $product->stock < $item->quantity) {
                    DB::rollBack();
                    throw ValidationException::withMessages(['quantity' => "Stok untuk produk '{$product->name}' tidak mencukupi atau produk tidak valid."]);
                }

                $itemPrice = $product->price * $item->quantity;
                $totalAmount += $itemPrice;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price_at_order' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $product->stock -= $item->quantity;
                $product->save();
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_province' => $request->shipping_province,
                'shipping_postal_code' => $request->shipping_postal_code,
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
            ]);

            $order->items()->createMany($orderItemsData);

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order->load('items.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengambil daftar pesanan user yang sedang login.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = $user->orders()->with('items.product')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'message' => 'Daftar pesanan berhasil diambil.',
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ], 200);
    }

    /**
     * Mengambil detail satu pesanan user yang sedang login.
     * @param  \App\Models\Order  $order Parameter route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $order->load('items.product.category');

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil.',
            'data' => $order
        ], 200);
    }

    /**
     * Memperbarui status pesanan.
     * Hanya admin atau sistem yang seharusnya bisa memanggil ini.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,shipped,completed,cancelled'],
        ]);

        // Otorisasi: Pastikan hanya admin atau user yang berwenang yang bisa mengubah status
        // Untuk tujuan ini, kita asumsikan hanya admin yang bisa (atau sistem pembayaran)
        // Jika diakses dari frontend user biasa, ini harus dilindungi lebih lanjut
        // Contoh: if (Auth::user()->role !== 'admin') { return response()->json(['message' => 'Forbidden.'], 403); }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => "Status pesanan {$order->id} berhasil diperbarui menjadi {$order->status}.",
            'data' => $order,
        ], 200);
    }
}
