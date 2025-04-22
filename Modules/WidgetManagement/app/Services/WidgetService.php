<?php

namespace Modules\WidgetManagement\app\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\Widget\WidgetRenderService;
use Modules\WidgetManagement\app\Services\Widget\WidgetCacheService;

class WidgetService
{
    protected $renderService;
    protected $cacheService;
    
    protected $cachePrefix = 'widget_';
    protected $cacheDuration = 1440;
    protected $useCache = false; // Önbellek kullanımını varsayılan olarak kapalı yap
    
    public function __construct(
        WidgetRenderService $renderService = null,
        WidgetCacheService $cacheService = null
    ) {
        $this->renderService = $renderService ?? new WidgetRenderService();
        $this->cacheService = $cacheService ?? new WidgetCacheService($this->cachePrefix);
    }
    
    // Önbellek kullanımını ayarla
    public function setCacheUsage($useCache)
    {
        $this->useCache = $useCache;
        return $this;
    }
    
    public function getAllWidgets(): Collection
    {
        return Widget::all();
    }
    
    public function getActiveWidgets(): Collection
    {
        return Widget::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
    
    public function getWidgetsByType(string $type): Collection
    {
        return Widget::where('type', $type)
            ->where('is_active', true)
            ->get();
    }
    
    public function getWidgetById($widgetId): ?Widget
    {
        return Widget::find($widgetId);
    }
    
    public function getWidgetBySlug($slug): ?Widget
    {
        return Widget::where('slug', $slug)->first();
    }
    
    public function getWidgetsForModule($moduleId): Collection
    {
        if ($moduleId === null) {
            return $this->getActiveWidgets();
        }
        
        return Widget::where('is_active', true)
            ->where(function ($query) use ($moduleId) {
                $query->whereNull('module_ids')
                      ->orWhereJsonContains('module_ids', $moduleId)
                      ->orWhere('module_ids', '[]');
            })
            ->orderBy('name')
            ->get();
    }
    
    public function getTenantWidgets($position = null): Collection
    {
        $query = TenantWidget::query();
        
        if ($position) {
            $query->where('position', $position);
        }
        
        return $query->orderBy('position')
            ->orderBy('order')
            ->get();
    }
    
    public function renderWidgetsInPosition(string $position): string
    {
        $cacheKey = $this->cachePrefix . tenant()->id . "_pos_{$position}";
        
        if ($this->useCache) {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($position) {
                return $this->generatePositionOutput($position);
            });
        }
        
        return $this->generatePositionOutput($position);
    }
    
    private function generatePositionOutput(string $position): string
    {
        $tenantWidgets = $this->getTenantWidgets($position);
        
        $output = '';
        foreach ($tenantWidgets as $tenantWidget) {
            $output .= $this->renderSingleWidget($tenantWidget);
        }
        
        return $output;
    }
        
    public function renderSingleWidget(TenantWidget $tenantWidget): string
    {
        $cacheKey = $this->cachePrefix . tenant()->id . "_widget_{$tenantWidget->id}";
        
        if ($this->useCache) {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($tenantWidget) {
                return $this->processWidget($tenantWidget);
            });
        }
        
        return $this->processWidget($tenantWidget);
    }
    
    private function processWidget(TenantWidget $tenantWidget): string
    {
        if ($tenantWidget->is_custom) {
            $html = $tenantWidget->custom_html;
            $css = $tenantWidget->custom_css;
            $js = $tenantWidget->custom_js;
            $cssFiles = [];
            $jsFiles = [];
            
            $html = $this->renderService->processVariables($html, $tenantWidget->settings ?? []);
        } else {
            $widget = $tenantWidget->widget;
            
            if (!$widget || !$widget->is_active) {
                return '';
            }
            
            if ($widget->type === 'file' && !empty($widget->file_path)) {
                try {
                    $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
                    if (view()->exists($viewPath)) {
                        $settings = $tenantWidget->settings ?? [];
                        return view($viewPath, ['settings' => $settings])->render();
                    } else {
                        return '<div class="alert alert-danger">Belirtilen view dosyası bulunamadı: ' . $viewPath . '</div>';
                    }
                } catch (\Exception $e) {
                    return '<div class="alert alert-danger">View render hatası: ' . $e->getMessage() . '</div>';
                }
            }
            
            $html = $widget->content_html;
            $css = $widget->content_css;
            $js = $widget->content_js;
            $cssFiles = $widget->css_files ?? [];
            $jsFiles = $widget->js_files ?? [];
            
            $settings = $tenantWidget->settings ?? [];
            
            $items = [];
            if ($widget->has_items) {
                $items = $tenantWidget->items
                    ->where('content.is_active', true)
                    ->map(function ($item) {
                        return $item->content;
                    })->toArray();
                
                $html = $this->renderService->processItems($html, $items);
            }
            
            if ($widget->type === 'module') {
                $moduleItems = $this->getModuleData($widget->data_source, $settings);
                $html = $this->renderService->processModuleData($html, $moduleItems);
            }
            
            $html = $this->renderService->processVariables($html, $settings);
            $html = $this->renderService->processConditionalBlocks($html, $settings);
        }
        
        $result = '';
        
        if (!empty($cssFiles)) {
            foreach ($cssFiles as $cssFile) {
                if (!empty($cssFile)) {
                    $result .= "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
                }
            }
        }
        
        if (!empty($css)) {
            $result .= "<style>{$css}</style>\n";
        }
        
        $result .= $html;
        
        if (!empty($jsFiles)) {
            foreach ($jsFiles as $jsFile) {
                if (!empty($jsFile)) {
                    $result .= "<script src=\"{$jsFile}\"></script>\n";
                }
            }
        }
        
        if (!empty($js)) {
            $result .= "<script>{$js}</script>\n";
        }
        
        return $result;
    }
    
    public function clearWidgetCache($tenantId = null, $widgetId = null): void
    {
        $this->cacheService->clearCache($tenantId, $widgetId);
    }
    
    /**
     * Modül verilerini al (önbelleksiz)
     * 
     * @param string|null $dataSource Veri kaynağı sınıfı yolu
     * @param array $settings Ayarlar
     * @return array Modül verileri
     */
    public function getModuleData($dataSource = null, array $settings = []): array
    {
        if (empty($dataSource)) {
            return [];
        }
        
        // İlk olarak tam sınıf yolu verilmişse direk kullan
        if (class_exists($dataSource)) {
            $module = new $dataSource();
            if (method_exists($module, 'getData')) {
                return $module->getData($settings);
            }
            return [];
        }
        
        // Modules/ öneki yoksa, resources/views/blocks/modules/ altında arama yap
        if (strpos($dataSource, 'Modules\\') !== 0) {
            // Dosya yolunu çıkar (page/recent)
            $parts = explode('/', $dataSource);
            if (count($parts) >= 2) {
                $moduleName = $parts[0]; // page, portfolio, vb.
                
                // Sınıf dosyasını kontrol et
                $moduleClassName = 'Modules\\WidgetManagement\\resources\\views\\blocks\\modules\\' . $moduleName . '\\' . ucfirst($moduleName) . 'Modules';
                
                if (class_exists($moduleClassName)) {
                    $module = new $moduleClassName();
                    if (method_exists($module, 'getData')) {
                        return $module->getData($settings);
                    }
                }
            }
        }
        
        return [];
    }
}