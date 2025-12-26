<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'three_d_model_id',
        'configuration',
        'price_breakdown',
        'subtotal',
        'vat_amount',
        'shipping_cost',
        'total',
        'currency',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'price_breakdown' => 'array',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the quote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model for the quote.
     */
    public function threeDModel()
    {
        return $this->belongsTo(ThreeDModel::class);
    }

    /**
     * Get the order created from this quote.
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
