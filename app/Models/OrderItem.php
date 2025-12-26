<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'three_d_model_id',
        'item_type',
        'title',
        'quantity',
        'material_id',
        'color',
        'layer_height',
        'infill',
        'print_settings',
        'notes',
        'unit_price',
        'total_price',
        'price_breakdown',
        'production_file_path',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'layer_height' => 'decimal:2',
        'infill' => 'integer',
        'print_settings' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'price_breakdown' => 'array',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the model for this item.
     */
    public function threeDModel()
    {
        return $this->belongsTo(ThreeDModel::class);
    }

    /**
     * Get the material for this item.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
