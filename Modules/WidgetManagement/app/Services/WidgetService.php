<?php

namespace Modules\WidgetManagement\app\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;

class WidgetService
{
    /**
     * Cache öneki
     */
    protected $cachePrefix = 'widget_';
    
    /**
     * Cache süresi (dakika)
     */
    protected $cacheDuration = 1440; // 24 saat
    
    /**
     * Tüm widget'ları getir
     */
    public function getAllWidgets(): Collection
    {
        return Widget::all();
    }
    
    /**
     * Aktif widget'ları getir
     */
    public function getActiveWidgets(): Collection
    {
        return Widget::where('is_active', true)->get();
    }
    
    /**
     * Widget tipine göre widget'ları getir
     */
    public function getWidgetsByType(string $type): Collection
    {
        return Widget::where('type', $type)
            ->where('is_active', true)
            ->get();
    }
    
    /**
     * Widget ID'sine göre widget getir
     */
    public function getWidgetById($widgetId): ?Widget
    {
        return Widget::find($widgetId);
    }
    
    /**
     * Widget slug'ına göre widget getir
     */
    public function getWidgetBySlug($slug): ?Widget
    {
        return Widget::where('slug', $slug)->first();
    }
    
    /**
     * Belirli bir modüle uygun widget'ları getir
     */
    public function getWidgetsForModule($moduleId): Collection
    {
        // moduleId null ise tüm widget'ları döndür
        if ($moduleId === null) {
            return $this->getActiveWidgets();
        }
        
        return Widget::where('is_active', true)
            ->where(function ($query) use ($moduleId) {
                $query->whereNull('module_ids')
                      ->orWhereJsonContains('module_ids', $moduleId)
                      ->orWhere('module_ids', '[]');
            })
            ->get();
    }
    
    /**
     * Tenant için widget örneklerini getir
     */
    public function getTenantWidgets($pageId = null, $module = null, $position = null): Collection
    {
        $query = TenantWidget::query();
        
        if ($pageId) {
            $query->where('page_id', $pageId);
        }
        
        if ($module) {
            $query->where('module', $module);
        }
        
        if ($position) {
            $query->where('position', $position);
        }
        
        return $query->orderBy('position')
            ->orderBy('order')
            ->get();
    }
    
