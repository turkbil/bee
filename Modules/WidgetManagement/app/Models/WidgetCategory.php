<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class WidgetCategory extends Model
{
    use SoftDeletes, Sluggable;

    protected $primaryKey = 'widget_category_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'order',
        'metakey',
        'metadesc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Sluggable Ayarları
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
                'unique' => true,
                'includeTrashed' => true,
            ],
        ];
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class, 'widget_category_id', 'widget_category_id');
    }
}