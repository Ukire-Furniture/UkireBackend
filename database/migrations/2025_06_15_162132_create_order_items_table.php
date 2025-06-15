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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel products
            $table->integer('quantity');
            $table->decimal('price_at_order', 12, 2); // Harga produk saat dipesan (penting jika harga berubah)
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
