<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'material_id',
        'printer_id',
        'min_volume',
        'max_volume',
        'min_weight',
        'max_weight',
        'price_per_gram',
        'setup_fee',
        'machine_time_rate',
        'margin_percent',
        'min_price',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'min_volume' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'price_per_gram' => 'decimal:4',
        'setup_fee' => 'decimal:2',
        'machine_time_rate' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'min_price' => 'decimal:2',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the material for this rule.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the printer for this rule.
     */
    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    /**
     * Scope to only active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority', 'desc');
    }
}
