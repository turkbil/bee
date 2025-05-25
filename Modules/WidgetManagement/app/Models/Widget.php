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

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            config('modules.namespace') . 'ModuleManagement\app\Models\Module',
            'widget_modules',
            'widget_id',
            'module_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WidgetCategory::class, 'widget_category_id', 'widget_category_id');
    }
    
    public function tenantWidgets(): HasMany
    {
        return $this->hasMany(TenantWidget::class);
    }
    
    public function getItemSchema()
    {
        if (!$this->has_items) {
            return [];
        }
        
        $schema = $this->item_schema ?? [];
        
        if (empty($schema)) {
            return [];
        }
        
        return $this->ensureRequiredItemFields($schema);
    }
    
    public function getSettingsSchema()
    {
        $schema = $this->settings_schema ?? [];
        
        if (empty($schema)) {
            return [];
        }
        
        return $this->ensureRequiredSettingsFields($schema);
    }
    
    private function ensureRequiredItemFields($schema)
    {
        $hasTitle = false;
        $hasActive = false;
        
        foreach ($schema as $field) {
            if (isset($field['name'])) {
                if ($field['name'] === 'title') $hasTitle = true;
                if ($field['name'] === 'is_active') $hasActive = true;
            }
        }
        
        if (!$hasTitle) {
            array_unshift($schema, [
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'protected' => true
            ]);
        } else {
            foreach ($schema as &$field) {
                if (isset($field['name']) && $field['name'] === 'title') {
                    $field['system'] = true;
                    $field['protected'] = true;
                    $field['required'] = true;
                }
            }
        }
        
        if (!$hasActive) {
            $schema[] = [
                'name' => 'is_active',
                'label' => 'Durum',
                'type' => 'switch',
                'required' => false,
                'system' => true,
                'protected' => true,
                'properties' => [
                    'active_label' => 'Aktif',
                    'inactive_label' => 'Aktif Değil',
                    'default_value' => true
                ]
            ];
        } else {
            foreach ($schema as &$field) {
                if (isset($field['name']) && $field['name'] === 'is_active') {
                    $field['system'] = true;
                    $field['protected'] = true;
                    $field['type'] = 'switch';
                    $field['label'] = 'Durum';
                    $field['properties'] = [
                        'active_label' => 'Aktif',
                        'inactive_label' => 'Aktif Değil',
                        'default_value' => true
                    ];
                }
            }
        }
        
        return $schema;
    }
    
    private function ensureRequiredSettingsFields($schema)
    {
        $hasTitle = false;
        $hasUniqueId = false;
        
        foreach ($schema as $field) {
            if (isset($field['name'])) {
                if ($field['name'] === 'widget_title') $hasTitle = true;
                if ($field['name'] === 'widget_unique_id') $hasUniqueId = true;
            }
        }
        
        if (!$hasTitle) {
            array_unshift($schema, [
                'name' => 'widget_title',
                'label' => 'Widget Başlığı',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'protected' => true
            ]);
        } else {
            foreach ($schema as &$field) {
                if (isset($field['name']) && $field['name'] === 'widget_title') {
                    $field['system'] = true;
                    $field['protected'] = true;
                    $field['required'] = true;
                }
            }
        }
        
        if (!$hasUniqueId) {
            $schema[] = [
                'name' => 'widget_unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true,
                'protected' => true
            ];
        } else {
            foreach ($schema as &$field) {
                if (isset($field['name']) && $field['name'] === 'widget_unique_id') {
                    $field['system'] = true;
                    $field['protected'] = true;
                    $field['hidden'] = true;
                }
            }
        }
        
        return $schema;
    }
    
    public function getThumbnailUrl()
    {
        if (empty($this->thumbnail)) {
            return null;
        }
        
        return $this->thumbnail;
    }
}