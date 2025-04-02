<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Widget extends Model
{
    use CentralConnection;
    
    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'module_ids', 'content_html', 'content_css', 'content_js',
        'css_files', 'js_files',
        'thumbnail', 'has_items', 'item_schema', 'settings_schema',
        'is_active', 'is_core'
    ];
    
    protected $casts = [
        'module_ids' => 'json',
        'item_schema' => 'json',
        'settings_schema' => 'json',
        'css_files' => 'json',
        'js_files' => 'json',
        'has_items' => 'boolean',
        'is_active' => 'boolean',
        'is_core' => 'boolean',
    ];
    
    /**
     * Widget-Modül ilişkisi
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            config('modules.namespace') . 'ModuleManagement\app\Models\Module',
            'widget_modules',
            'widget_id',
            'module_id'
        );
    }
    
    /**
     * Tenant Widget örnekleri
     */
    public function tenantWidgets(): HasMany
    {
        return $this->hasMany(TenantWidget::class);
    }
    
    /**
     * Widget önizleme görselini al
     */
    public function getThumbnailUrl(): string
    {
        if ($this->thumbnail) {
            return asset("storage/widgets/{$this->slug}/{$this->thumbnail}");
        }
        
        return asset('images/default-widget-thumbnail.jpg');
    }
    
    /**
     * Widget ayar şemasını al
     */
    public function getSettingsSchema(): array
    {
        return $this->settings_schema ?? [];
    }
    
    /**
     * Widget öğe şemasını al
     */
    public function getItemSchema(): array
    {
        return $this->item_schema ?? [];
    }
}