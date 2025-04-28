<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use LightnCandy\LightnCandy;
use LightnCandy\Str;

class BlockService
{
    /**
     * Tüm blokları al
     *
     * @return array
     */
    public function getAllBlocks(): array
    {
        Log::info('BlockService::getAllBlocks - Bloklar yükleniyor');
        
        $blocks = [];
        
        try {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                Log::warning('WidgetManagement modülü bulunamadı');
                return $blocks;
            }
            
            // Aktif tenant widget'ları
            $blocks = array_merge($blocks, $this->getActiveTenantWidgets());
            
            // Widget tablosundaki bileşenleri ekle (dynamic, static, module, file)
            $widgets = \Modules\WidgetManagement\App\Models\Widget::where('is_active', true)->get();
            
            // Kategori bilgilerini al
            $categories = \Modules\WidgetManagement\App\Models\WidgetCategory::get()->keyBy('widget_category_id');
            
            foreach ($widgets as $widget) {
                // Widget tipini belirle
                $type = $widget->type;
                if (empty($type)) {
                    $type = $widget->has_items ? 'dynamic' : 'static';
                }
                
                // Kategori bilgilerini hazırla
                $categoryInfo = $this->getCategoryInfo($widget, $categories);
                $category = $categoryInfo['category'];
                
                // İçeriği hazırla
                $content = $this->prepareWidgetContent($widget, $type);
                
                // Editör özelliklerini belirle - burada değişiklik yapıyoruz
                $editable = false;
                $disable_interactions = false;
                
                // Widget tipine göre davranışları belirle
                switch ($type) {
                    case 'static':
                    case 'file':
                        $editable = true;
                        $disable_interactions = false;
                        break;
                    case 'dynamic':
                    case 'module':
                        $editable = false;
                        $disable_interactions = true;
                        break;
                }
                
                $blocks[] = [
                    'id' => 'widget-' . $widget->id,
                    'label' => $widget->name,
                    'category' => $category,
                    'content' => $content,
                    'css_content' => $widget->content_css ?? '',
                    'js_content' => $widget->content_js ?? '',
                    'css_files' => $widget->css_files ?? [],
                    'js_files' => $widget->js_files ?? [],
                    'widget_id' => $widget->id,
                    'type' => $type,
                    'has_items' => $widget->has_items,
                    'is_widget' => true,
                    'icon' => $this->getIconForType($type),
                    'meta' => [
                        'file_path' => $widget->file_path,
                        'editable' => $editable,
                        'disable_interactions' => $disable_interactions,
                        'module_type' => ($type === 'module')
                    ]
                ];
            }
            
            Log::info('BlockService - Toplam ' . count($blocks) . ' adet blok yüklendi');
        } catch (\Exception $e) {
            Log::error('BlockService - Blok yükleme hatası: ' . $e->getMessage());
        }
        
