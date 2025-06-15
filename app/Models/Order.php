<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'phone_number',
        'notes',
    ];

    /**
     * Relasi: Pesanan dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu pesanan memiliki banyak item.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
