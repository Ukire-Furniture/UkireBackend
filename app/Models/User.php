<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- Tambahkan ini untuk relasi HasOne

class User extends Authenticatable implements FilamentUser
    {
        use HasFactory, Notifiable, HasApiTokens;

        protected $fillable = [
            'name',
            'email',
            'password',
            'role', // <-- Tambahkan 'role' di fillable
        ];

        protected $hidden = [
            'password',
            'remember_token',
        ];

        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'role' => 'string', // <-- Tambahkan casting untuk 'role'
            ];
        }

        // Metode untuk mengecek apakah user adalah admin
        public function isAdmin(): bool
        {
            return $this->role === 'admin';
        }

        // Metode untuk mengecek akses ke panel admin Filament
        public function canAccessPanel(Panel $panel): bool
        {
            // Hanya user dengan role 'admin' yang bisa akses panel Filament
            return $this->isAdmin(); 
        }

        /**
         * Relasi: Satu user memiliki banyak pesanan.
         */
        public function orders(): HasMany
        {
            return $this->hasMany(Order::class);
        }

        /**
         * Relasi: Satu user memiliki satu wishlist.
         */
        public function wishlist(): HasOne
        {
            return $this->hasOne(Wishlist::class);
        }
    }
