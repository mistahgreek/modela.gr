<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ThreeDModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'license',
        'tags',
        'visibility',
        'price_type',
        'model_price',
        'status',
        'download_count',
        'view_count',
        'like_count',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'model_price' => 'decimal:2',
        'published_at' => 'datetime',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    /**
     * Get the user that owns the model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the model.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the files for the model.
     */
    public function files()
    {
        return $this->hasMany(ModelFile::class);
    }

    /**
     * Get the images for the model.
     */
    public function images()
    {
        return $this->hasMany(ModelImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the model.
     */
    public function primaryImage()
    {
        return $this->hasOne(ModelImage::class)->where('is_primary', true);
    }

    /**
     * Get the quotes for the model.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Increment view count.
     */
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    /**
     * Increment download count.
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    /**
     * Increment like count.
     */
    public function incrementLikes()
    {
        $this->increment('like_count');
    }

    /**
     * Check if model is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->visibility === 'public';
    }

    /**
     * Scope to only published models.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('visibility', 'public');
    }

    /**
     * Scope to search models.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
