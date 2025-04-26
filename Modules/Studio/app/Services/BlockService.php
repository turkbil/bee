<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

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
                
                // Editör özelliklerini belirle
                $editable = ($type === 'static' || $type === 'file');
                
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
                    $content = $widget->content_html ?? '<div class="widget-placeholder">' . $widget->name . '</div>';
                }
                break;
                
            case 'dynamic':
            case 'static':
            default:
                $content = $widget->content_html ?? '<div class="widget-placeholder">' . $widget->name . '</div>';
                break;
        }
        
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
                $content = $tenantWidget->is_custom 
                    ? $tenantWidget->custom_html 
                    : $this->prepareWidgetContent($widget, $type);
                
                $blocks[] = [
                    'id' => 'tenant-widget-' . $tenantWidget->id,
                    'label' => $tenantWidget->settings['title'] ?? $widget->name,
                    'category' => 'active-widgets',
                    'content' => $content,
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
                        'editable' => ($type === 'static' || $type === 'file'),
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