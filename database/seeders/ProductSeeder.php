<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data produk ini diambil dari screenshot MySQL Workbench Anda
        // Pastikan category_id merujuk ke ID kategori yang ada di CategorySeeder Anda
        $products = [
            [
                'id' => 5,
                'category_id' => 5, // Perhatikan category_id ini
                'name' => 'Pintu Ukir Jati',
                'description' => 'Pintu dengan ukiran kayu jati yang tahan lama dan memberikan kesan klasik pada rumah Anda',
                'price' => 8750000.00,
                'stock' => 5,
                'image_path' => 'product-images/Pintu-Kayu-Jati-1.webp', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 6,
                'category_id' => 5, // Perhatikan category_id ini
                'name' => 'Pintu Gebyok Ukir',
                'description' => 'Pintu gebyok tradisional dengan ukiran Jawa klasik',
                'price' => 9800000.00,
                'stock' => 1,
                'image_path' => 'product-images/pintugebyok1.webp', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 7,
                'category_id' => 2, // Perhatikan category_id ini
                'name' => 'Lemari Hias Ukir',
                'description' => 'Lemari pajangan ukiran untuk koleksi hiasan ruang tamu Anda',
                'price' => 11750000.00,
                'stock' => 3,
                'image_path' => 'product-images/Lemari-hias-jati3.webp', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 8,
                'category_id' => 2, // Perhatikan category_id ini
                'name' => 'Lemari Ukir',
                'description' => 'Produk ukiran berkualitas tinggi, dibuat dari material pilihan dan dikerjakan oleh pengrajin profesional. Cocok untuk mempercantik ruangan Anda',
                'price' => 12500000.00,
                'stock' => 5,
                'image_path' => 'product-images/Lemari-hias-jati3.webp', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 9,
                'category_id' => 3, // Perhatikan category_id ini
                'name' => 'Set Kursi Tamu Ukir',
                'description' => 'Set kursi tamu dengan detail ukiran klasik dan bahan premium',
                'price' => 15900000.00,
                'stock' => 4,
                'image_path' => 'product-images/setkursitamu2.webp', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 10,
                'category_id' => 2, // Perhatikan category_id ini
                'name' => 'Lemari Pakaian Ukir',
                'description' => 'Lemari pakaian dua pintu dengan ukiran elegan untuk memperindah ruang tidur Anda.',
                'price' => 14250000.00,
                'stock' => 2,
                'image_path' => 'product-images/lemaripakaianukir1jpg.jpg', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 11,
                'category_id' => 2, // Perhatikan category_id ini
                'name' => 'Kursi Ukir',
                'description' => 'Kursi ukiran kayu jati dengan desain simpel namun elegan. Cocok untuk meja makan atau sudut baca',
                'price' => 7250000.00,
                'stock' => 2,
                'image_path' => 'product-images/kursiukir1.png', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 12,
                'category_id' => 4, // Perhatikan category_id ini
                'name' => 'Set Meja Makan Ukir',
                'description' => 'Meja makan kayu jati dengan ukiran klasik dan kokoh untuk keluarga besar',
                'price' => 15500000.00,
                'stock' => 3,
                'image_path' => 'product-images/setmejamakan1.jpg', // Ganti dengan nama file gambar yang sebenarnya
            ],
            [
                'id' => 13,
                'category_id' => 6, // Perhatikan category_id ini
                'name' => 'Tiang Ukir Naga & Hong',
                'description' => 'Tiang dekoratif berukir khas klasik untuk pilar rumah atau gazebo',
                'price' => 7750000.00,
                'stock' => 12,
                'image_path' => 'product-images/tiangukir1.jpg', // Ganti dengan nama file gambar yang sebenarnya
            ],
        ];

        // Masukkan data ke tabel products
        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(['id' => $product['id']], $product);
        }
    }
}
