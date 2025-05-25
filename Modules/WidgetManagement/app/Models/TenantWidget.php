<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Page\app\Models\Page;

class TenantWidget extends Model
{
    protected $fillable = [
        'widget_id', 'order',
        'settings', 'display_title', 'is_custom', 'custom_html', 'custom_css', 'custom_js', 'is_active'
    ];
    
    protected $casts = [
        'settings' => 'json',
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * Merkezi widget
     */
    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }
    
    /**
     * Widget öğeleri (dinamik widget'lar için)
     */
    public function items(): HasMany
    {
        return $this->hasMany(WidgetItem::class, 'tenant_widget_id')
            ->orderBy('order');
    }
    
    /**
     * Display title getter - öncelikle display_title, sonra settings'ten widget_title, son olarak widget name
     */
    public function getDisplayTitleAttribute()
    {
        // 1. display_title varsa onu kullan
        if (!empty($this->attributes['display_title'])) {
            return $this->attributes['display_title'];
        }
        
        // 2. settings'ten widget_title'ı al
        $settings = $this->settings ?? [];
        if (isset($settings['widget_title']) && !empty($settings['widget_title'])) {
            return $settings['widget_title'];
        }
        
        // 3. settings'ten title'ı al
        if (isset($settings['title']) && !empty($settings['title'])) {
            return $settings['title'];
        }
        
        // 4. Son çare widget'ın adını kullan
        return $this->widget ? $this->widget->name : 'Bilinmeyen Widget';
    }
    
    /**
     * Display title setter
     */
    public function setDisplayTitleAttribute($value)
    {
        $this->attributes['display_title'] = !empty($value) ? strip_tags(trim($value)) : null;
    }
    
    /**
     * Widget başlığını al (backward compatibility için)
     */
    public function getWidgetTitle()
    {
        return $this->getDisplayTitleAttribute();
    }
}