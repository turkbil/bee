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
        'settings', 'is_custom', 'custom_html', 'custom_css', 'custom_js', 'is_active'
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
}