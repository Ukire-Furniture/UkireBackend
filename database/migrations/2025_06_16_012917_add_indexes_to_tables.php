<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Menambahkan indeks ke tabel 'products'
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id'); // Indeks untuk filter/join berdasarkan kategori
            $table->index('name');        // Indeks untuk pencarian berdasarkan nama produk
        });

        // Menambahkan indeks ke tabel 'orders'
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');    // Indeks untuk mencari pesanan berdasarkan user
            $table->index('status');     // Indeks untuk filter status pesanan
            $table->index('created_at'); // Indeks untuk pengurutan pesanan terbaru
        });

        // Menambahkan indeks ke tabel 'cart_items'
        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('cart_id');
            $table->index('product_id');
        });

        // Menambahkan indeks ke tabel 'order_items'
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        // Menambahkan indeks ke tabel 'wishlists'
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('user_id'); // Indeks untuk mencari wishlist berdasarkan user
        });

        // Menambahkan indeks ke tabel 'wishlist_items'
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->index('wishlist_id');
            $table->index('product_id');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Menghapus indeks jika migrasi dibalikkan
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex(['wishlist_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
