<?php

use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\TenantWidget;
use Illuminate\Support\Facades\Cache;

if (!function_exists('widget_by_id')) {
    /**
     * ID'ye göre widget render et
     *
     * @param int $id TenantWidget ID
     * @param array $params Ekstra parametreler
     * @param int|null $cacheTtl Önbellek süresi (saniye)
     * @return string Render edilmiş widget HTML'i
     */
    function widget_by_id(int $id, array $params = [], ?int $cacheTtl = null): string
    {
        try {
            $tenantWidget = TenantWidget::with('widget', 'items')->findOrFail($id);
            $widgetService = app('widget.service');
            return $widgetService->renderSingleWidget($tenantWidget);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Widget render hatası: " . $e->getMessage());
            return "";
        }
    }
}

if (!function_exists('widget_by_slug')) {
    /**
     * Slug'a göre widget render et
     *
     * @param string $slug Widget slug
     * @param array $params Widget parametreleri
     * @param int|null $cacheTtl Önbellek süresi (saniye)
     * @return string Render edilmiş widget HTML'i
     */
    function widget_by_slug(string $slug, array $params = [], ?int $cacheTtl = null): string
    {
        $widget = Widget::where('slug', $slug)->where('is_active', true)->first();
        if (!$widget) {
            return "";
        }
        $settings = array_merge([
            'title' => $widget->name,
            'unique_id' => \Illuminate\Support\Str::uuid()->toString(),
        ], $params);
        $tenantWidget = new TenantWidget();
        $tenantWidget->widget_id = $widget->id;
        $tenantWidget->settings = $settings;
        $tenantWidget->is_active = true;
        $tenantWidget->save();
        // Items varsa kaydet
        if ($widget->has_items && isset($params['items']) && is_array($params['items'])) {
            $widgetItemService = app('widget.item.service');
            foreach ($params['items'] as $itemData) {
                $widgetItemService->addItem($tenantWidget->id, $itemData);
            }
        }
        return widget_by_id($tenantWidget->id);
    }
}

if (!function_exists('widgets_by_position')) {
    /**
     * Belirli bir pozisyondaki tüm widget'ları render et
     *
     * @param string $position Widget pozisyonu
     * @return string Render edilmiş widgetlar
     */
    function widgets_by_position(string $position): string
    {
        $query = TenantWidget::with('widget', 'items');
        if ($position) {
            $query->where('position', $position);
        }
        $tenantWidgets = $query->orderBy('position')->orderBy('order')->get();
        $html = '';
        foreach ($tenantWidgets as $tw) {
            $html .= widget_by_id($tw->id);
        }
        return $html;
    }
}

if (!function_exists('parse_widget_shortcodes')) {
    /**
     * İçerikteki widget kısa kodlarını işle
     *
     * @param string $content İçerik
     * @return string İşlenmiş içerik
     */
    function parse_widget_shortcodes($content): string
    {
        try {
            // Content array ise string'e çevir
            if (is_array($content)) {
                // JSON multi-language content ise locale'ye göre al
                $locale = app()->getLocale();
                if (isset($content[$locale])) {
                    $content = $content[$locale];
                } else {
                    // Fallback: ilk değeri al ya da boş string
                    $content = reset($content) ?: '';
                }
            }
            
            // Null ise boş string yap
            if (is_null($content)) {
                $content = '';
            }
            
            // String değilse string'e çevir
            if (!is_string($content)) {
                $content = (string) $content;
            }
            
            $parser = app('shortcode.parser');
            return $parser->parse($content);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Widget kısa kod ayrıştırma hatası: " . $e->getMessage());
            return is_string($content) ? $content : '';
        }
    }
}

if (!function_exists('widget_cached')) {
    /**
     * Widget'ı önbellekle render et
     *
     * @param int $id TenantWidget ID
     * @param int $ttl Önbellek süresi (saniye)
     * @param array $params Ekstra parametreler
     * @return string Render edilmiş widget
     */
    function widget_cached(int $id, int $ttl = 3600, array $params = []): string
    {
        return widget_by_id($id, $params, $ttl);
    }
}

// Module widget ID ile render et
if (!function_exists('module_widget_by_id')) {
    /**
     * ID'ye göre module widget render et
     *
     * @param int $id Widget ID
     * @param array $params Ekstra parametreler
     * @param int|null $cacheTtl Önbellek süresi (saniye)
     * @return string Render edilmiş widget HTML'i
     */
    function module_widget_by_id(int $id, array $params = [], ?int $cacheTtl = null): string
    {
        $widget = Widget::where('id', $id)->where('type', 'module')->where('is_active', true)->first();
        if (!$widget) {
            return "";
        }
        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
        try {
            return \View::make($viewPath, ['widget' => $widget, 'params' => $params])->render();
        } catch (\Throwable $e) {
            \Log::error("Module widget render hatası: " . $e->getMessage());
            return '';
        }
    }
}

// Module ID ile ilişkili module widgetları render et
if (!function_exists('module_widgets_by_module')) {
    /**
     * Belirli bir module_id'deki module widget'ları render et
     *
     * @param int $moduleId Module ID
     * @param array $params Ekstra parametreler
     * @return string Render edilmiş widgetlar
     */
    function module_widgets_by_module(int $moduleId, array $params = []): string
    {
        $widgets = Widget::where('type', 'module')->where('is_active', true)
            ->whereHas('modules', function ($q) use ($moduleId) {
                $q->where('module_id', $moduleId);
            })->get();
        $html = '';
        foreach ($widgets as $widget) {
            $html .= module_widget_by_id($widget->id, $params);
        }
        return $html;
    }
}

// File widget ID ile render et  
if (!function_exists('widget_file_by_id')) {
    /**
     * ID'ye göre file widget render et
     *
     * @param int $id Widget ID
     * @param array $params Ekstra parametreler
     * @param int|null $cacheTtl Önbellek süresi (saniye)
     * @return string Render edilmiş widget HTML'i
     */
    function widget_file_by_id(int $id, array $params = [], ?int $cacheTtl = null): string
    {
        $widget = Widget::where('id', $id)->where('type', 'file')->where('is_active', true)->first();
        if (!$widget) {
            return "";
        }
        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
        try {
            return \View::make($viewPath, ['widget' => $widget, 'params' => $params])->render();
        } catch (\Throwable $e) {
            \Log::error("File widget render hatası: " . $e->getMessage());
            return '';
        }
    }
}