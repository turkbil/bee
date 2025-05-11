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
    protected $useHandlebars = false; // Handlebars kullanımını kapalı olarak ayarla
    
    // Yüklenen dosyaları ve içeriği takip etmek için statik değişkenler
    protected static $loadedCssFiles = [];
    protected static $loadedJsFiles = [];
    protected static $cssStyles = [];
    protected static $jsScripts = [];
    
    public function __construct(
        WidgetRenderService $renderService = null,
        WidgetCacheService $cacheService = null
    ) {
        $this->renderService = $renderService ?? new WidgetRenderService();
        $this->cacheService = $cacheService ?? new WidgetCacheService($this->cachePrefix);
        
        // Handlebars kullanımı için renderService'i ayarla
        if ($this->useHandlebars) {
            $this->renderService->setUseHandlebars(true);
        } else {
            $this->renderService->setUseHandlebars(false);
        }
    }
    
    // Önbellek kullanımını ayarla
    public function setCacheUsage($useCache)
    {
        $this->useCache = $useCache;
        return $this;
    }

    // Handlebars kullanımını ayarla
    public function setHandlebarsUsage($useHandlebars)
    {
        $this->useHandlebars = $useHandlebars;
        $this->renderService->setUseHandlebars($useHandlebars);
        return $this;
    }
    
    /**
     * Sayfa render edildiğinde eklenecek CSS dosyalarını döndürür
     * 
     * @return array CSS dosya ve stil listesi
     */
    public static function getStylesOutput(): string
    {
        $output = '';
        
        // CSS dosyalarını ekle
        foreach (self::$loadedCssFiles as $file) {
            $output .= '<link rel="stylesheet" href="' . $file . '">' . "\n";
        }
        
        // CSS içeriklerini ekle
        foreach (self::$cssStyles as $id => $style) {
            $output .= '<style id="' . $id . '">' . $style . '</style>' . "\n";
        }
        
        return $output;
    }
    
    /**
     * Sayfa render edildiğinde eklenecek JS dosyalarını döndürür
     * 
     * @return array JS dosya ve script listesi
     */
    public static function getScriptsOutput(): string
    {
        $output = '';
        
        // JS dosyalarını ekle
        foreach (self::$loadedJsFiles as $file) {
            $output .= '<script src="' . $file . '"></script>' . "\n";
        }
        
        // JS içeriklerini ekle
        foreach (self::$jsScripts as $id => $script) {
            $output .= '<script id="' . $id . '">' . $script . '</script>' . "\n";
        }
        
        return $output;
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
        // tenant()->id null durumunu önlemek için optional ile id al
        $tenantId = optional(tenant())->id ?? 0;
        $cacheKey = $this->cachePrefix . $tenantId . "_pos_{$position}";
        
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
        // tenant()->id null durumunu önlemek için optional ile id al
        $tenantId = optional(tenant())->id ?? 0;
        $cacheKey = $this->cachePrefix . $tenantId . "_widget_{$tenantWidget->id}";
        
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
            
            if ($widget->type === 'module' && !empty($widget->file_path)) {
                try {
                    $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
                    if (view()->exists($viewPath)) {
                        $settings = $tenantWidget->settings ?? [];
                        return view($viewPath, ['settings' => $settings, 'widget' => $widget])->render();
                    } else {
                        return '<div class="alert alert-danger">Belirtilen modül dosyası bulunamadı: ' . $viewPath . '</div>';
                    }
                } catch (\Exception $e) {
                    return '<div class="alert alert-danger">Modül render hatası: ' . $e->getMessage() . '</div>';
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
        
        // CSS dosyalarını global listeye ekle
        if (!empty($cssFiles)) {
            foreach ($cssFiles as $cssFile) {
                if (!empty($cssFile)) {
                    $this->addCssFile($cssFile);
                }
            }
        }
        
        // CSS içeriğini global listeye ekle
        if (!empty($css)) {
            $styleId = 'widget-style-' . $tenantWidget->id;
            $this->addCssStyle($css, $styleId);
        }
        
        // JS dosyalarını global listeye ekle
        if (!empty($jsFiles)) {
            foreach ($jsFiles as $jsFile) {
                if (!empty($jsFile)) {
                    $this->addJsFile($jsFile);
                }
            }
        }
        
        // JS içeriğini global listeye ekle
        if (!empty($js)) {
            $scriptId = 'widget-script-' . $tenantWidget->id;
            $this->addJsScript($js, $scriptId);
        }
        
        return $html;
    }
    
    /**
     * CSS dosyasını global listeye ekler
     * 
     * @param string $file Dosya yolu
     * @return void
     */
    protected function addCssFile(string $file): void
    {
        // CSS dosyası daha önce yüklendi mi kontrol et
        if (in_array($file, self::$loadedCssFiles)) {
            return; // Zaten yüklenmiş, tekrar ekleme
        }
        
        // Dosya yolunu tam URL'ye dönüştür
        if (!preg_match('/^https?:\/\//', $file)) {
            $file = cdn($file);
        }
        
        // Yüklenen dosyalar listesine ekle
        self::$loadedCssFiles[] = $file;
    }
    
    /**
     * CSS içeriğini global listeye ekler
     * 
     * @param string $css CSS içeriği
     * @param string $id Benzersiz ID
     * @return void
     */
    protected function addCssStyle(string $css, string $id): void
    {
        // Boş CSS kontrolü
        if (empty(trim($css))) {
            return;
        }
        
        // CSS içeriğini global listeye ekle
        self::$cssStyles[$id] = $css;
    }
    
    /**
     * JS dosyasını global listeye ekler
     * 
     * @param string $file Dosya yolu
     * @return void
     */
    protected function addJsFile(string $file): void
    {
        // JS dosyası daha önce yüklendi mi kontrol et
        if (in_array($file, self::$loadedJsFiles)) {
            return; // Zaten yüklenmiş, tekrar ekleme
        }
        
        // Dosya yolunu tam URL'ye dönüştür
        if (!preg_match('/^https?:\/\//', $file)) {
            $file = cdn($file);
        }
        
        // Yüklenen dosyalar listesine ekle
        self::$loadedJsFiles[] = $file;
    }
    
    /**
     * JS içeriğini global listeye ekler
     * 
     * @param string $js JS içeriği
     * @param string $id Benzersiz ID
     * @return void
     */
    protected function addJsScript(string $js, string $id): void
    {
        // Boş JS kontrolü
        if (empty(trim($js))) {
            return;
        }
        
        // JS içeriğini global listeye ekle
        self::$jsScripts[$id] = $js;
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