<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartApiController extends Controller
{
    /**
     * Mengambil dan mengembalikan isi keranjang belanja user yang sedang login.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        // Temukan keranjang user, atau buat jika belum ada
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Muat item-item keranjang beserta detail produknya
        $cart->load(['items.product.category']);

        // Format data untuk respons (opsional, untuk menyederhanakan frontend)
        $formattedItems = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'image_path' => $item->product->image_path,
                'total_item_price' => $item->product->price * $item->quantity,
                'category' => $item->product->category ? $item->product->category->name : 'N/A',
            ];
        });

        return response()->json([
            'message' => 'Isi keranjang berhasil diambil.',
            'data' => $formattedItems,
            'cart_total' => $formattedItems->sum('total_item_price'),
        ], 200);
    }

    /**
     * Menambahkan produk ke keranjang belanja atau memperbarui kuantitasnya.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $product = Product::find($request->product_id);

        if (!$product) {
            throw ValidationException::withMessages(['product_id' => 'Produk tidak ditemukan.']);
        }

        if ($product->stock < $request->quantity) {
            throw ValidationException::withMessages(['quantity' => 'Stok produk tidak mencukupi.']);
        }

        // Temukan keranjang user, atau buat jika belum ada
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Cek apakah produk sudah ada di keranjang
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            // Jika sudah ada, tambahkan kuantitas
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                throw ValidationException::withMessages(['quantity' => 'Penambahan kuantitas melebihi stok tersedia.']);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Jika belum ada, buat item keranjang baru
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartItem->load('product'); // Muat detail produk untuk respons

        return response()->json([
            'message' => 'Produk berhasil ditambahkan/diperbarui di keranjang.',
            'data' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'name' => $cartItem->product->name,
                'price' => $cartItem->product->price,
                'quantity' => $cartItem->quantity,
                'image_path' => $cartItem->product->image_path,
                'total_item_price' => $cartItem->product->price * $cartItem->quantity,
            ]
        ], 200);
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cartItemId ID item keranjang yang akan diperbarui
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateQuantity(Request $request, int $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0'], // Kuantitas bisa 0 untuk menghapus item
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail(); // Pastikan keranjang milik user

        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('id', $cartItemId)
                            ->firstOrFail(); // Pastikan item keranjang ada di keranjang user

        if ($request->quantity === 0) {
            $cartItem->delete();
            return response()->json([
                'message' => 'Item berhasil dihapus dari keranjang.'
            ], 200);
        }

        $product = Product::find($cartItem->product_id);
        if ($product->stock < $request->quantity) {
            throw ValidationException::withMessages(['quantity' => 'Kuantitas melebihi stok tersedia.']);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $cartItem->load('product');

        return response()->json([
            'message' => 'Kuantitas item keranjang berhasil diperbarui.',
            'data' => [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'name' => $cartItem->product->name,
                'price' => $cartItem->product->price,
                'quantity' => $cartItem->quantity,
                'image_path' => $cartItem->product->image_path,
                'total_item_price' => $cartItem->product->price * $cartItem->quantity,
            ]
        ], 200);
    }

    /**
     * Menghapus item dari keranjang belanja.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cartItemId ID item keranjang yang akan dihapus
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, int $cartItemId): JsonResponse
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('id', $cartItemId)
                            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus dari keranjang.'
        ], 200);
    }

    /**
     * Mengosongkan seluruh keranjang belanja user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        
        $cart->items()->delete(); // Hapus semua item di keranjang

        return response()->json([
            'message' => 'Keranjang berhasil dikosongkan.'
        ], 200);
    }
}
