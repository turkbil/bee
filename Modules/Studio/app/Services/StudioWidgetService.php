<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\WidgetManagement\App\Models\Widget;

class StudioWidgetService
{
    /**
     * Widget kategorilerini al
     *
     * @return array
     */
    public function getCategories()
    {
        return config('studio.widget.categories', []);
    }
    
    /**
     * Tüm widgetları al
     *
     * @return array
     */
    public function getAllWidgets()
    {
        // Widgetları önbellekten al ya da veritabanından çek
        $cacheKey = 'studio_widgets_' . tenant_id();
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                return [];
            }
            
            return Widget::where('is_active', true)
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
    public function getWidgetContent($widgetId)
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return null;
        }
        
        $widget = Widget::find($widgetId);
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
     * Widgetları GrapesJS blokları şeklinde al
     *
     * @return array
     */
    public function getWidgetsAsBlocks()
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
}