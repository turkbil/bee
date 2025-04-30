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
            // Widget servisini al
            $widgetService = app('widget.service');
            
            // Tenant widget'ı bul
            $tenantWidget = TenantWidget::find($id);
            
            if (!$tenantWidget) {
                return "<!-- TenantWidget #{$id} bulunamadı -->";
            }
            
            // Eğer ekstra parametreler varsa, settings'e ekle
            if (!empty($params)) {
                $settings = $tenantWidget->settings ?? [];
                $tenantWidget->settings = array_merge($settings, $params);
            }
            
            // Önbellek kullanımını ayarla
            if ($cacheTtl !== null) {
                $widgetService->setCacheUsage(true);
                Cache::put('widget_cache_ttl_' . $id, $cacheTtl, now()->addDay());
            }
            
            // Widget'ı render et
            return $widgetService->renderSingleWidget($tenantWidget);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Widget render hatası (ID: {$id}): " . $e->getMessage());
            return "<!-- Widget render hatası: " . $e->getMessage() . " -->";
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
        try {
            // Widget'ı bul
            $widget = Widget::where('slug', $slug)->where('is_active', true)->first();
            
            if (!$widget) {
                return "<!-- Widget '{$slug}' bulunamadı -->";
            }
            
            // Tenant widget var mı kontrol et veya oluştur
            $settings = array_merge([
                'title' => $widget->name,
                'unique_id' => \Illuminate\Support\Str::uuid()->toString(),
            ], $params);
            
            $tenantWidget = new TenantWidget();
            $tenantWidget->widget_id = $widget->id;
            $tenantWidget->settings = $settings;
            $tenantWidget->is_active = true;
            
            // Veritabanına kaydetmiyoruz, sadece geçici bir instance oluşturuyoruz
            
            // Widget servisini al
            $widgetService = app('widget.service');
            
            // Önbellek kullanımını ayarla
            if ($cacheTtl !== null) {
                $widgetService->setCacheUsage(true);
            }
            
            // Widget'ı render et
            return $widgetService->renderSingleWidget($tenantWidget);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Widget render hatası (Slug: {$slug}): " . $e->getMessage());
            return "<!-- Widget render hatası: " . $e->getMessage() . " -->";
        }
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
        try {
            $widgetService = app('widget.service');
            return $widgetService->renderWidgetsInPosition($position);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Pozisyon widget render hatası ({$position}): " . $e->getMessage());
            return "<!-- Pozisyon widget render hatası: " . $e->getMessage() . " -->";
        }
    }
}

if (!function_exists('parse_widget_shortcodes')) {
    /**
     * İçerikteki widget kısa kodlarını işle
     *
     * @param string $content İçerik
     * @return string İşlenmiş içerik
     */
    function parse_widget_shortcodes(string $content): string
    {
        try {
            $parser = app('shortcode.parser');
            return $parser->parse($content);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Widget kısa kod ayrıştırma hatası: " . $e->getMessage());
            return $content;
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