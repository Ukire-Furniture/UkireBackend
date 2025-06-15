<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Impor DB facade

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data kategori yang sudah ada atau yang ingin Anda backup
        $categories = [
            // Contoh data: Sesuaikan dengan ID dan nama kategori yang ada di DB Anda
            // Jika ID-nya 1, 2, 3, 4 seperti di screenshot Anda
            ['id' => 2, 'name' => 'Lemari'],
            ['id' => 3, 'name' => 'Kursi'],
            ['id' => 4, 'name' => 'Meja'],
            ['id' => 5, 'name' => 'Pintu'],
            ['id' => 6, 'name' => 'Pilar'],
            // Tambahkan kategori lain jika ada, dengan ID yang sesuai
            // ['id' => 5, 'name' => 'Tiang Pilar'], // Jika Anda punya ini sebelumnya
        ];

        // Masukkan data ke tabel categories
        // Gunakan insertOrIgnore agar tidak error jika ID sudah ada
        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(['id' => $category['id']], $category);
        }

        // Atau jika Anda ingin agar ID selalu auto-increment dari 1 dan reset setiap kali run:
        // DB::table('categories')->truncate(); // Hapus semua data (hati-hati)
        // DB::table('categories')->insert($categories);
    }
}
