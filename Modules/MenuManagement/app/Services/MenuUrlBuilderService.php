<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Services;

use Illuminate\Support\Facades\Route;
use Nwidart\Modules\Facades\Module;
use App\Services\ModuleService;
use App\Services\UnifiedUrlBuilderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Menu URL Builder Service
 * 
 * Bu servis menu itemları için URL oluşturma ve çözümleme işlemlerini yönetir.
 * Modül bazlı dinamik URL'ler, sayfalar ve harici linkler için merkezi bir çözüm sunar.
 */
readonly class MenuUrlBuilderService
{
    public function __construct(
        private ModuleService $moduleService,
        private ?UnifiedUrlBuilderService $unifiedUrlBuilder = null
    ) {
        // Unified URL Builder is handled via dependency injection
    }
    /**
     * URL tipine göre URL oluştur
     */
    public function buildUrl(string $urlType, array $urlData, string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        try {
            // Unified URL Builder varsa onu kullan
            if ($this->unifiedUrlBuilder) {
                return match($urlType) {
                    'internal' => $this->unifiedUrlBuilder->buildUrlForPath($urlData['url'] ?? '/', $locale),
                    'external' => $this->buildExternalUrl($urlData),
                    'module' => $this->buildModuleUrlWithUnified($urlData, $locale),
                    default => '#'
                };
            }
            
            // Fallback: eski sistem
            return match($urlType) {
                'internal' => $this->buildInternalUrl($urlData),
                'external' => $this->buildExternalUrl($urlData),
                'module' => $this->buildModuleUrl($urlData, $locale),
                default => '#'
            };
        } catch (\Exception $e) {
            Log::error('MenuUrlBuilderService: URL build error', [
                'type' => $urlType,
                'data' => $urlData,
                'error' => $e->getMessage()
            ]);
            
            return '#';
        }
    }
    
    /**
     * Site içi URL oluştur (örn: /hakkimizda, /iletisim)
     */
    private function buildInternalUrl(array $urlData): string
    {
        $url = $urlData['url'] ?? '/';
        
        // URL'nin başında / yoksa ekle
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }
        
        return url($url);
    }
    
    /**
     * Harici URL oluştur (örn: https://google.com)
     */
    private function buildExternalUrl(array $urlData): string
    {
        $url = $urlData['url'] ?? '#';
        
        // http:// veya https:// ile başlamıyorsa ekle
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }
    
    /**
     * Modül bazlı URL oluştur - TAMAMEN DİNAMİK
     */
    private function buildModuleUrl(array $urlData, string $locale): string
    {
        $module = $urlData['module'] ?? null;
        $type = $urlData['type'] ?? 'list';
        $id = $urlData['id'] ?? null;
        $slug = $urlData['slug'] ?? null;
        
        if (!$module) {
            return '#';
        }
        
        // Page modülü için özel işlem
        if ($module === 'page' && $id) {
            return $this->buildPageUrl($id, $locale);
        }
        
        // Type'a göre dinamik URL oluşturma
        if ($type === 'list' || $type === 'index') {
            return $this->buildModuleListUrl($module, $locale);
        }
        
        // Detail tipinde ise
        if ($type === 'detail' || $type === 'show') {
            return $this->buildModuleDetailUrl($module, $id, $locale);
        }
        
        // Diğer tüm tipler için (category, tag, etiket, label vs.)
        // Bunlar genelde 3-segment URL'ler: module/action/slug
        return $this->buildModuleActionUrl($module, $type, $slug ?? $id, $locale);
    }
    
    /**
     * Page modülü için URL oluştur
     */
    private function buildPageUrl(int $pageId, string $locale): string
    {
        try {
            $pageClass = 'Modules\Page\App\Models\Page';
            if (class_exists($pageClass)) {
                $page = $pageClass::find($pageId);
                if ($page) {
                    $slug = $page->getTranslated('slug', $locale);
                    $defaultLocale = get_tenant_default_locale();
                    
                    // Varsayılan dil ise prefix yok
                    if ($locale === $defaultLocale) {
                        return url('/' . ltrim($slug, '/'));
                    } else {
                        // Diğer diller için dil prefix'i ekle
                        return url('/' . $locale . '/' . ltrim($slug, '/'));
                    }
                }
            }
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::buildPageUrl error: ' . $e->getMessage());
        }
        
        return '#';
    }
    
    /**
     * URL'den veri çıkar (tersine işlem)
     */
    public function parseUrl(string $url): array
    {
        // URL'i parse et
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        $host = $parsedUrl['host'] ?? '';
        
        // Harici URL kontrolü
        if ($host && $host !== request()->getHost()) {
            return [
                'type' => 'external',
                'data' => ['url' => $url]
            ];
        }
        
        // Site içi URL analizi
        $segments = array_filter(explode('/', $path));
        
        if (empty($segments)) {
            return [
                'type' => 'internal',
                'data' => ['url' => '/']
            ];
        }
        
        // Modül kontrolü
        $firstSegment = reset($segments);
        if ($this->isModule($firstSegment)) {
            return $this->parseModuleUrl($segments);
        }
        
        // Normal site içi URL
        return [
            'type' => 'internal',
            'data' => ['url' => $path]
        ];
    }
    
    /**
     * Modül URL'sini parse et
     */
    private function parseModuleUrl(array $segments): array
    {
        $module = array_shift($segments);
        
        if (empty($segments)) {
            return [
                'type' => 'module',
                'data' => [
                    'module' => $module,
                    'type' => 'list'
                ]
            ];
        }
        
        $nextSegment = array_shift($segments);
        
        if ($nextSegment === 'category' && !empty($segments)) {
            return [
                'type' => 'module',
                'data' => [
                    'module' => $module,
                    'type' => 'category',
                    'id' => array_shift($segments)
                ]
            ];
        }
        
        return [
            'type' => 'module',
            'data' => [
                'module' => $module,
                'type' => 'detail',
                'id' => $nextSegment
            ]
        ];
    }
    
    /**
     * Verilen ismin bir modül olup olmadığını kontrol et
     */
    private function isModule(string $name): bool
    {
        return Module::has(ucfirst($name));
    }
    
    /**
     * Kullanılabilir modülleri getir
     */
    public function getAvailableModules(): array
    {
        $modules = [];
        
        // Tenant ID'yi al
        $tenantId = tenant() ? tenant()->id : null;
        
        // ModuleService'ten tenant'a atanmış modülleri getir
        $tenantModules = $tenantId 
            ? $this->moduleService->getTenantModuleAssignments($tenantId)
            : $this->moduleService->getActiveModules();
        
        // Sadece content tipindeki modülleri filtrele
        $contentModules = $tenantModules->filter(function ($module) {
            return $module->type === 'content';
        });
        
        foreach ($contentModules as $module) {
            $moduleSlug = strtolower($module->name);
            
            // Nwidart Module paketi ile de kontrol et (aktif modül olmalı)
            if (!Module::has($module->name)) {
                continue;
            }
            
            // Her modülün desteklediği URL tiplerini belirle
            $urlTypes = $this->getModuleUrlTypes($moduleSlug);
            
            $modules[] = [
                'name' => $module->name,
                'slug' => $moduleSlug,
                'label' => $module->display_name ?? $module->name, // display_name kullan
                'url_types' => $urlTypes
            ];
        }
        
        return $modules;
    }
    
    /**
     * Modülün desteklediği URL tiplerini getir - DİNAMİK
     */
    private function getModuleUrlTypes(string $moduleSlug): array
    {
        $urlTypes = [];
        $moduleCapitalized = ucfirst($moduleSlug);
        
        // 1. Önce modülün config dosyasından kontrol et
        $configKey = "modules.{$moduleSlug}.menu_url_types";
        $configTypes = config($configKey);
        
        if ($configTypes && is_array($configTypes)) {
            return $configTypes;
        }
        
        // 2. Modülün kendi config dosyasından kontrol et
        $moduleConfigKey = "{$moduleSlug}::config.menu_url_types";
        $moduleConfigTypes = config($moduleConfigKey);
        
        if ($moduleConfigTypes && is_array($moduleConfigTypes)) {
            return $moduleConfigTypes;
        }
        
        // 3. Otomatik keşfet - Controller metodlarına bakarak
        return $this->discoverModuleUrlTypes($moduleSlug, $moduleCapitalized);
    }
    
    /**
     * Modül URL tiplerini otomatik keşfet
     */
    private function discoverModuleUrlTypes(string $moduleSlug, string $moduleCapitalized): array
    {
        $urlTypes = [];
        
        try {
            // Controller sınıfını kontrol et
            $controllerClass = "Modules\\{$moduleCapitalized}\\App\\Http\\Controllers\\Front\\{$moduleCapitalized}Controller";
            
            if (!class_exists($controllerClass)) {
                // Varsayılan: sadece liste sayfası
                return [['type' => 'list', 'label' => 'Liste Sayfası']];
            }
            
            $reflection = new \ReflectionClass($controllerClass);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            
            // Standart metodları kontrol et
            foreach ($methods as $method) {
                $methodName = $method->getName();
                
                switch ($methodName) {
                    case 'index':
                        $urlTypes[] = [
                            'type' => 'list',
                            'label' => __("{$moduleSlug}::admin.all_items") !== "{$moduleSlug}::admin.all_items" 
                                ? __("{$moduleSlug}::admin.all_items") 
                                : 'Tüm ' . $this->getModuleLabel($moduleCapitalized),
                            'needs_selection' => false
                        ];
                        break;
                        
                    case 'show':
                        $urlTypes[] = [
                            'type' => 'detail',
                            'label' => __("{$moduleSlug}::admin.item_detail") !== "{$moduleSlug}::admin.item_detail"
                                ? __("{$moduleSlug}::admin.item_detail")
                                : $this->getModuleLabel($moduleCapitalized) . ' Detay',
                            'needs_selection' => true
                        ];
                        break;
                        
                    case 'category':
                        // Kategori modeli var mı kontrol et
                        $categoryModel = "Modules\\{$moduleCapitalized}\\App\\Models\\{$moduleCapitalized}Category";
                        if (class_exists($categoryModel)) {
                            $urlTypes[] = [
                                'type' => 'category',
                                'label' => __("{$moduleSlug}::admin.category") !== "{$moduleSlug}::admin.category"
                                    ? __("{$moduleSlug}::admin.category")
                                    : 'Kategori',
                                'needs_selection' => true
                            ];
                        }
                        break;
                }
            }
            
            // Hiç URL tipi bulunamadıysa varsayılan ekle
            if (empty($urlTypes)) {
                $urlTypes[] = [
                    'type' => 'list',
                    'label' => 'Liste Sayfası',
                    'needs_selection' => false
                ];
            }
            
            return $urlTypes;
            
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::discoverModuleUrlTypes error: ' . $e->getMessage());
            
            // Hata durumunda varsayılan
            return [['type' => 'list', 'label' => 'Liste Sayfası']];
        }
    }
    
    /**
     * Modül etiketi getir (i18n desteği)
     */
    private function getModuleLabel(string $moduleName): string
    {
        $key = strtolower($moduleName) . '::admin.module_name';
        $translated = __($key);
        
        return $translated !== $key ? $translated : $moduleName;
    }
    
    /**
     * Modül içeriklerini getir (dropdown için) - DİNAMİK
     */
    public function getModuleContent(string $module, string $type = 'list'): array
    {
        $content = [];
        $locale = app()->getLocale();
        
        try {
            // Model class'ı bul
            $modelClass = $this->getModuleModelClass($module);
            
            if (!$modelClass) {
                return $content;
            }
            
            // Type'a göre işlem yap
            if ($type === 'category') {
                // Kategori modeli var mı kontrol et
                $categoryClass = $this->getModuleCategoryClass($module);
                
                if ($categoryClass && class_exists($categoryClass)) {
                    $items = $categoryClass::where('is_active', true)->get();
                    
                    foreach ($items as $item) {
                        $content[] = [
                            'id' => $item->getKey(),
                            'label' => $item->getTranslated('title', $locale),
                            'slug' => $item->getTranslated('slug', $locale)
                        ];
                    }
                }
            } elseif ($type === 'detail') {
                // Ana model'den içerikleri al
                if (class_exists($modelClass)) {
                    $items = $modelClass::where('is_active', true)->get();
                    
                    foreach ($items as $item) {
                        $content[] = [
                            'id' => $item->getKey(),
                            'label' => $item->getTranslated('title', $locale),
                            'slug' => $item->getTranslated('slug', $locale)
                        ];
                    }
                }
            }
            
            // Alfabetik sıralama
            if (!empty($content)) {
                usort($content, function($a, $b) {
                    return strcoll($a['label'], $b['label']);
                });
            }
            
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::getModuleContent error: ' . $e->getMessage());
        }
        
        return $content;
    }
    
    /**
     * Module'ün model class'ını getir
     */
    private function getModuleModelClass(string $module): ?string
    {
        $modelClassMap = Cache::remember('module_model_class_map', 3600, function () {
            return $this->discoverModuleModelClasses();
        });
        
        return $modelClassMap[strtolower($module)] ?? null;
    }
    
    /**
     * Module'ün category model class'ını getir
     */
    private function getModuleCategoryClass(string $module): ?string
    {
        $categoryClassMap = Cache::remember('module_category_class_map', 3600, function () {
            return $this->discoverModuleCategoryClasses();
        });
        
        return $categoryClassMap[strtolower($module)] ?? null;
    }
    
    /**
     * Category model class'larını keşfet
     */
    private function discoverModuleCategoryClasses(): array
    {
        $categoryClasses = [];
        $modulesPath = base_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return $categoryClasses;
        }
        
        $modules = array_filter(glob($modulesPath . '/*'), 'is_dir');
        
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $moduleNameLower = strtolower($moduleName);
            
            // Category model dosyasını bul
            $possiblePaths = [
                "{$modulePath}/app/Models/{$moduleName}Category.php",
                "{$modulePath}/App/Models/{$moduleName}Category.php",
                "{$modulePath}/Entities/{$moduleName}Category.php",
                "{$modulePath}/src/Models/{$moduleName}Category.php"
            ];
            
            foreach ($possiblePaths as $categoryPath) {
                if (file_exists($categoryPath)) {
                    $content = file_get_contents($categoryPath);
                    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
                        $namespace = $matches[1];
                        $fullClass = $namespace . '\\' . $moduleName . 'Category';
                        
                        if (class_exists($fullClass)) {
                            $categoryClasses[$moduleNameLower] = $fullClass;
                            break;
                        }
                    }
                }
            }
        }
        
        return $categoryClasses;
    }
    
    /**
     * Modül detay URL oluştur (slug tabanlı)
     */
    private function buildModuleDetailUrl(string $module, int $id, string $locale): string
    {
        try {
            $defaultLocale = get_tenant_default_locale();
            
            // Modül bazlı slug bulma
            $slug = $this->getModuleItemSlug($module, $id, $locale);
            
            if (!$slug) {
                return '#';
            }
            
            // Varsayılan dil ise prefix yok
            if ($locale === $defaultLocale) {
                return url('/' . ltrim($slug, '/'));
            } else {
                // Diğer diller için dil prefix'i ekle
                return url('/' . $locale . '/' . ltrim($slug, '/'));
            }
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::buildModuleDetailUrl error: ' . $e->getMessage());
            return '#';
        }
    }
    
    /**
     * Modül action URL oluştur - DİNAMİK (category, tag, etiket, label vs.)
     */
    private function buildModuleActionUrl(string $module, string $action, mixed $idOrSlug, string $locale): string
    {
        try {
            $defaultLocale = get_tenant_default_locale();
            
            // Modül slug'larını dinamik al - HER DİL İÇİN FARKLI OLABİLİR!
            $moduleService = app(\App\Services\ModuleSlugService::class);
            
            // Şu anki dil için slug'ları al
            $moduleSlug = $moduleService->getSlugForLocale($module, 'index', $locale);
            if (!$moduleSlug) {
                $moduleSlug = $moduleService->getSlug($module, 'index');
            }
            
            // Action slug'ını al (category, tag, etiket, label vs. ne olursa olsun)
            $actionSlug = $moduleService->getSlugForLocale($module, $action, $locale);
            if (!$actionSlug) {
                $actionSlug = $moduleService->getSlug($module, $action);
            }
            
            // Debug log
            logger('MenuUrlBuilderService::buildModuleActionUrl DEBUG', [
                'module' => $module,
                'action' => $action,
                'idOrSlug' => $idOrSlug,
                'locale' => $locale,
                'moduleSlug' => $moduleSlug,
                'actionSlug' => $actionSlug,
                'defaultLocale' => $defaultLocale
            ]);
            
            // İçerik slug'ını al
            $contentSlug = null;
            
            if (is_numeric($idOrSlug)) {
                // ID verilmişse, ilgili model'den slug'ı al
                $model = $this->getModuleActionModel($module, $action, (int)$idOrSlug);
                if ($model) {
                    // İLGİLİ DİL İÇİN slug'ı al
                    $contentSlug = $model->getTranslated('slug', $locale);
                    
                    // Eğer o dilde slug yoksa, default dildeki slug'ı kullan
                    if (!$contentSlug) {
                        $contentSlug = $model->getTranslated('slug', $defaultLocale);
                    }
                }
            } else {
                // Zaten slug verilmiş
                $contentSlug = $idOrSlug;
            }
            
            if (!$contentSlug) {
                // Model bulunamadıysa ID'yi kullan
                $contentSlug = (string)$idOrSlug;
            }
            
            // URL oluştur - module/action/content formatında
            if ($locale === $defaultLocale) {
                return url('/' . $moduleSlug . '/' . $actionSlug . '/' . ltrim($contentSlug, '/'));
            } else {
                return url('/' . $locale . '/' . $moduleSlug . '/' . $actionSlug . '/' . ltrim($contentSlug, '/'));
            }
            
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::buildModuleCategoryUrl error: ' . $e->getMessage());
            return '#';
        }
    }
    
    /**
     * Kategori modelini ID'den getir
     */
    private function getModuleCategoryModel(string $module, int $id): ?object
    {
        $categoryClass = $this->getModuleCategoryClass($module);
        
        if ($categoryClass && class_exists($categoryClass)) {
            return $categoryClass::find($id);
        }
        
        return null;
    }
    
    /**
     * Action için model getir - DİNAMİK (category, tag, etiket, label vs.)
     * 
     * @param string $module Module adı
     * @param string $action Action adı (category, tag, etiket, label, etc.)
     * @param int $id Model ID
     * @return object|null
     */
    private function getModuleActionModel(string $module, string $action, int $id): ?object
    {
        try {
            $moduleCapitalized = ucfirst($module);
            $actionCapitalized = ucfirst($action);
            
            // Action'a göre olası model isimleri
            $possibleModelNames = [
                // Standart pattern: ModuleAction (PortfolioCategory, PortfolioTag)
                $moduleCapitalized . $actionCapitalized,
                // Plural pattern: ModuleActions (PortfolioCategories, PortfolioTags)
                $moduleCapitalized . $actionCapitalized . 's',
                // Singular pattern: ModuleAction tekil hali
                $moduleCapitalized . rtrim($actionCapitalized, 's'),
                // Türkçe pattern: Kategori, Etiket vs.
                $moduleCapitalized . ucfirst(str_replace(['kategori', 'etiket'], ['Category', 'Tag'], $action)),
            ];
            
            // Her modül için olası namespace'ler
            $possibleNamespaces = [
                "Modules\\{$moduleCapitalized}\\App\\Models",
                "Modules\\{$moduleCapitalized}\\app\\Models",
                "Modules\\{$moduleCapitalized}\\Entities",
                "Modules\\{$moduleCapitalized}\\src\\Models"
            ];
            
            // Tüm kombinasyonları dene
            foreach ($possibleNamespaces as $namespace) {
                foreach ($possibleModelNames as $modelName) {
                    $fullClass = $namespace . '\\' . $modelName;
                    
                    if (class_exists($fullClass)) {
                        // Model'i bul ve döndür
                        $model = $fullClass::find($id);
                        if ($model) {
                            logger("MenuUrlBuilderService: Found action model", [
                                'module' => $module,
                                'action' => $action,
                                'class' => $fullClass,
                                'id' => $id
                            ]);
                            return $model;
                        }
                    }
                }
            }
            
            // Hiçbir model bulunamazsa loglayalım
            logger("MenuUrlBuilderService: No action model found", [
                'module' => $module,
                'action' => $action,
                'id' => $id,
                'tried_namespaces' => $possibleNamespaces,
                'tried_models' => $possibleModelNames
            ]);
            
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::getModuleActionModel error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Modül liste URL oluştur
     */
    private function buildModuleListUrl(string $module, string $locale): string
    {
        try {
            $defaultLocale = get_tenant_default_locale();
            
            // ModuleSlugService kullanarak doğru slug'ı al
            $moduleService = app(\App\Services\ModuleSlugService::class);
            $moduleSlug = $moduleService->getSlugForLocale($module, 'index', $locale);
            if (!$moduleSlug) {
                $moduleSlug = $moduleService->getSlug($module, 'index');
            }
            
            // Varsayılan dil ise prefix yok
            if ($locale === $defaultLocale) {
                return url('/' . $moduleSlug);
            } else {
                // Diğer diller için dil prefix'i ekle
                return url('/' . $locale . '/' . $moduleSlug);
            }
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::buildModuleListUrl error: ' . $e->getMessage());
            return '#';
        }
    }
    
    /**
     * Modül item slug'ını getir
     */
    private function getModuleItemSlug(string $module, int $id, string $locale): ?string
    {
        try {
            switch ($module) {
                case 'portfolio':
                    if (class_exists('Modules\\Portfolio\\App\\Models\\Portfolio')) {
                        $item = \Modules\Portfolio\App\Models\Portfolio::find($id);
                        return $item ? $item->getTranslated('slug', $locale) : null;
                    }
                    break;
                    
                case 'announcement':
                    if (class_exists('Modules\\Announcement\\App\\Models\\Announcement')) {
                        $item = \Modules\Announcement\App\Models\Announcement::find($id);
                        return $item ? $item->getTranslated('slug', $locale) : null;
                    }
                    break;
                    
                // Diğer modüller için benzer logic eklenebilir
            }
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::getModuleItemSlug error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Modül kategori slug'ını getir
     */
    private function getModuleCategorySlug(string $module, int $id, string $locale): ?string
    {
        try {
            switch ($module) {
                case 'portfolio':
                    if (class_exists('Modules\\Portfolio\\App\\Models\\PortfolioCategory')) {
                        $category = \Modules\Portfolio\App\Models\PortfolioCategory::find($id);
                        return $category ? $category->getTranslated('slug', $locale) : null;
                    }
                    break;
                    
                // Diğer modüller için benzer logic eklenebilir
            }
        } catch (\Exception $e) {
            logger('MenuUrlBuilderService::getModuleCategorySlug error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Unified URL Builder ile module URL oluştur - YENİ
     */
    private function buildModuleUrlWithUnified(array $urlData, string $locale): string
    {
        $module = $urlData['module'] ?? null;
        $type = $urlData['type'] ?? 'list';
        $id = $urlData['id'] ?? null;
        $slug = $urlData['slug'] ?? null;
        
        if (!$module) {
            return '#';
        }
        
        try {
            // Page modülü için özel işlem
            if ($module === 'page' && $id) {
                $pageClass = 'Modules\Page\App\Models\Page';
                if (class_exists($pageClass)) {
                    $page = $pageClass::find($id);
                    if ($page) {
                        return $this->unifiedUrlBuilder->buildUrlForModel($page, 'show', $locale);
                    }
                }
            }
            
            // Diğer modüller için
            switch ($type) {
                case 'detail':
                    if ($id) {
                        $model = $this->getModuleModel($module, $id);
                        if ($model) {
                            return $this->unifiedUrlBuilder->buildUrlForModel($model, 'show', $locale);
                        }
                    }
                    break;
                    
                case 'category':
                    if ($id || $slug) {
                        $params = $slug ? [$slug] : [$id];
                        return $this->unifiedUrlBuilder->buildUrlForModule($module, 'category', $params, $locale);
                    }
                    break;
                    
                case 'list':
                default:
                    return $this->unifiedUrlBuilder->buildUrlForModule($module, 'index', null, $locale);
            }
        } catch (\Exception $e) {
            Log::error('MenuUrlBuilderService: Unified URL build failed', [
                'module' => $module,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
        
        return '#';
    }
    
    /**
     * Module model'ini getir - DİNAMİK
     */
    private function getModuleModel(string $module, int $id): ?object
    {
        // Önce cache'den model class map'i al
        $modelClassMap = Cache::remember('module_model_class_map', 3600, function () {
            return $this->discoverModuleModelClasses();
        });
        
        $modelClass = $modelClassMap[$module] ?? null;
        
        if ($modelClass && class_exists($modelClass)) {
            return $modelClass::find($id);
        }
        
        return null;
    }
    
    /**
     * Tüm modüllerin model class'larını otomatik keşfet
     */
    private function discoverModuleModelClasses(): array
    {
        $modelClasses = [];
        $modulesPath = base_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return $modelClasses;
        }
        
        // Tüm modül klasörlerini tara
        $modules = array_filter(glob($modulesPath . '/*'), 'is_dir');
        
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $moduleNameLower = strtolower($moduleName);
            
            // Model dosyasını bul - standart yerlere bak
            $possiblePaths = [
                "{$modulePath}/app/Models/{$moduleName}.php",
                "{$modulePath}/App/Models/{$moduleName}.php",
                "{$modulePath}/Entities/{$moduleName}.php",
                "{$modulePath}/src/Models/{$moduleName}.php"
            ];
            
            foreach ($possiblePaths as $modelPath) {
                if (file_exists($modelPath)) {
                    // Namespace'i belirle
                    $content = file_get_contents($modelPath);
                    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
                        $namespace = $matches[1];
                        $fullClass = $namespace . '\\' . $moduleName;
                        
                        // Class'ın gerçekten var olduğunu kontrol et
                        if (class_exists($fullClass)) {
                            $modelClasses[$moduleNameLower] = $fullClass;
                            break;
                        }
                    }
                }
            }
        }
        
        return $modelClasses;
    }
}