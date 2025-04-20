<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Widget extends Model
{
    use CentralConnection;
    
    protected $fillable = [
        'widget_category_id',
        'name', 
        'slug', 
        'description', 
        'type',
        'module_ids', 
        'content_html', 
        'content_css', 
        'content_js',
        'css_files', 
        'js_files',
        'thumbnail', 
        'has_items', 
        'item_schema', 
        'settings_schema',
        'is_active', 
        'is_core',
        'file_path'
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
     * Widget-Kategori ilişkisi
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WidgetCategory::class, 'widget_category_id', 'widget_category_id');
    }
    
    /**
     * Tenant Widget örnekleri
     */
    public function tenantWidgets(): HasMany
    {
        return $this->hasMany(TenantWidget::class);
    }
    
    /**
     * Item şemasını döndür
     */
    public function getItemSchema()
    {
        if (!$this->has_items) {
            return [];
        }
        
        $schema = $this->item_schema;
        
        if (empty($schema)) {
            return [];
        }
        
        return $schema;
    }
    
    /**
     * Settings şemasını döndür
     */
    public function getSettingsSchema()
    {
        $schema = $this->settings_schema;
        
        if (empty($schema)) {
            return [];
        }
        
        return $schema;
    }
    
    /**
     * Thumbnail URL'ini döndür
     */
    public function getThumbnailUrl()
    {
        if (empty($this->thumbnail)) {
            return null;
        }
        
        return $this->thumbnail;
    }
}