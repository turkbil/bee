<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Cviebrock\EloquentSluggable\Sluggable;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class WidgetCategory extends Model
{
    use Sluggable, CentralConnection;

    protected $primaryKey = 'widget_category_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'order',
        'icon',
        'is_active',
        'parent_id',
        'has_subcategories',
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
            ],
        ];
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class, 'widget_category_id', 'widget_category_id');
    }
    
    /**
     * Üst kategori
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(WidgetCategory::class, 'parent_id', 'widget_category_id');
    }
    
    /**
     * Alt kategoriler
     */
    public function children(): HasMany
    {
        return $this->hasMany(WidgetCategory::class, 'parent_id', 'widget_category_id')
            ->withCount('widgets')
            ->orderBy('order');
    }
    
    /**
     * Tüm alt kategorileri ve bu kategorinin tüm widget'larını getirir
     */
    public function allWidgets()
    {
        $widgetIds = $this->widgets->pluck('id')->toArray();
        
        // Alt kategorilerin widget'larını da ekle
        foreach ($this->children as $child) {
            $childWidgetIds = $child->widgets->pluck('id')->toArray();
            $widgetIds = array_merge($widgetIds, $childWidgetIds);
        }
        
        return Widget::whereIn('id', $widgetIds)->get();
    }
}