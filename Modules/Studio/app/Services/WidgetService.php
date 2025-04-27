<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WidgetService
{
    /**
     * Widget kategorilerini al
     *
     * @return array
     */
    public function getCategories(): array
    {
        // Widget kategorilerini widgetmanagement modülünden çek
        if (class_exists('Modules\WidgetManagement\App\Models\WidgetCategory')) {
            $categories = [];
            
            // Ana kategoriler
            $rootCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
                
            $order = 1;
            
            // Aktif bileşenler kategorisi her zaman ekle
            $categories['active-widgets'] = [
                'name' => 'Aktif Bileşenler',
                'icon' => 'fa fa-star',
                'order' => 0
            ];
            
            foreach ($rootCategories as $rootCategory) {
                $categories[$rootCategory->slug] = [
                    'name' => $rootCategory->title,
                    'icon' => $rootCategory->icon ?? 'fa fa-folder',
                    'order' => $order++
                ];
            }
            
            return $categories;
        }
        
        // Widgetmanagement modülü yoksa varsayılan kategoriler
        return [
            'active-widgets' => [
                'name' => 'Aktif Bileşenler',
                'icon' => 'fa fa-star',
                'order' => 0
            ]
        ];
    }
    
    /**
     * Tüm widget'ları al
     *
     * @return array
     */
    public function getAllWidgets(): array
    {
        // Önbellekten widget'ları al
        $cacheKey = 'studio_widgets_' . (function_exists('tenant_id') ? tenant_id() : 'default');
        $cacheTtl = config('studio.cache.ttl', 3600);
        
        return Cache::remember($cacheKey, $cacheTtl, function () {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                return [];
            }
            
            return \Modules\WidgetManagement\App\Models\Widget::where('is_active', true)
                ->get()
                ->map(function ($widget) {
                    return [
                        'id' => $widget->id,
                        'name' => $widget->name,
                        'slug' => $widget->slug,
                        'description' => $widget->description,
                        'type' => $widget->type ?: ($widget->has_items ? 'dynamic' : 'static'), // has_items varsa dynamic yoksa static
                        'thumbnail' => $widget->getThumbnailUrl(),
                        'content_html' => $widget->content_html,
                        'content_css' => $widget->content_css,
                        'content_js' => $widget->content_js,
                        'css_files' => $widget->css_files ?? [],
                        'js_files' => $widget->js_files ?? [],
                        'has_items' => $widget->has_items,
                        'category' => isset($widget->data['category']) ? $widget->data['category'] : 'widget',
                    ];
                })
                ->toArray();
        });
    }
    
    /**
     * Widget içeriğini al
     *
     * @param int $widgetId
     * @return array|null
     */
    public function getWidgetContent(int $widgetId): ?array
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return null;
        }
        
        $widget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
        if (!$widget) {
            return null;
        }
        
        return [
            'html' => $widget->content_html,
            'css' => $widget->content_css,
            'js' => $widget->content_js,
            'css_files' => $widget->css_files ?? [],
            'js_files' => $widget->js_files ?? [],
        ];
    }
    
    /**
     * Widget'ları GrapesJS blokları olarak al
     *
     * @return array
     */
    public function getWidgetsAsBlocks(): array
    {
        $widgets = $this->getAllWidgets();
        $blocks = [];
        
        foreach ($widgets as $widget) {
            $blocks[] = [
                'id' => $widget['id'],
                'name' => $widget['name'],
                'description' => $widget['description'] ?? '',
                'type' => $widget['type'] ?? ($widget['has_items'] ? 'dynamic' : 'static'),
                'category' => isset($widget['category']) ? $widget['category'] : 'widget',
                'thumbnail' => $widget['thumbnail'] ?? '',
                'content_html' => $widget['content_html'] ?? '<div class="widget-placeholder">Widget: ' . $widget['name'] . '</div>',
                'content_css' => $widget['content_css'] ?? '',
                'content_js' => $widget['content_js'] ?? '',
                'css_files' => $widget['css_files'] ?? [],
                'js_files' => $widget['js_files'] ?? [],
                'has_items' => $widget['has_items'] ?? false
            ];
        }
        
        return $blocks;
    }
    
    /**
     * Widget içeriğini güncelle
     *
     * @param int $widgetId
     * @param string $content
     * @param string $css
     * @param string $js
     * @return bool
     */
    public function updateWidgetContent(int $widgetId, string $content, string $css = '', string $js = ''): bool
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return false;
        }
        
        try {
            $widget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
            if (!$widget) {
                return false;
            }
            
            $widget->content_html = $content;
            $widget->content_css = $css;
            $widget->content_js = $js;
            $result = $widget->save();
            
            // Önbelleği temizle
            $cacheKey = 'studio_widgets_' . (function_exists('tenant_id') ? tenant_id() : 'default');
            Cache::forget($cacheKey);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Widget içeriği güncellenirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tenant widget oluştur
     *
     * @param int $widgetId
     * @param array $settings
     * @param array $items
     * @return \Modules\WidgetManagement\App\Models\TenantWidget|null
     */
    public function createTenantWidget(int $widgetId, array $settings = [], array $items = [])
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget') || 
            !class_exists('Modules\WidgetManagement\App\Models\TenantWidget')) {
            return null;
        }
        
        try {
            $widget = \Modules\WidgetManagement\App\Models\Widget::findOrFail($widgetId);
            
            $tenantWidget = new \Modules\WidgetManagement\App\Models\TenantWidget();
            $tenantWidget->widget_id = $widgetId;
            $tenantWidget->position = $settings['position'] ?? 'content';
            $tenantWidget->order = \Modules\WidgetManagement\App\Models\TenantWidget::max('order') + 1;
            $tenantWidget->settings = array_merge([
                'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => $widget->name
            ], $settings);
            $tenantWidget->save();
            
            if ($widget->has_items && !empty($items)) {
                foreach ($items as $index => $itemData) {
                    $widgetItem = new \Modules\WidgetManagement\App\Models\WidgetItem();
                    $widgetItem->tenant_widget_id = $tenantWidget->id;
                    $widgetItem->content = is_array($itemData) ? $itemData : ['content' => $itemData, 'is_active' => true];
                    $widgetItem->order = $index;
                    $widgetItem->save();
                }
            }
            
            // Önbelleği temizle
            $cacheKey = 'studio_widgets_' . (function_exists('tenant_id') ? tenant_id() : 'default');
            Cache::forget($cacheKey);
            
            return $tenantWidget;
        } catch (\Exception $e) {
            Log::error('Tenant widget oluşturulurken hata: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Widget önbelleğini temizle
     */
    public function clearWidgetCache(): void
    {
        $cacheKey = 'studio_widgets_' . (function_exists('tenant_id') ? tenant_id() : 'default');
        Cache::forget($cacheKey);
    }
}