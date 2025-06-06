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
    public function getAllBlocks(): array
    {
        Log::info('BlockService::getAllBlocks - Bloklar yükleniyor');
        
        $blocks = [];
        
        try {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                Log::warning('WidgetManagement modülü bulunamadı');
                return $blocks;
            }
            
            $blocks = array_merge($blocks, $this->getActiveTenantWidgets());
            
            $widgets = \Modules\WidgetManagement\App\Models\Widget::where('is_active', true)->get();
            
            $categories = \Modules\WidgetManagement\App\Models\WidgetCategory::get()->keyBy('widget_category_id');
            
            foreach ($widgets as $widget) {
                $type = $widget->type;
                if (empty($type)) {
                    $type = $widget->has_items ? 'dynamic' : 'static';
                }
                
                $categoryInfo = $this->getCategoryInfo($widget, $categories);
                $category = $categoryInfo['category'];
                
                $content = $this->prepareWidgetContent($widget, $type);
                
                $editable = false;
                $disable_interactions = false;
                
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
    
    private function getCategoryInfo($widget, $categories): array
    {
        $category = '';
        $categoryId = $widget->widget_category_id;
        
        $type = $widget->type ?: ($widget->has_items ? 'dynamic' : 'static');
        
        if ($categoryId && isset($categories[$categoryId])) {
            $currentCategory = $categories[$categoryId];
            $category = $currentCategory->slug;
            
            if ($currentCategory->parent_id && isset($categories[$currentCategory->parent_id])) {
                $parentCategory = $categories[$currentCategory->parent_id];
                $category = $parentCategory->slug . '-' . $category;
                
                if ($parentCategory->parent_id && isset($categories[$parentCategory->parent_id])) {
                    $grandparentCategory = $categories[$parentCategory->parent_id];
                    $category = $grandparentCategory->slug . '-' . $category;
                }
            }
        } else {
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
    
    private function prepareWidgetContent($widget, $type): string
    {
        $content = '';
        
        if ($type === 'module') {
            return '<div data-widget-module-id="' . $widget->id . '" id="module-widget-' . $widget->id . '" class="module-widget-container">
                <div id="module-content-' . $widget->id . '" class="module-widget-content-placeholder">
                    <div class="widget-loading">
                        <i class="fa fa-spin fa-spinner"></i>
                    </div>
                </div>
            </div>';
        }
        
        switch ($type) {
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
        
        return $content;
    }

    private function prepareTenantWidgetContent($widget, $tenantWidget, $type): string
    {
        if ($type === 'module') {
            return '<div data-widget-module-id="' . $widget->id . '" id="module-widget-' . $widget->id . '" class="module-widget-container">
                <div id="module-content-' . $widget->id . '" class="module-widget-content-placeholder">
                    <div class="widget-loading">
                        <i class="fa fa-spin fa-spinner"></i>
                    </div>
                </div>
            </div>';
        }
        
        $uniqueId = 'widget-content-' . $tenantWidget->id;
        
        $content = '<div class="widget-embed" data-widget-id="' . $tenantWidget->id . '" data-tenant-widget-id="' . $tenantWidget->id . '" id="widget-embed-' . $tenantWidget->id . '">';
        
        $content .= '<div class="widget-content-placeholder" id="' . $uniqueId . '">';
        $content .= '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
        $content .= '</div>';
        
        $content .= '<div class="widget-embed-info" style="display:none;" data-widget-type="' . $type . '"></div>';
        $content .= '</div>';
        
        $content .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            (function() {
                var widgetId = ' . $tenantWidget->id . ';
                var containerId = "' . $uniqueId . '";
                
                function loadWidget() {
                    console.log("Widget " + widgetId + " için yükleme başlatılıyor...");
                    var container = document.getElementById(containerId);
                    
                    if (!container) {
                        console.log("Container bulunamadı, alternatif arama yapılıyor...");
                        container = document.querySelector("#" + containerId);
                        
                        if (!container) {
                            container = document.querySelector("[id=\'" + containerId + "\']");
                        }
                        
                        if (!container) {
                            var parentElement = document.getElementById("widget-embed-" + widgetId);
                            if (parentElement) {
                                container = parentElement.querySelector(".widget-content-placeholder");
                                if (container) {
                                    container.id = containerId;
                                }
                            }
                        }
                        
                        if (!container) {
                            console.error("Widget container bulunamadı: #" + containerId);
                            return;
                        }
                    }
                    
                    if (typeof window.studioLoadWidget === "function") {
                        console.log("Global yükleyici kullanılıyor: " + widgetId);
                        window.studioLoadWidget(widgetId);
                    } else {
                        console.log("Doğrudan fetch ile yükleme yapılıyor: " + widgetId);
                        fetch("/admin/widgetmanagement/preview/embed/" + widgetId)
                            .then(function(response) { return response.text(); })
                            .then(function(html) {
                                container.innerHTML = html;
                                
                                var scripts = container.querySelectorAll("script");
                                scripts.forEach(function(script) {
                                    var newScript = document.createElement("script");
                                    if (script.src) {
                                        newScript.src = script.src;
                                    } else {
                                        newScript.textContent = script.textContent;
                                    }
                                    document.body.appendChild(newScript);
                                });
                                
                                console.log("Widget " + widgetId + " başarıyla yüklendi");
                            })
                            .catch(function(error) {
                                console.error("Widget yükleme hatası:", error);
                                container.innerHTML = "<div class=\"alert alert-danger\">Widget yüklenirken hata: " + error.message + "</div>";
                            });
                    }
                }
                
                setTimeout(loadWidget, 500);
                
                setTimeout(function() {
                    var container = document.getElementById(containerId);
                    if (container && container.querySelector(".widget-loading")) {
                        console.log("Widget " + widgetId + " için yükleme tekrar deneniyor...");
                        loadWidget();
                    }
                }, 3000);
            })();
        });
        </script>';
        
        return $content;
    }
    
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
            
            $widgetIds = $tenantWidgets->pluck('widget_id')->filter()->toArray();
            $widgets = \Modules\WidgetManagement\App\Models\Widget::whereIn('id', $widgetIds)->get()->keyBy('id');
            
            foreach ($tenantWidgets as $tenantWidget) {
                $widget = $widgets->get($tenantWidget->widget_id);
                if (!$widget) continue;
                
                $type = $widget->type;
                if (empty($type)) {
                    $type = $widget->has_items ? 'dynamic' : 'static';
                }
                
                $content = $this->prepareTenantWidgetContent($widget, $tenantWidget, $type);
                
                $editable = false;
                $disable_interactions = false;
                
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
                
                if ($tenantWidget->is_custom) {
                    $editable = true;
                    $disable_interactions = false;
                }
                
                $blocks[] = [
                    'id' => 'tenant-widget-' . $tenantWidget->id,
                    'label' => $tenantWidget->display_title ?? $widget->name,
                    'category' => 'active-widgets',
                    'content' => $content,
                    'media' => '<div class="block-media"><i class="fa fa-star"></i> ' . ($tenantWidget->display_title ?? $widget->name) . '</div>',
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
    
    public function getBlocksByCategory(string $category): array
    {
        $blocks = $this->getAllBlocks();
        
        return array_filter($blocks, function ($block) use ($category) {
            return $block['category'] === $category;
        });
    }
    
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