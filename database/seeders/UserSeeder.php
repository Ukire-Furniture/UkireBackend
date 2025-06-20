<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin UKIRE
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@ukire.com'], // Cek berdasarkan email
            [
                'name' => 'Admin UKIRE',
                'email' => 'admin@ukire.com',
                'password' => Hash::make('password'), // Password default: password
                'role' => 'admin', // <-- Set role sebagai 'admin'
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Buat user biasa (jika ingin ada default user biasa)
        DB::table('users')->updateOrInsert(
            ['email' => 'user@ukire.com'],
            [
                'name' => 'User UKIRE',
                'email' => 'user@ukire.com',
                'password' => Hash::make('password'), // Password default: password
                'role' => 'user', // <-- Set role sebagai 'user'
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