        return $blocks;
    }
    
    /**
     * Kategori bilgilerini elde eder
     */
    private function getCategoryInfo($widget, $categories): array
    {
        $category = '';
        $categoryId = $widget->widget_category_id;
        
        // Özel kategori atamaları
        $type = $widget->type ?: ($widget->has_items ? 'dynamic' : 'static');
        
        // Kategori bilgisini belirle
        if ($categoryId && isset($categories[$categoryId])) {
            $currentCategory = $categories[$categoryId];
            $category = $currentCategory->slug;
            
            // Eğer üst kategorisi varsa, birleştir
            if ($currentCategory->parent_id && isset($categories[$currentCategory->parent_id])) {
                $parentCategory = $categories[$currentCategory->parent_id];
                $category = $parentCategory->slug . '-' . $category;
                
                // Üçüncü seviye için kontrol
                if ($parentCategory->parent_id && isset($categories[$parentCategory->parent_id])) {
                    $grandparentCategory = $categories[$parentCategory->parent_id];
                    $category = $grandparentCategory->slug . '-' . $category;
                }
            }
        } else {
            // Varsayılan kategori atamaları
            if ($type === 'module') {
                $category = 'moduller';
            } else if ($type === 'file') {
                $category = 'page';
            } else if ($type === 'dynamic' || $type === 'static') {
                $category = 'content';
            }
        }
        
        return [
            'category' => $category
        ];
    }
    
    /**
     * Widget içeriğini hazırla
     */
    private function prepareWidgetContent($widget, $type): string
    {
        $content = '';
        
        switch ($type) {
            case 'module':
            case 'file':
                if (!empty($widget->file_path)) {
                    try {
                        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
                        if (View::exists($viewPath)) {
                            $content = View::make($viewPath, ['settings' => []])->render();
                        } else {
                            $content = '<div class="widget-placeholder">Görünüm bulunamadı: ' . $viewPath . '</div>';
                        }
                    } catch (\Exception $e) {
                        $content = '<div class="widget-placeholder">Hata: ' . $e->getMessage() . '</div>';
                    }
                } else {
                    if (!empty(trim($widget->content_html))) {
                        $content = $widget->content_html;
                    } else {
                        $content = '<div class="widget-placeholder">Widget: ' . $widget->name . '</div>';
                    }
                }
                break;
                
            case 'dynamic':
            case 'static':
            default:
                if (!empty(trim($widget->content_html))) {
                    $content = $widget->content_html;
                } else {
                    $content = '<div class="widget-placeholder">Widget: ' . $widget->name . '</div>';
                }
                break;
        }
        
        // Studio editörde kilitli container wrapper ekle
        if ($type === 'module' && Request::is('admin/studio*')) {
            $content = '<div class="widget-container widget-type-' . $type . '" data-widget-type="' . $type . '" contenteditable="false" style="pointer-events:none;">'
                     . '<div class="widget-content" style="filter:grayscale(20%) blur(0.3px); opacity:0.9;">'
                     . $content
                     . '</div></div>';
        }
        
        return $content;
    }
        
    /**
     * Tenant widget içeriğini hazırla (dinamik ve static)
     */
    private function prepareTenantWidgetContent($widget, $tenantWidget, $type): string
    {
        // Widget ID'sini ve bağlantı noktasını içeren div oluşturuyoruz
        $content = '<div class="widget-embed" data-widget-id="' . $tenantWidget->id . '" data-tenant-widget-id="' . $tenantWidget->id . '">';
        
        // Widget placeholder ile başlangıç içeriği göster (JavaScript ile doldurulacak)
        $content .= '<div class="widget-content-placeholder">';
        $content .= '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
        $content .= '</div>';
        
        // Widget tipi ve ID bilgisi
        $content .= '<div class="widget-embed-info" style="display:none;" data-widget-type="' . $type . '"></div>';
        $content .= '</div>';
        
        // Hidden script ile widget yükleyiciyi ekle
        $content .= '<script>
        (function() {
            // Widget yükleyici fonksiyonu
            window.loadTenantWidget = window.loadTenantWidget || function(widgetId) {
                var container = document.querySelector(".widget-embed[data-tenant-widget-id=\'" + widgetId + "\'] .widget-content-placeholder");
                if (!container) return;
                
                fetch("/admin/widgetmanagement/preview/embed/" + widgetId)
                    .then(function(response) { return response.text(); })
                    .then(function(html) {
                        container.innerHTML = html;
                        
                        // Başka bir yükleyicide Handlebars bekliyor olabilir, bekleme yaparak işlemleri sıralı yapalım
                        setTimeout(function() {
                            // Handlebars scriptlerini çalıştır
                            var scripts = container.querySelectorAll("script");
                            scripts.forEach(function(script) {
                                // İçeriği alıp yeni bir script elementi oluştur
                                var newScript = document.createElement("script");
                                if (script.src) {
                                    newScript.src = script.src;
                                } else {
                                    newScript.textContent = script.textContent;
                                }
                                // Yeni scripti document.body\'ye ekle
                                document.body.appendChild(newScript);
                            });
                        }, 100);
                    })
                    .catch(function(error) {
                        container.innerHTML = "<div class=\'alert alert-danger\'>Widget yüklenirken hata: " + error.message + "</div>";
                    });
            };
            
            // Widget ID\'sini al ve yükleyiciyi başlat
            var widgetId = ' . $tenantWidget->id . ';
            // Sayfa tamamen yüklendiğinde çalıştır
            if (document.readyState === "complete") {
                window.loadTenantWidget(widgetId);
            } else {
                window.addEventListener("load", function() {
                    window.loadTenantWidget(widgetId);
                });
            }
        })();
        </script>';
        
        return $content;
    }
    
    /**
     * Widget tipine göre ikon belirle
     */
    private function getIconForType($type): string
    {
        switch ($type) {
            case 'module':
                return 'fa fa-cube';
            case 'file':
                return 'fa fa-file';
            case 'dynamic':
                return 'fa fa-puzzle-piece';
            case 'static':
                return 'fa fa-puzzle-piece';
            default:
                return 'fa fa-puzzle-piece';
        }
    }
    
    /**
     * Aktif tenant widget'ları 
     */
    private function getActiveTenantWidgets(): array
    {
        $blocks = [];
        
        try {
            $tenantWidgets = \Modules\WidgetManagement\App\Models\TenantWidget::where('is_active', true)
                ->orderBy('order')
                ->get();
            
            if ($tenantWidgets->isEmpty()) {
                return [];
            }
            
            // Widget bilgilerini al
            $widgetIds = $tenantWidgets->pluck('widget_id')->filter()->toArray();
            $widgets = \Modules\WidgetManagement\App\Models\Widget::whereIn('id', $widgetIds)->get()->keyBy('id');
            
            foreach ($tenantWidgets as $tenantWidget) {
                $widget = $widgets->get($tenantWidget->widget_id);
                if (!$widget) continue;
                
                // Widget tipini belirle
                $type = $widget->type;
                if (empty($type)) {
                    $type = $widget->has_items ? 'dynamic' : 'static';
                }
                
                // İçeriği belirle (özel mi yoksa widget'tan mı)
                $content = $this->prepareTenantWidgetContent($widget, $tenantWidget, $type);
                
                // Editör özelliklerini belirle - tenant widget'lar için uyarla
                $editable = false;
                $disable_interactions = false;
                
                // Widget tipine göre davranışları belirle
                switch ($type) {
                    case 'static':
                    case 'file':
                        $editable = true;
                        $disable_interactions = false;
                        break;
                    case 'dynamic':
                    case 'module':
                        $editable = false;
                        $disable_interactions = true;
                        break;
                }
                
                // Özel tenant widget davranışını belirle
                if ($tenantWidget->is_custom) {
                    $editable = true;
                    $disable_interactions = false;
                }
                
                $blocks[] = [
                    'id' => 'tenant-widget-' . $tenantWidget->id,
                    'label' => $tenantWidget->settings['title'] ?? $widget->name,
                    'category' => 'active-widgets',
                    'content' => $content,
                    'media' => '<div class="block-media"><i class="fa fa-star"></i> ' . ($tenantWidget->settings['title'] ?? $widget->name) . '</div>',
                    'css_content' => $tenantWidget->is_custom ? $tenantWidget->custom_css : $widget->content_css,
                    'js_content' => $tenantWidget->is_custom ? $tenantWidget->custom_js : $widget->content_js,
                    'css_files' => $widget->css_files ?? [],
                    'js_files' => $widget->js_files ?? [],
                    'tenant_widget_id' => $tenantWidget->id,
                    'widget_id' => $widget->id,
                    'type' => $type,
                    'is_tenant_widget' => true,
                    'icon' => 'fa fa-star',
                    'meta' => [
                        'file_path' => $widget->file_path,
                        'editable' => $editable,
                        'disable_interactions' => $disable_interactions,
                        'module_type' => ($type === 'module')
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('Tenant widget yükleme hatası: ' . $e->getMessage());
        }
        
        return $blocks;
    }
    
    /**
     * Kategoriye göre blokları al
     *
     * @param string $category
     * @return array
     */
    public function getBlocksByCategory(string $category): array
    {
        $blocks = $this->getAllBlocks();
        
        return array_filter($blocks, function ($block) use ($category) {
            return $block['category'] === $category;
        });
    }
    
    /**
     * Bloğu HTML olarak render et
     *
     * @param string $blockId
     * @return string|null
     */
    public function renderBlock(string $blockId): ?string
    {
        $blocks = $this->getAllBlocks();
        
        foreach ($blocks as $block) {
            if ($block['id'] === $blockId) {
                $result = '';
                
                if (!empty($block['css_content'])) {
                    $result .= '<style>' . $block['css_content'] . '</style>';
                }
                
                if (!empty($block['css_files']) && is_array($block['css_files'])) {
                    foreach ($block['css_files'] as $cssFile) {
                        $result .= '<link rel="stylesheet" href="' . $cssFile . '">';
                    }
                }
                
                $result .= $block['content'] ?? '';
                
                if (!empty($block['js_files']) && is_array($block['js_files'])) {
                    foreach ($block['js_files'] as $jsFile) {
                        $result .= '<script src="' . $jsFile . '"></script>';
                    }
                }
                
                if (!empty($block['js_content'])) {
                    $result .= '<script>' . $block['js_content'] . '</script>';
                }
                
                return $result;
            }
        }
        
        return null;
    }
}