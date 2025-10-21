<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

class Widget extends Model implements HasMedia
{
    use CentralConnection, HasMediaManagement;
    
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
        $hasUniqueId = false;
        
        foreach ($schema as $field) {
            if (isset($field['name'])) {
                if ($field['name'] === 'widget_unique_id') $hasUniqueId = true;
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

    /**
     * Register media collections for Widget
     * Widget form builder'da dinamik field name'ler collection olarak kullanılır
     */
    public function registerMediaCollections(): void
    {
        // Widget için collection'lar dinamik (field name bazında)
        // registerMediaCollections boş bırakılabilir, runtime'da belirlenir
    }

    /**
     * Media collections config (HasMediaManagement trait için)
     */
    protected function getMediaCollectionsConfig(): array
    {
        // Widget için dynamic collections - field name bazında
        return [];
    }

    /**
     * Spatie Media Library için disk belirleme (tenant-aware)
     * Setting model'indeki gibi tenant context'e göre disk belirle
     */
    public function getMediaDisk(?string $collectionName = null): string
    {
        // Tenant context varsa tenant disk kullan
        $tenantId = null;
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant('id');
        }

        // Request'ten domain çöz (fallback)
        if (!$tenantId && request()) {
            $host = request()->getHost();
            $centralDomains = config('tenancy.central_domains', []);

            if (!in_array($host, $centralDomains)) {
                try {
                    $domainModel = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
                    if ($domainModel && $domainModel->tenant_id) {
                        $tenantId = $domainModel->tenant_id;
                    }
                } catch (\Exception $e) {
                    // Fallback to public disk
                }
            }
        }

        // Tenant context varsa tenant disk kullan
        if ($tenantId) {
            $diskName = 'tenant';
            $root = storage_path("tenant{$tenantId}/app/public");

            if (!is_dir($root)) {
                @mkdir($root, 0775, true);
            }

            $appUrl = request() ? request()->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

            config([
                'filesystems.disks.tenant' => [
                    'driver' => 'local',
                    'root' => $root,
                    'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantId}" : null,
                    'visibility' => 'public',
                    'throw' => false,
                ],
            ]);

            return $diskName;
        }

        // Central context için public disk
        return 'public';
    }
}