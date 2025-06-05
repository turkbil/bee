<?php

namespace Modules\WidgetManagement\App\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\TenantWidget;
use Illuminate\Support\Facades\Cache;

class ShortcodeParser
{
    /**
     * Kısa kod formatı: [[widget:slug param1=value1 param2=value2]]
     * İç içe kısa kod formatı: [[widget:slug]]İçerik[[/widget:slug]]
     */
    protected $pattern = '/\[\[widget:([\w\-]+)(?:\s+([^\]]+))?\]\](.*?)(?:\[\[\/widget:\1\]\]|(?=\[\[widget)|\Z)/s';
    protected $simplePattern = '/\[\[widget:([\w\-]+)(?:\s+([^\]]+))?\]\]/';
    
    /**
     * Module widget pattern
     */
    protected $modulePattern = '/\[\[module:([\d]+)(?:\s+([^\]]+))?\]\]/';
    
    /**
     * File widget pattern
     */
    protected $filePattern = '/\[\[file:([\d]+)(?:\s+([^\]]+))?\]\]/';
    
    /**
     * Module widget HTML pattern
     */
    protected $moduleHtmlPattern = '/<div[^>]*data-widget-module-id="(\d+)"[^>]*>.*?<\/div>/s';
    
    /**
     * İçerikteki tüm widget kısa kodlarını işle
     *
     * @param string $content İçerik metni
     * @return string İşlenmiş içerik
     */
    public function parse(string $content): string
    {
        if (empty($content)) {
            return $content;
        }
        
        // İlk olarak HTML formatındaki module widget'ları işle
        $content = preg_replace_callback($this->moduleHtmlPattern, function ($matches) {
            $id = (int)$matches[1];
            return $this->renderModuleWidget($id, []);
        }, $content);
        
        // İlk olarak iç içe format için işlem yap
        $content = preg_replace_callback($this->pattern, function ($matches) {
            $slug = $matches[1];
            $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
            $innerContent = isset($matches[3]) ? $matches[3] : '';
            
            // İç içe içerik varsa params içine ekle
            if (!empty($innerContent)) {
                $params['content'] = $innerContent;
            }
            
            return $this->renderWidget($slug, $params);
        }, $content);
        
        // Basit format için ikinci bir işlem
        $content = preg_replace_callback($this->simplePattern, function ($matches) {
            $slug = $matches[1];
            $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
            
            return $this->renderWidget($slug, $params);
        }, $content);
        
        // Module pattern
        $content = preg_replace_callback($this->modulePattern, function ($matches) {
            $id = (int)$matches[1];
            $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
            
            return $this->renderModuleWidget($id, $params);
        }, $content);
        
        // File pattern
        $content = preg_replace_callback($this->filePattern, function ($matches) {
            $id = (int)$matches[1];
            $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
            
            return $this->renderFileWidget($id, $params);
        }, $content);
        
        return $content;
    }
    
    /**
     * Widget kısa kod parametrelerini ayrıştır
     *
     * @param string $paramsString Parametre metni
     * @return array İşlenmiş parametreler
     */
    protected function parseParams(string $paramsString): array
    {
        $params = [];
        
        // Anahtar=değer formatında parametreleri ayır
        preg_match_all('/(\w+)=(?:(["\'])(.*?)\2|(\S+))/', $paramsString, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $key = $match[1];
            $value = isset($match[3]) ? $match[3] : (isset($match[4]) ? $match[4] : '');
            
            // Boole değerleri doğru şekilde dönüştür
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            } elseif (is_numeric($value)) {
                $value = $value + 0; // Sayıya dönüştür
            }
            
            $params[$key] = $value;
        }
        
