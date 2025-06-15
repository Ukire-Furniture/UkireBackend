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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang membuat pesanan
            $table->decimal('total_amount', 12, 2); // Total jumlah pesanan
            $table->string('status')->default('pending'); // Status pesanan: pending, processing, completed, cancelled, etc.
            // Informasi pengiriman (bisa dinormalisasi ke tabel terpisah jika lebih kompleks)
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code');
            $table->string('phone_number')->nullable();
            $table->text('notes')->nullable(); // Catatan tambahan dari user
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
