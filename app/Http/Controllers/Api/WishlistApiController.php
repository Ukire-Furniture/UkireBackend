<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WishlistApiController extends Controller
{
    /**
     * Mengambil dan mengembalikan isi daftar keinginan user yang sedang login.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        // Temukan wishlist user, atau buat jika belum ada
        $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);

        // Muat item-item wishlist beserta detail produknya
        $wishlist->load(['items.product.category']);

        // Format data untuk respons
        $formattedItems = $wishlist->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->product->price,
                'image_path' => $item->product->image_path,
                'stock' => $item->product->stock,
                'category' => $item->product->category ? $item->product->category->name : 'N/A',
            ];
        });

        return response()->json([
            'message' => 'Isi wishlist berhasil diambil.',
            'data' => $formattedItems,
            'total_items' => $formattedItems->count(),
        ], 200);
    }

    /**
     * Menambahkan produk ke wishlist.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user = $request->user();
        $product = Product::find($request->product_id);

        if (!$product) {
            throw ValidationException::withMessages(['product_id' => 'Produk tidak ditemukan.']);
        }

        $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);

        // Cek apakah produk sudah ada di wishlist
        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)
                                    ->where('product_id', $request->product_id)
                                    ->first();

        if ($wishlistItem) {
            // Jika sudah ada, tidak perlu menambahkan lagi, bisa langsung merespons sukses
            return response()->json([
                'message' => 'Produk sudah ada di wishlist.',
                'data' => $wishlistItem->load('product'),
            ], 200);
        } else {
            // Jika belum ada, tambahkan item wishlist baru
            $wishlistItem = WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id,
            ]);
            $wishlistItem->load('product');
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke wishlist.',
                'data' => $wishlistItem,
            ], 201); // 201 Created
        }
    }

    /**
     * Menghapus item dari wishlist.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $wishlistItemId ID item wishlist yang akan dihapus
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, int $wishlistItemId): JsonResponse
    {
        $user = $request->user();
        $wishlist = Wishlist::where('user_id', $user->id)->firstOrFail();

        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)
                                    ->where('id', $wishlistItemId)
                                    ->firstOrFail();

        $wishlistItem->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus dari wishlist.'
        ], 200);
    }

    /**
     * Mengosongkan seluruh wishlist user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $wishlist = Wishlist::where('user_id', $user->id)->firstOrFail();
        
        $wishlist->items()->delete(); // Hapus semua item di wishlist

        return response()->json([
            'message' => 'Wishlist berhasil dikosongkan.'
        ], 200);
    }
}
