<?php

namespace Modules\WidgetManagement\app\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
    protected $useCache = true; // Cache AÇIK (performans iyileştirmesi)
    protected $useHandlebars = false;
    
    protected static $loadedCssFiles = [];
    protected static $loadedJsFiles = [];
    protected static $cssStyles = [];
    protected static $jsScripts = [];
    
    public function __construct(
        ?WidgetRenderService $renderService = null,
        ?WidgetCacheService $cacheService = null
    ) {
        $this->renderService = $renderService ?? new WidgetRenderService();
        $this->cacheService = $cacheService ?? new WidgetCacheService($this->cachePrefix);
        
        if ($this->useHandlebars) {
            $this->renderService->setUseHandlebars(true);
        } else {
            $this->renderService->setUseHandlebars(false);
        }
    }
    
    public function setCacheUsage($useCache)
    {
        $this->useCache = $useCache;
        return $this;
    }

    public function setHandlebarsUsage($useHandlebars)
    {
        $this->useHandlebars = $useHandlebars;
        $this->renderService->setUseHandlebars($useHandlebars);
        return $this;
    }
    
    /**
     * Sayfa render edildiğinde eklenecek CSS stillerini döndürür
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
        $tenantId = optional(tenant())->id ?? 0;
        $cacheKey = $this->cachePrefix . $tenantId . "_widget_{$tenantWidget->id}";
        
        if ($this->useCache) {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($tenantWidget) {
                return $this->processWidget($tenantWidget);
            });
        }
        
        return $this->processWidget($tenantWidget);
    }
                
    /**
     * Widget HTML içeriğini render et
     * 
     * @param Widget $widget Widget modeli
     * @param array $context Context değişkenleri
     * @param bool $useHandlebars Handlebars kullanılsın mı
     * @return string Render edilmiş HTML içerik
     */
    public function renderWidgetHtml($widget, array $context = [], bool $useHandlebars = false): string
    {
        // Handlebars kullanımını ayarla
        $this->setHandlebarsUsage($useHandlebars);
        
        $html = $widget->content_html ?? '';
        
        // Önce HTML içinde bulunan bağlantı ve script etiketlerini çıkar
        $html = preg_replace('/<link[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/i', '', $html);
        $html = preg_replace('/<script[^>]+src=[\'"]([^\'"]+)[\'"][^>]*><\/script>/i', '', $html);
        
        // Widget tipi ve içeriğine göre render işlemini gerçekleştir
        if ($widget->has_items && isset($context['items']) && !empty($context['items']) && is_array($context['items'])) {
            // Dinamik widget için items işleme
            $html = $this->renderService->processItems($html, $context['items']);
        }
        
        // Context içinde widget. önekli değişkenleri çıkar ve düzenle
        $widgetSettings = [];
        
        if (isset($context['widget']) && is_array($context['widget'])) {
            $widgetSettings = $context['widget'];
        } else {
            foreach ($context as $key => $value) {
                if (strpos($key, 'widget.') === 0) {
                    $newKey = str_replace('widget.', '', $key);
                    $widgetSettings[$newKey] = $value;
                }
            }
        }
        
        // Handlebars template değişkenlerini işle
        $html = $this->renderHandlebarsTemplate($html, array_merge($context, ['widget' => $widgetSettings]));
        
        return $html;
    }

    /**
     * Handlebars template değişkenlerini işle
     * 
     * @param string $template Handlebars template
     * @param array $context Template değişkenleri
     * @return string İşlenmiş içerik
     */
    protected function renderHandlebarsTemplate(string $template, array $context = []): string
    {
        // {{variable}} formatındaki değişkenleri değiştir
        $template = preg_replace_callback('/\{\{([^#\/][^\}]*?)\}\}/m', function($matches) use ($context) {
            $key = trim($matches[1]);
            
            // {{widget.var}} formatında mı?
            if (strpos($key, 'widget.') === 0) {
                $widgetKey = str_replace('widget.', '', $key);
                if (isset($context['widget'][$widgetKey])) {
                    return $context['widget'][$widgetKey];
                }
            }
            
            // Normal değişken mi?
            if (isset($context[$key])) {
                if (is_scalar($context[$key])) {
                    return $context[$key];
                }
                return json_encode($context[$key]);
            }
            
            // Değişken hiyerarşisinde mi? (object.property)
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $value = $context;
                
                foreach ($parts as $part) {
                    if (isset($value[$part])) {
                        $value = $value[$part];
                    } else {
                        return ''; // Değişken bulunamadı
                    }
                }
                
                if (is_scalar($value)) {
                    return $value;
                } elseif (is_array($value)) {
                    return json_encode($value);
                }
            }
            
            return ''; // Değişken bulunamadı
        }, $template);
        
        // {{#if variable}} Koşullu blokları işle
        $template = preg_replace_callback('/\{\{#if\s+([^\}]+)\}\}(.*?)(?:\{\{else\}\}(.*?))?\{\{\/if\}\}/s', function($matches) use ($context) {
            $condition = trim($matches[1]);
            $ifContent = $matches[2];
            $elseContent = isset($matches[3]) ? $matches[3] : '';
            
            // Koşul widget. önekli mi?
            if (strpos($condition, 'widget.') === 0) {
                $widgetKey = str_replace('widget.', '', $condition);
                if (isset($context['widget'][$widgetKey]) && $context['widget'][$widgetKey]) {
                    return $ifContent;
                }
            } else {
                // Normal koşul değişkeni
                if (isset($context[$condition]) && $context[$condition]) {
                    return $ifContent;
                }
                
                // Koşul hiyerarşisinde mi? (object.property)
                if (strpos($condition, '.') !== false) {
                    $parts = explode('.', $condition);
                    $value = $context;
                    
                    foreach ($parts as $part) {
                        if (isset($value[$part])) {
                            $value = $value[$part];
                        } else {
                            return $elseContent; // Değişken bulunamadı, else içeriğini döndür
                        }
                    }
                    
                    // Değer bulundu mu?
                    if ($value) {
                        return $ifContent;
                    }
                }
            }
            
            return $elseContent; // Koşul sağlanmadı, else içeriğini döndür
        }, $template);
        
        // {{#each}} döngülerini işle (basit düzeyde)
        $template = preg_replace_callback('/\{\{#each\s+items\}\}(.*?)\{\{\/each\}\}/s', function($matches) use ($context) {
            $itemTemplate = $matches[1];
            $result = '';
            
            if (isset($context['items']) && is_array($context['items'])) {
                foreach ($context['items'] as $item) {
                    // Her öğe için template'i kopyala ve değişkenleri değiştir
                    $itemHtml = $itemTemplate;
                    
                    // {{title}}, {{description}} gibi değişkenleri değiştir
                    $itemHtml = preg_replace_callback('/\{\{([^#\/][^\}]*?)\}\}/m', function($varMatches) use ($item) {
                        $key = trim($varMatches[1]);
                        
                        if (isset($item[$key])) {
                            if (is_scalar($item[$key])) {
                                return $item[$key];
                            }
                            return json_encode($item[$key]);
                        }
                        
                        return '';
                    }, $itemHtml);
                    
                    // İçeriğe ekle
                    $result .= $itemHtml;
                }
            }
            
            return $result;
        }, $template);
        
        return $template;
    }

    public static function resetAssets(): void
    {
        self::$loadedCssFiles = [];
        self::$loadedJsFiles = [];
        self::$cssStyles = [];
        self::$jsScripts = [];
    }

    public function processWidget(TenantWidget $tenantWidget): string
    {
    
        if ($tenantWidget->is_custom) {
            $html = $tenantWidget->custom_html;
            $css = $tenantWidget->custom_css;
            $js = $tenantWidget->custom_js;
            $cssFiles = [];
            $jsFiles = [];
            $settings = $tenantWidget->settings ?? [];
            
            // HTML içindeki link etiketlerini topla ve kaldır
            $html = preg_replace_callback('/<link\s+[^>]*href=(["\'])([^"\']+)\\1[^>]*>/i', function($matches) {
                $url = $matches[2];
                // HTTP/HTTPS kontrolü
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                $this->addCssFile($url);
                return ''; // Etiketi kaldır
            }, $html);
            
            // HTML içindeki script etiketlerini topla ve kaldır (src özelliği olanlar)
            $html = preg_replace_callback('/<script\s+[^>]*src=(["\'])([^"\']+)\\1[^>]*><\/script>/i', function($matches) {
                $url = $matches[2];
                // HTTP/HTTPS kontrolü
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                $this->addJsFile($url);
                return ''; // Etiketi kaldır
            }, $html);
            
            $html = preg_replace_callback('/<img\s+[^>]*src=(["\'])([^"\']+)\\1/i', function($matches) {
                $url = $matches[2];
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                return str_replace($matches[2], $url, $matches[0]);
            }, $html);
            
            $html = $this->renderService->processVariables($html, $settings);
    
            
            // CSS değişkenlerini işle
            if (!empty($css)) {
                $css = $this->renderService->processVariables($css, $settings);
            }
            
            // JS değişkenlerini işle
            if (!empty($js)) {
                $js = $this->renderService->processVariables($js, $settings);
            }
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
                    Log::error('Widget render hatası: ' . $e->getMessage(), ['exception' => $e, 'widget_id' => $widget->id, 'file_path' => $widget->file_path]);
                    return '<div class="alert alert-danger">View render hatası: ' . $e->getMessage() . '</div>';
                }
            }
            
            if ($widget->type === 'module' && !empty($widget->file_path)) {
                try {
                    $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
                    if (view()->exists($viewPath)) {
                        $settings = $tenantWidget->settings ?? [];
                        // Blade bölüm (section) hatalarını önlemek için renderViewWithoutSections kullanıyoruz
                        $view = view($viewPath, ['settings' => $settings, 'widget' => $widget]);
                        return $this->renderViewWithoutSections($view);
                    } else {
                        return '<div class="alert alert-danger">Belirtilen modül dosyası bulunamadı: ' . $viewPath . '</div>';
                    }
                } catch (\Exception $e) {
                    Log::error('Widget render hatası: ' . $e->getMessage(), ['exception' => $e, 'widget_id' => $widget->id, 'file_path' => $widget->file_path]);
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
                    ->filter(function ($item) {
                        return !isset($item->content['is_active']) || $item->content['is_active'] === true;
                    })
                    ->map(function ($item) {
                        $content = $item->content;
                        if (isset($content['image']) && !preg_match('/^https?:\/\//', $content['image'])) {
                            $content['image'] = cdn($content['image']);
                        }
                        return $content;
                    })->toArray();
                
                $html = $this->renderService->processItems($html, $items);
                Log::debug('[WidgetService] processWidget: Standard widget HTML after processItems.', ['tenant_widget_id' => $tenantWidget->id, 'html' => Str::limit($html, 300)]);
                
                // Eğer CSS ve JS içerikleri varsa, onlar için de items işleme
                if (!empty($css)) {
                    $css = $this->renderService->processItems($css, $items);
                }
                
                if (!empty($js)) {
                    $js = $this->renderService->processItems($js, $items);
                }
            }
            
            if ($widget->type === 'module') {
                $moduleItems = $this->getModuleData($widget->data_source, $settings);
                $html = $this->renderService->processModuleData($html, $moduleItems);
                Log::debug('[WidgetService] processWidget: Standard widget HTML after processModuleData.', ['tenant_widget_id' => $tenantWidget->id, 'html' => Str::limit($html, 300)]);
                
                // Eğer CSS ve JS içerikleri varsa, onları da moduleItems için işle
                if (!empty($css)) {
                    $css = $this->renderService->processModuleData($css, $moduleItems);
                }
                
                if (!empty($js)) {
                    $js = $this->renderService->processModuleData($js, $moduleItems);
                }
            }
            
            // HTML içindeki link etiketlerini topla ve kaldır
            $html = preg_replace_callback('/<link\s+[^>]*href=(["\'])([^"\']+)\\1[^>]*>/i', function($matches) {
                $url = $matches[2];
                // HTTP/HTTPS kontrolü
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                $this->addCssFile($url);
                return ''; // Etiketi kaldır
            }, $html);
            
            // HTML içindeki script etiketlerini topla ve kaldır (src özelliği olanlar)
            $html = preg_replace_callback('/<script\s+[^>]*src=(["\'])([^"\']+)\\1[^>]*><\/script>/i', function($matches) {
                $url = $matches[2];
                // HTTP/HTTPS kontrolü
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                $this->addJsFile($url);
                return ''; // Etiketi kaldır
            }, $html);
            
            $html = preg_replace_callback('/<img\s+[^>]*src=(["\'])([^"\']+)\\1/i', function($matches) {
                $url = $matches[2];
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = cdn($url);
                }
                return str_replace($matches[2], $url, $matches[0]);
            }, $html);
            
            $html = $this->renderService->processVariables($html, $settings);
            $html = $this->renderService->processConditionalBlocks($html, $settings);
            Log::debug('[WidgetService] processWidget: Standard widget HTML after processConditionalBlocks.', ['tenant_widget_id' => $tenantWidget->id, 'html' => Str::limit($html, 300)]);
            
            // CSS ve JS değişkenlerini işle
            if (!empty($css)) {
                $css = $this->renderService->processVariables($css, $settings);
                $css = $this->renderService->processConditionalBlocks($css, $settings);
            }
            
            if (!empty($js)) {
                $js = $this->renderService->processVariables($js, $settings);
                $js = $this->renderService->processConditionalBlocks($js, $settings);
            }
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
        
        Log::debug('[WidgetService] processWidget Bitti. Dönen HTML (tam):', ['tenant_widget_id' => $tenantWidget->id, 'html' => $html]);
        return $html;
    }
    
    /**
     * CSS dosyasını global listeye ekler
     */
    protected function addCssFile(string $file): void
    {
        if (in_array($file, self::$loadedCssFiles)) {
            return;
        }
        
        if (!preg_match('/^https?:\/\//', $file)) {
            $file = cdn($file);
        }
        
        self::$loadedCssFiles[] = $file;
    }
    
    /**
     * CSS içeriğini global listeye ekler
     */
    protected function addCssStyle(string $css, string $id): void
    {
        if (empty(trim($css))) {
            return;
        }
        
        self::$cssStyles[$id] = $css;
    }
    
    /**
     * JS dosyasını global listeye ekler
     */
    protected function addJsFile(string $file): void
    {
        if (in_array($file, self::$loadedJsFiles)) {
            return;
        }
        
        if (!preg_match('/^https?:\/\//', $file)) {
            $file = cdn($file);
        }
        
        self::$loadedJsFiles[] = $file;
    }
    
    /**
     * JS içeriğini global listeye ekler
     */
    protected function addJsScript(string $js, string $id): void
    {
        if (empty(trim($js))) {
            return;
        }
        
        self::$jsScripts[$id] = $js;
    }
    
    public function clearWidgetCache($tenantId = null, $widgetId = null): void
    {
        $this->cacheService->clearCache($tenantId, $widgetId);
    }
    
    /**
     * Modül verilerini al (önbelleksiz)
     */
    public function getModuleData($dataSource = null, array $settings = []): array
    {
        if (empty($dataSource)) {
            return [];
        }
        
        if (class_exists($dataSource)) {
            $module = new $dataSource();
            if (method_exists($module, 'getData')) {
                return $module->getData($settings);
            }
            return [];
        }
        
        if (strpos($dataSource, 'Modules\\') !== 0) {
            $parts = explode('/', $dataSource);
            if (count($parts) >= 2) {
                $moduleName = $parts[0];
                
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
    
    /**
     * Blade bölüm (section) hatalarını önlemek için view'ı özel bir şekilde render eder
     * Bu metod, view içindeki @section ve @endsection direktiflerinin ana şablonu etkilemesini engeller
     */
    protected function renderViewWithoutSections($view): string
    {
        try {
            // View'ın içeriğini almak için render() kullanıyoruz
            $content = $view->render();
            
            // Eğer içerik boşsa veya render sırasında hata oluştuysa
            if (empty($content)) {
                return '<div class="alert alert-warning">Widget içeriği boş</div>';
            }
            
            return $content;
        } catch (\Exception $e) {
            Log::error('Widget view render hatası: ' . $e->getMessage(), ['exception' => $e]);
            return '<div class="alert alert-danger">Widget render hatası: ' . $e->getMessage() . '</div>';
        }
    }
}