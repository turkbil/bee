<?php

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Portfolio extends BaseModel implements HasMedia
{
    use Sluggable, SoftDeletes, InteractsWithMedia;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'portfolio_category_id',
        'title',
        'slug',
        'body',
        'image',
        'css',
        'js',
        'metakey',
        'metadesc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
                'maxLength' => 100,
                'unique' => true,
                'includeTrashed' => true,
            ],
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id', 'portfolio_category_id');
    }

    protected static function booted()
    {
        static::saving(function ($portfolio) {
            if ($portfolio->portfolio_category_id) {
                $category = PortfolioCategory::find($portfolio->portfolio_category_id);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk('public');
    }
}