        return $params;
    }
    
    /**
     * Widget'ı render et
     *
     * @param string $slug Widget slug
     * @param array $params Widget parametreleri
     * @return string Render edilmiş HTML
     */
    protected function renderWidget(string $slug, array $params = []): string
    {
        // Eğer slug sayısal ise ID üzerinden widget render et
        if (ctype_digit($slug)) {
            return widget_by_id((int)$slug, $params);
        }
        
        try {
            // Önbelleğe alınmış widget'ı kontrol et
            $cacheKey = 'widget_shortcode_' . md5($slug . json_encode($params));
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // İlk olarak slug ile widget'ı bul
            $widget = Widget::where('slug', $slug)->where('is_active', true)->first();
            
            if (!$widget) {
                return "<!-- Widget bulunamadı: {$slug} -->";
            }
            
            // Tenant widget var mı kontrol et veya oluştur
            $tenantWidget = $this->findOrCreateTenantWidget($widget, $params);
            
            if (!$tenantWidget) {
                return "<!-- Widget yüklenemedi: {$slug} -->";
            }
            
            // Widget servisini kullanarak widget içeriğini render et
            $widgetService = app('widget.service');
            $html = $widgetService->renderSingleWidget($tenantWidget);
            
            // Önbelleğe al
            Cache::put($cacheKey, $html, now()->addHours(1));
            
            return $html;
        } catch (\Exception $e) {
            Log::error("Widget kısa kodu işlenirken hata: " . $e->getMessage(), [
                'slug' => $slug,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            
            return "<!-- Widget işleme hatası: {$slug} -->";
        }
    }
    
    /**
     * Module widget'ı render et
     *
     * @param int $id Module widget ID
     * @param array $params Parametreler
     * @return string Render edilmiş HTML
     */
        protected function renderModuleWidget(int $id, array $params = []): string
    {
        try {
            $widgetModel = \Modules\WidgetManagement\App\Models\Widget::where('id', $id)
                            ->where('type', 'module')
                            ->where('is_active', true)
                            ->first();

            if (!$widgetModel) {
                \Illuminate\Support\Facades\Log::warning("Module Widget (base) '{$id}' bulunamadı veya aktif değil.");
                return "<!-- Module Widget '{$id}' bulunamadı -->";
            }

            // Bu widget için TenantWidget'ı bul veya oluştur.
            // findOrCreateTenantWidget, $params'ı (kısa kod parametreleri) kullanarak
            // TenantWidget'ın settings'ini güncelleyebilir veya oluşturabilir.
            $tenantWidget = $this->findOrCreateTenantWidget($widgetModel, $params);

            if (!$tenantWidget) {
                \Illuminate\Support\Facades\Log::error("Module TenantWidget '{$id}' için oluşturulamadı veya bulunamadı.");
                return "<!-- Module Widget '{$id}' yüklenemedi -->";
            }
            
            // WidgetService'i kullanarak render et
            $widgetService = app('widget.service');
            return $widgetService->renderSingleWidget($tenantWidget);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Module widget kısa kodu işlenirken hata (ID: {$id}): " . $e->getMessage(), [
                'id' => $id,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            
            return "<!-- Module widget işleme hatası: {$id} -->";
        }
    }
    
    /**
     * File widget'ı render et
     *
     * @param int $id File widget ID
     * @param array $params Parametreler
     * @return string Render edilmiş HTML
     */
    protected function renderFileWidget(int $id, array $params = []): string
    {
        try {
            return widget_file_by_id($id, $params);
        } catch (\Exception $e) {
            Log::error("File widget kısa kodu işlenirken hata: " . $e->getMessage(), [
                'id' => $id,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            
            return "<!-- File widget işleme hatası: {$id} -->";
        }
    }
    
    /**
     * Tenant widget'ı bul veya oluştur
     *
     * @param Widget $widget Widget modeli
     * @param array $params Widget parametreleri
     * @return TenantWidget|null
     */
    protected function findOrCreateTenantWidget(Widget $widget, array $params = []): ?TenantWidget
    {
        // Unique parametre kontrolü
        $uniqueId = $params['id'] ?? null;
        
        if ($uniqueId) {
            // ID parametresi varsa, bu ID'ye sahip tenant widget'ı ara
            $existingWidget = TenantWidget::where('settings->widget.unique_id', $uniqueId)
                ->where('widget_id', $widget->id)
                ->first();
                
            if ($existingWidget) {
                return $existingWidget;
            }
        }
        
        // Yeni tenant widget oluştur
        $settings = array_merge([
            'widget_title' => $widget->name,
            'widget.unique_id' => $uniqueId ?? Str::uuid()->toString(),
        ], $params);
        
        $tenantWidget = new TenantWidget();
        $tenantWidget->widget_id = $widget->id;
        $tenantWidget->settings = $settings;
        $tenantWidget->is_active = true;
        $tenantWidget->save();
        
        // Dinamik widget'lar için items varsa ekleyelim
        if ($widget->has_items && isset($params['items']) && is_array($params['items'])) {
            $widgetItemService = app('widget.item.service');
            
            foreach ($params['items'] as $index => $itemData) {
                $widgetItemService->addItem($tenantWidget->id, $itemData);
            }
        }
        
        return $tenantWidget;
    }
}