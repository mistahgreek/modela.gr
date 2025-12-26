<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'three_d_model_id',
        'disk',
        'path',
        'thumbnail_path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the model that owns the image.
     */
    public function threeDModel()
    {
        return $this->belongsTo(ThreeDModel::class);
    }
}
