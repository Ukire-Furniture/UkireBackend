<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Penting untuk Hash password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin Filament default
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@ukire.com'], // Cek berdasarkan email
            [
                'name' => 'Admin UKIRE',
                'email' => 'admin@ukire.com',
                'password' => Hash::make('password'), // Password default: password
                'email_verified_at' => now(), // Tandai sebagai sudah diverifikasi
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Anda bisa menambahkan user lain di sini jika diperlukan
        // DB::table('users')->updateOrInsert(
        //     ['email' => 'user@ukire.com'],
        //     [
        //         'name' => 'User Biasa',
        //         'email' => 'user@ukire.com',
        //         'password' => Hash::make('password'),
        //         'email_verified_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // );
    }
}
