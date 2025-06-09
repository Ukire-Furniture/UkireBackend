<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'name',
    ];

    /**
     * Mendefinisikan relasi 'has many' ke model Product.
     * Sebuah kategori memiliki banyak produk.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}