<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudioWidgetService
{
    /**
     * Widget kategorilerini al
     *
     * @return array
     */
    public function getCategories(): array
    {
        return config('studio.blocks.categories', [
            'temel' => 'Temel Bileşenler',
            'mizanpaj' => 'Mizanpaj Bileşenleri',
            'medya' => 'Medya Bileşenleri',
            'bootstrap' => 'Bootstrap Bileşenleri',
            'özel' => 'Özel Bileşenler',
            'widget' => 'Widgetlar'
        ]);
    }
    
    /**
     * Tüm widgetları al
     *
     * @return array
     */
    public function getAllWidgets(): array
    {
        // Önbellekleme ayarları
        $cacheEnabled = config('studio.cache.enable', true);
        $cacheTtl = config('studio.cache.ttl', 60 * 24);
        
        // Tenant'a özgü önbellek anahtarı
        $cacheKey = $this->getCacheKey('all_widgets');
        
        // Önbellekleme aktifse, önbellekten al
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, now()->addMinutes($cacheTtl), function () {
                return $this->loadWidgetsFromDatabase();
            });
        }
        
        // Önbellekleme aktif değilse, doğrudan yükle
        return $this->loadWidgetsFromDatabase();
    }
    
    /**
     * Veritabanından widgetları yükle
     *
     * @return array
     */
    protected function loadWidgetsFromDatabase(): array
    {
        // WidgetManagement modülü yüklü değilse boş dizi döndür
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return [];
        }
        
        try {
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
                        'category' => $widget->data['category'] ?? 'widget',
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Widget yüklenirken hata: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Widget içeriğini al
     *
     * @param int $widgetId Widget ID
     * @return array|null
     */
    public function getWidgetContent(int $widgetId): ?array
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return null;
        }
        
        try {
            $widget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
            
            if (!$widget) {
                return null;
            }
            
            return [
                'html' => $widget->content_html,
                'css' => $widget->content_css,
                'js' => $widget->content_js,
            ];
        } catch (\Exception $e) {
            Log::error('Widget içeriği alınırken hata: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Widgetları GrapesJS blokları olarak al
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
                'category' => $widget['category'] ?? 'widget',
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
     * Widget önbelleğini temizle
     *
     * @return void
     */
    public function clearCache(): void
    {
        $cacheKey = $this->getCacheKey('all_widgets');
        Cache::forget($cacheKey);
    }
        
    /**
     * Önbellek anahtarı oluştur
     *
     * @param string $key Anahtar
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        $prefix = config('studio.cache.prefix', 'studio_');
        $tenantId = 'central'; // Varsayılan değer
        
        // tenant() fonksiyonu var mı ve tenant nesnesi var mı kontrol et
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant()->getTenantKey();
        }
        
        return "{$prefix}{$tenantId}_{$key}";
    }
    
    /**
     * Widget kaydı ekle veya güncelle
     * 
     * @param int $widgetId Widget ID
     * @param array $data Kaydedilecek veri
     * @return bool
     */
    public function saveWidget(int $widgetId, array $data): bool
    {
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            return false;
        }
        
        try {
            $widget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
            
            if (!$widget) {
                return false;
            }
            
            $widget->content_html = $data['html'] ?? $widget->content_html;
            $widget->content_css = $data['css'] ?? $widget->content_css;
            $widget->content_js = $data['js'] ?? $widget->content_js;
            
            if (isset($data['name'])) {
                $widget->name = $data['name'];
            }
            
            if (isset($data['description'])) {
                $widget->description = $data['description'];
            }
            
            if (isset($data['category'])) {
                $widgetData = $widget->data;
                $widgetData['category'] = $data['category'];
                $widget->data = $widgetData;
            }
            
            $result = $widget->save();
            
            // Önbelleği temizle
            $this->clearCache();
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Widget kaydedilirken hata: ' . $e->getMessage());
            return false;
        }
    }
}