    /**
     * Konuma göre widget'ları render et
     */
    public function renderWidgetsInPosition(string $position, $pageId = null, $module = null): string
    {
        $cacheKey = $this->cachePrefix . tenant()->id . "_pos_{$position}" . ($pageId ? "_page_{$pageId}" : "") . ($module ? "_module_{$module}" : "");
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($position, $pageId, $module) {
            $tenantWidgets = $this->getTenantWidgets($pageId, $module, $position);
            
            $output = '';
            foreach ($tenantWidgets as $tenantWidget) {
                $output .= $this->renderSingleWidget($tenantWidget);
            }
            
            return $output;
        });
    }
    
    /**
     * Tek bir widget'ı render et
     */
    public function renderSingleWidget(TenantWidget $tenantWidget): string
    {
        $cacheKey = $this->cachePrefix . tenant()->id . "_widget_{$tenantWidget->id}";
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($tenantWidget) {
            // Özel widget ise
            if ($tenantWidget->is_custom) {
                $html = $tenantWidget->custom_html;
                $css = $tenantWidget->custom_css;
                $js = $tenantWidget->custom_js;
                $cssFiles = [];
                $jsFiles = [];
                
                // Değişkenleri değiştir
                $html = $this->processVariables($html, $tenantWidget->settings ?? []);
            } else {
                // Merkezi widget
                $widget = $tenantWidget->widget;
                
                if (!$widget || !$widget->is_active) {
                    return '';
                }
                
                $html = $widget->content_html;
                $css = $widget->content_css;
                $js = $widget->content_js;
                $cssFiles = $widget->css_files ?? [];
                $jsFiles = $widget->js_files ?? [];
                
                // Widget'ın ayarlarını al
                $settings = $tenantWidget->settings ?? [];
                
                // Dinamik widget'lar için öğeleri al
                $items = [];
                if ($widget->has_items) {
                    $items = $tenantWidget->items->map(function ($item) {
                        return $item->content;
                    })->toArray();
                    
                    // Öğeleri şablona yerleştir
                    $html = $this->processItems($html, $items);
                }
                
                // Değişkenleri değiştir
                $html = $this->processVariables($html, $settings);
                
                // Koşullu blokları işle
                $html = $this->processConditionalBlocks($html, $settings);
            }
            
            // Sonuç HTML
            $result = '';
            
            // CSS dosya bağlantıları
            if (!empty($cssFiles)) {
                foreach ($cssFiles as $cssFile) {
                    if (!empty($cssFile)) {
                        $result .= "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
                    }
                }
            }
            
            // CSS varsa ekle
            if (!empty($css)) {
                $result .= "<style>{$css}</style>\n";
            }
            
            // HTML içerik
            $result .= $html;
            
            // JS dosya bağlantıları
            if (!empty($jsFiles)) {
                foreach ($jsFiles as $jsFile) {
                    if (!empty($jsFile)) {
                        $result .= "<script src=\"{$jsFile}\"></script>\n";
                    }
                }
            }
            
            // JS varsa ekle
            if (!empty($js)) {
                $result .= "<script>{$js}</script>\n";
            }
            
            return $result;
        });
    }
    
    /**
     * Değişkenleri işle
     */
    protected function processVariables(string $content, array $settings): string
    {
        return preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($settings) {
            $key = trim($matches[1]);
            
            // Değişken kontrolü
            if (strpos($key, '#') === 0 || strpos($key, '/') === 0) {
                return $matches[0]; // Özel direktifleri koru
            }
            
            // Nokta notasyonu desteği
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $value = $settings;
                
                foreach ($parts as $part) {
                    if (isset($value[$part])) {
                        $value = $value[$part];
                    } else {
                        return ''; // Değer bulunamadı
                    }
                }
                
                return is_scalar($value) ? $value : '';
            }
            
            // Basit değişken
            return $settings[$key] ?? '';
        }, $content);
    }
    
    /**
     * Öğeleri işle
     */
    protected function processItems(string $content, array $items): string
    {
        // {{#each items}} ... {{/each}} bloklarını işle
        $pattern = '/\{\{#each\s+items\}\}(.*?)\{\{\/each\}\}/s';
        
        return preg_replace_callback($pattern, function ($matches) use ($items) {
            $itemTemplate = $matches[1];
            $result = '';
            
            foreach ($items as $item) {
                // Her öğe için şablonu işle
                $itemContent = $this->processVariables($itemTemplate, $item);
                $result .= $itemContent;
            }
            
            return $result;
        }, $content);
    }
    
    /**
     * Koşullu blokları işle
     */
    protected function processConditionalBlocks(string $content, array $settings): string
    {
        // {{#if condition}} ... {{/if}} bloklarını işle
        $pattern = '/\{\{#if\s+(.*?)\}\}(.*?)(?:\{\{else\}\}(.*?))?\{\{\/if\}\}/s';
        
        return preg_replace_callback($pattern, function ($matches) use ($settings) {
            $condition = trim($matches[1]);
            $trueContent = $matches[2];
            $falseContent = $matches[3] ?? '';
            
            // Nokta notasyonu desteği
            if (strpos($condition, '.') !== false) {
                $parts = explode('.', $condition);
                $value = $settings;
                
                foreach ($parts as $part) {
                    if (isset($value[$part])) {
                        $value = $value[$part];
                    } else {
                        $value = null;
                        break;
                    }
                }
                
                return $value ? $trueContent : $falseContent;
            }
            
            // Basit koşul
            return isset($settings[$condition]) && $settings[$condition] ? $trueContent : $falseContent;
        }, $content);
    }
    
    /**
     * Widget önbelleğini temizle
     */
    public function clearWidgetCache($tenantId = null, $widgetId = null): void
    {
        if ($widgetId) {
            // Belirli bir widget için önbelleği temizle
            if ($tenantId) {
                Cache::forget($this->cachePrefix . $tenantId . "_widget_{$widgetId}");
            } else {
                // tenantId null ise, merkezi widget önbelleğini temizle
                Cache::forget($this->cachePrefix . "central_widget_{$widgetId}");
            }
        } else {
            // Tüm widget önbelleklerini temizle
            if ($tenantId) {
                $keys = Cache::get($this->cachePrefix . "keys_{$tenantId}", []);
                
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                
                Cache::forget($this->cachePrefix . "keys_{$tenantId}");
            } else {
                // Tüm tenant'lar için temizlik (bu işlem daha dikkatli yapılmalı)
                $globalKeys = Cache::get($this->cachePrefix . "global_keys", []);
                
                foreach ($globalKeys as $key) {
                    Cache::forget($key);
                }
                
                Cache::forget($this->cachePrefix . "global_keys");
            }
        }
    }
}