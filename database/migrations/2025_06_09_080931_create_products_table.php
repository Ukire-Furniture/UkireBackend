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
        // Membuat tabel 'products'
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis (primary key)
            // Kolom category_id sebagai foreign key yang merujuk ke tabel 'categories'
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama produk
            $table->longText('description')->nullable(); // Deskripsi produk, bisa kosong
            $table->decimal('price', 10, 2); // Harga produk (misal: 10 digit total, 2 di belakang koma)
            $table->integer('stock'); // Jumlah stok produk
            $table->string('image_path')->nullable(); // Path gambar produk, bisa kosong
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Menghapus tabel 'products' jika migrasi dibalikkan
        Schema::dropIfExists('products');
    }
};
