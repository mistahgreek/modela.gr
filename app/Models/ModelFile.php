<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'three_d_model_id',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'format',
        'checksum',
        'metadata',
        'processing_status',
        'processing_error',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    /**
     * Get the model that owns the file.
     */
    public function threeDModel()
    {
        return $this->belongsTo(ThreeDModel::class);
    }
}
