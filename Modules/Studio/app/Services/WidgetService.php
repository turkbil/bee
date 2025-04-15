<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;

class WidgetService
{
    /**
     * Widget kategorilerini al
     *
     * @return array
     */
    public function getCategories(): array
    {
        return config('studio.blocks.categories', [
            'layout' => [
                'name' => 'Düzen',
                'icon' => 'fa fa-columns',
                'order' => 1,
            ],
            'content' => [
                'name' => 'İçerik',
                'icon' => 'fa fa-font',
                'order' => 2,
            ],
            'form' => [
                'name' => 'Form',
                'icon' => 'fa fa-wpforms',
                'order' => 3,
            ],
            'media' => [
                'name' => 'Medya',
                'icon' => 'fa fa-image',
                'order' => 4,
            ],
            'widget' => [
                'name' => 'Widgetlar',
                'icon' => 'fa fa-puzzle-piece',
                'order' => 5,
            ],
        ]);
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
                        'type' => $widget->type,
                        'thumbnail' => $widget->thumbnail,
                        'content_html' => $widget->content_html,
                        'content_css' => $widget->content_css,
                        'content_js' => $widget->content_js,
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
                'id' => 'widget-' . $widget['id'],
                'label' => $widget['name'],
                'category' => isset($widget['category']) ? $widget['category'] : 'widget',
                'content' => [
                    'widget_id' => $widget['id'],
                    'type' => 'widget',
                    'html' => $widget['content_html'] ?? '<div class="widget-placeholder">Widget: ' . $widget['name'] . '</div>',
                    'css' => $widget['content_css'] ?? '',
                    'js' => $widget['content_js'] ?? '',
                ],
                'attributes' => [
                    'class' => 'fa fa-puzzle-piece'
                ]
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
            \Illuminate\Support\Facades\Log::error('Widget içeriği güncellenirken hata: ' . $e->getMessage());
            return false;
        }
    }
}