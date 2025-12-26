<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'density_g_cm3',
        'available_colors',
        'base_cost_per_kg',
        'description',
        'properties',
        'is_active',
    ];

    protected $casts = [
        'density_g_cm3' => 'decimal:4',
        'base_cost_per_kg' => 'decimal:2',
        'available_colors' => 'array',
        'properties' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get pricing rules for this material.
     */
    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }

    /**
     * Scope to only active materials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
