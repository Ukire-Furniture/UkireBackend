<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Untuk User Seeder
use App\Models\User; // Untuk User Seeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder-seeder yang Anda buat
        $this->call([
            UserSeeder::class, // Untuk membuat user admin Filament
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Ini adalah user yang dibuat oleh filament:install
        // Anda bisa memilih untuk menggunakan ini atau UserSeeder di atas
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
