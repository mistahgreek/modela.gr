<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manufacturer',
        'build_volume_x',
        'build_volume_y',
        'build_volume_z',
        'nozzle_sizes',
        'supported_materials',
        'hourly_rate',
        'is_active',
    ];

    protected $casts = [
        'build_volume_x' => 'integer',
        'build_volume_y' => 'integer',
        'build_volume_z' => 'integer',
        'nozzle_sizes' => 'array',
        'supported_materials' => 'array',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get pricing rules for this printer.
     */
    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }

    /**
     * Scope to only active printers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the build volume in cubic millimeters.
     */
    public function getBuildVolumeAttribute()
    {
        return $this->build_volume_x * $this->build_volume_y * $this->build_volume_z;
    }
}
