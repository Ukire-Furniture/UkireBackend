<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image_path',
    ];

    /**
     * Mendefinisikan relasi 'belongs to' ke model Category.
     * Sebuah produk dimiliki oleh satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}