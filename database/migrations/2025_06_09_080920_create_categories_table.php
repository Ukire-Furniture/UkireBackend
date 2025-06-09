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
        // Membuat tabel 'categories'
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis (primary key)
            $table->string('name')->unique(); // Nama kategori, harus unik
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Menghapus tabel 'categories' jika migrasi dibalikkan
        Schema::dropIfExists('categories');
    }
};
