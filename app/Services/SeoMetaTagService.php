<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Route;
use App\Services\TenantCacheService;
use Illuminate\Database\Eloquent\Model;
use Modules\SeoManagement\app\Models\SeoSetting;
use App\Services\TenantLanguageProvider;

/**
 * Global SEO Meta Tag Service
 * 
 * Dinamik olarak route'tan model bilgisini alır ve
 * uygun SEO meta tag'lerini generate eder.
 */
readonly class SeoMetaTagService
{
    /**
     * Route'tan model tipini ve ID'sini algıla
     */
    public function detectModel(): ?Model
    {
        // Öncelikle view'dan share edilen model'i kontrol et
        $sharedData = view()->getShared();
        if (isset($sharedData['currentModel']) && $sharedData['currentModel'] instanceof Model) {
            return $sharedData['currentModel'];
        }
        
        $route = Route::current();
        
        // Homepage kontrolü
        if (($route && $route->getName() === 'home') || request()->path() === '/' || request()->path() === '') {
            return $this->getHomepageModel();
        }

        // Module index page kontrolü (yeni eklenen) - Route olmadan da çalışır
        $moduleIndex = $this->detectModuleIndex();
        if ($moduleIndex) {
            return $moduleIndex;
        }
        
        // Route yoksa sadece module index detection ile devam et
        if (!$route) {
            // Frontend slug bazlı arama yapmaya devam et
            return $this->detectBySlug();
        }

        // Route parametrelerini kontrol et
        $parameters = $route->parameters();
        
        // Admin panel route'ları için ID bazlı arama
        if (request()->is('admin/*')) {
            // Page modülü
            if (isset($parameters['id']) && request()->is('admin/page/*')) {
                $model = \Modules\Page\app\Models\Page::find($parameters['id']);
                if ($model) return $model;
            }
            
            // Portfolio modülü - SADECE TENANT CONTEXT'TE
            if (!\App\Helpers\TenantHelpers::isCentral() && isset($parameters['id']) && request()->is('admin/portfolio/manage/*')) {
                $model = \Modules\Portfolio\app\Models\Portfolio::find($parameters['id']);
                if ($model) return $model;
            }

            // Announcement modülü - SADECE TENANT CONTEXT'TE
            if (!\App\Helpers\TenantHelpers::isCentral() && isset($parameters['id']) && request()->is('admin/announcement/*')) {
                $model = \Modules\Announcement\app\Models\Announcement::find($parameters['id']);
                if ($model) return $model;
            }
            
            return null;
        }
        
        // Frontend - Slug bazlı arama
        $fullPath = request()->path();
        
        // URL'den locale'i tespit et
        $locale = 'tr'; // Varsayılan
        $segments = explode('/', $fullPath);
        
        // İlk segment dil kodu mu kontrol et - DİNAMİK
        $availableLocales = TenantLanguageProvider::getActiveLanguageCodes();
        if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
            $locale = $segments[0];
            // Dil prefix'ini kaldır
            $fullPath = implode('/', array_slice($segments, 1));
        }
        
        // Path'i parçala
        $parts = explode('/', $fullPath);
        $slug = $fullPath; // Varsayılan olarak tüm path
        
        // İlk parça modül slug'ı mı dinamik kontrol et
        if (count($parts) > 1) {
            $firstPart = $parts[0];
            
            // ModuleSlugService'den tüm modülleri al ve slug'larını kontrol et
            $allModules = \App\Services\ModuleSlugService::getAllModules();
            
            foreach ($allModules as $moduleName) {
                // Bu modül için index slug'ını al
                $moduleIndexSlug = \App\Services\ModuleSlugService::getSlugForLocale($moduleName, 'index', $locale);
                if (!$moduleIndexSlug) {
                    $moduleIndexSlug = \App\Services\ModuleSlugService::getSlug($moduleName, 'index');
                }
                
                // Show slug'ını da kontrol et (bazı modüller için show slug farklı olabilir)
                $moduleShowSlug = \App\Services\ModuleSlugService::getSlugForLocale($moduleName, 'show', $locale);
                if (!$moduleShowSlug) {
                    $moduleShowSlug = \App\Services\ModuleSlugService::getSlug($moduleName, 'show');
                }
                
                // İlk segment bu modülün slug'ı mı?
                if ($firstPart === $moduleIndexSlug || $firstPart === $moduleShowSlug) {
                    // Modül slug'ını çıkar, sadece content slug'ını al
                    $slug = implode('/', array_slice($parts, 1));
                    break;
                }
            }
        }
        
        // ÖNCELİKLE kategori kontrolü yap (3-segment URL'ler için)
        if (str_contains($slug, '/')) {
            $slugParts = explode('/', $slug);
            
            // category/web-tasarim formatını kontrol et
            if (count($slugParts) >= 2) {
                $possibleAction = $slugParts[0];  // 'category'
                $categorySlug = end($slugParts);  // 'web-tasarim'
                
                // Bilinen action'lar
                $knownActions = ['category', 'tag', 'author', 'type', 'label'];
                if (in_array($possibleAction, $knownActions)) {
                    // Portfolio kategori kontrolü - SADECE TENANT CONTEXT'TE
                    if (!\App\Helpers\TenantHelpers::isCentral()) {
                        $category = \Modules\Portfolio\app\Models\PortfolioCategory::where('is_active', true)
                            ->where(function($query) use ($categorySlug, $locale) {
                                $query->whereJsonContains('slug->' . $locale, $categorySlug)
                                      ->orWhere('slug', 'LIKE', '%"' . $categorySlug . '"%');
                            })
                            ->first();
                        if ($category) return $category;
                    }

                    // Announcement kategori kontrolü - SADECE TENANT CONTEXT'TE
                    if (!\App\Helpers\TenantHelpers::isCentral() && class_exists('\Modules\Announcement\app\Models\AnnouncementCategory')) {
                        $announcementCategory = \Modules\Announcement\app\Models\AnnouncementCategory::where('is_active', true)
                            ->where(function($query) use ($categorySlug, $locale) {
                                $query->whereJsonContains('slug->' . $locale, $categorySlug)
                                      ->orWhere('slug', 'LIKE', '%"' . $categorySlug . '"%');
                            })
                            ->first();
                        if ($announcementCategory) return $announcementCategory;
                    }
                }
            }
        }
        
        // Normal content arama (Page, Portfolio, Announcement)
        // Önce Page'lerde ara
        $page = \Modules\Page\app\Models\Page::where('is_active', true)
            ->where(function($query) use ($slug, $locale) {
                $query->whereJsonContains('slug->' . $locale, $slug)
                      ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
            })
            ->first();
        if ($page) return $page;

        // Portfolio'da ara - SADECE TENANT CONTEXT'TE
        if (!\App\Helpers\TenantHelpers::isCentral()) {
            $portfolio = \Modules\Portfolio\app\Models\Portfolio::where('is_active', true)
                ->where(function($query) use ($slug, $locale) {
                    $query->whereJsonContains('slug->' . $locale, $slug)
                          ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
                })
                ->first();
            if ($portfolio) return $portfolio;
        }

        // Announcement'ta ara - SADECE TENANT CONTEXT'TE
        if (!\App\Helpers\TenantHelpers::isCentral()) {
            $announcement = \Modules\Announcement\app\Models\Announcement::where('is_active', true)
                ->where(function($query) use ($slug, $locale) {
                    $query->whereJsonContains('slug->' . $locale, $slug)
                          ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
                })
                ->first();
            if ($announcement) return $announcement;
        }
        
        return null;
    }
    
    /**
     * Slug bazlı model detection (Route olmadan)
     */
    private function detectBySlug(): ?Model
    {
        // Frontend - Slug bazlı arama
        $fullPath = request()->path();
        
        // URL'den locale'i tespit et
        $locale = 'tr'; // Varsayılan
        $segments = explode('/', $fullPath);
        
        // İlk segment dil kodu mu kontrol et - DİNAMİK
        $availableLocales = TenantLanguageProvider::getActiveLanguageCodes();
        if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
            $locale = $segments[0];
            // Dil prefix'ini kaldır
            $fullPath = implode('/', array_slice($segments, 1));
        }
        
        // Path'i parçala
        $parts = explode('/', $fullPath);
        $slug = $fullPath; // Varsayılan olarak tüm path
        
        // İlk parça modül slug'ı mı dinamik kontrol et
        if (count($parts) > 1) {
            $firstPart = $parts[0];
            
            // ModuleSlugService'den tüm modülleri al ve slug'larını kontrol et
            $allModules = \App\Services\ModuleSlugService::getAllModules();
            
            foreach ($allModules as $moduleName) {
                // Bu modül için index slug'ını al
                $moduleIndexSlug = \App\Services\ModuleSlugService::getSlugForLocale($moduleName, 'index', $locale);
                if (!$moduleIndexSlug) {
                    $moduleIndexSlug = \App\Services\ModuleSlugService::getSlug($moduleName, 'index');
                }
                
                // Show slug'ını da kontrol et (bazı modüller için show slug farklı olabilir)
                $moduleShowSlug = \App\Services\ModuleSlugService::getSlugForLocale($moduleName, 'show', $locale);
                if (!$moduleShowSlug) {
                    $moduleShowSlug = \App\Services\ModuleSlugService::getSlug($moduleName, 'show');
                }
                
                // İlk segment bu modülün slug'ı mı?
                if ($firstPart === $moduleIndexSlug || $firstPart === $moduleShowSlug) {
                    // Modül slug'ını çıkar, sadece content slug'ını al
                    $slug = implode('/', array_slice($parts, 1));
                    break;
                }
            }
        }
        
        // Önce Page'lerde ara
        $page = \Modules\Page\app\Models\Page::where('is_active', true)
            ->where(function($query) use ($slug, $locale) {
                $query->whereJsonContains('slug->' . $locale, $slug)
                      ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
            })
            ->first();
        if ($page) return $page;
        
        // Portfolio'da ara
        $portfolio = \Modules\Portfolio\app\Models\Portfolio::where('is_active', true)
            ->where(function($query) use ($slug, $locale) {
                $query->whereJsonContains('slug->' . $locale, $slug)
                      ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
            })
            ->first();
        if ($portfolio) return $portfolio;
        
        // Announcement'ta ara
        $announcement = \Modules\Announcement\app\Models\Announcement::where('is_active', true)
            ->where(function($query) use ($slug, $locale) {
                $query->whereJsonContains('slug->' . $locale, $slug)
                      ->orWhere('slug', 'LIKE', '%"' . $slug . '"%');
            })
            ->first();
        if ($announcement) return $announcement;
        
        // Portfolio kategori kontrolü
        if (str_contains($slug, '/')) {
            $parts = explode('/', $slug);
            $categorySlug = end($parts);
            
            $category = \Modules\Portfolio\app\Models\PortfolioCategory::where('is_active', true)
                ->where(function($query) use ($categorySlug, $locale) {
                    $query->whereJsonContains('slug->' . $locale, $categorySlug)
                          ->orWhere('slug', 'LIKE', '%"' . $categorySlug . '"%');
                })
                ->first();
            if ($category) return $category;
        }
        
        return null;
    }
    
    /**
     * Homepage için model bul
     */
    private function getHomepageModel(): ?Model
    {
        // Homepage olarak işaretlenmiş page varsa
        $homePage = \Modules\Page\app\Models\Page::where('is_homepage', true)
            ->where('is_active', true)
            ->first();
            
        return $homePage;
    }
    
    /**
     * Module index page'leri için özel model oluştur
     */
    private function detectModuleIndex(): ?Model
    {
        $fullPath = request()->path();
        $locale = 'tr'; // Varsayılan
        $segments = explode('/', $fullPath);
        
        // İlk segment dil kodu mu kontrol et - DİNAMİK
        $availableLocales = TenantLanguageProvider::getActiveLanguageCodes();
        if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
            $locale = $segments[0];
            // Dil prefix'ini kaldır
            $fullPath = implode('/', array_slice($segments, 1));
        }
        
        // ModuleSlugService'den tüm modülleri al
        $allModules = \App\Services\ModuleSlugService::getAllModules();
        
        // Path'in module index page olup olmadığını kontrol et
        foreach ($allModules as $moduleName) {
            // Bu modül için slug'ı al
            $moduleSlug = \App\Services\ModuleSlugService::getSlugForLocale($moduleName, 'index', $locale);
            
            // Tam eşleşme kontrolü (sadece module slug'ı, alt sayfa değil)
            if ($fullPath === $moduleSlug) {
                // Özel module index model'i oluştur
                return $this->createModuleIndexModel($moduleName, $locale);
            }
        }
        
        return null;
    }
    
    /**
     * Module index için özel model oluştur
     */
    private function createModuleIndexModel(string $moduleName, string $locale): Model
    {
        // ModuleSlugService'ten modül title'ını al
        $moduleTitle = \App\Services\ModuleSlugService::getModuleName($moduleName, $locale);
        
        // Anonim model sınıfı oluştur (Module index için)
        return new class($moduleName, $moduleTitle, $locale) extends Model {
            protected $fillable = ['id', 'title', 'is_module_index', 'module_name', 'created_at', 'updated_at'];
            
            private string $moduleName;
            private string $moduleTitle;
            private string $locale;
            
            public function __construct(string $moduleName = '', string $moduleTitle = '', string $locale = 'tr')
            {
                // Boş constructor çağrısı için varsayılan değerler
                if (empty($moduleName)) {
                    parent::__construct();
                    return;
                }
                
                // Parent constructor'ı attributes array ile çağır
                parent::__construct([
                    'id' => 0,
                    'title' => json_encode([$locale => $moduleTitle]),
                    'is_module_index' => true,
                    'module_name' => $moduleName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->moduleName = $moduleName;
                $this->moduleTitle = $moduleTitle;
                $this->locale = $locale;
                
                // Exists flag'i true yap (veritabanından geliyormuş gibi)
                $this->exists = true;
            }
            
            public function getMorphClass(): string
            {
                return 'ModuleIndex';
            }
            
            public function getKey()
            {
                return "module_index_{$this->moduleName}";
            }
            
            public function getTranslated(string $field, string $locale): ?string
            {
                if ($field === 'title') {
                    return $this->moduleTitle;
                }
                return null;
            }
            
            public function seoSetting()
            {
                // GlobalSeoRepository'den module-based SEO ayarlarını al
                $seoRepository = app(\App\Contracts\GlobalSeoRepositoryInterface::class);
                $seoData = $seoRepository->getSeoSettings($this->moduleName, 'index');
                
                // SeoSetting benzeri bir obje oluştur
                return new class($seoData, $this->locale) {
                    private array $seoData;
                    private string $locale;
                    
                    public function __construct(array $seoData, string $locale)
                    {
                        $this->seoData = $seoData;
                        $this->locale = $locale;
                    }
                    
                    public function getTranslated(string $field, string $locale): ?string
                    {
                        $value = $this->seoData[$field][$locale] ?? null;
                        
                        // Array ise string'e çevir (keywords için)
                        if (is_array($value)) {
                            return implode(', ', $value);
                        }
                        
                        return $value;
                    }
                    
                    public function __get($name)
                    {
                        if ($name === 'og_titles' || $name === 'og_descriptions') {
                            return $this->seoData[$name] ?? null;
                        }
                        if ($name === 'twitter_title' || $name === 'twitter_description') {
                            return $this->seoData[$name] ?? null;
                        }
                        if ($name === 'robots') {
                            return $this->seoData[$name] ?? 'index, follow';
                        }
                        if ($name === 'og_image') {
                            return $this->seoData[$name] ?? null;
                        }
                        return null;
                    }
                    
                    public function hasDirectTitle(?string $locale = null): bool
                    {
                        $locale = $locale ?? $this->locale;
                        return !empty($this->seoData['title'][$locale] ?? '');
                    }
                    
                    public function getTitle(?string $locale = null): ?string
                    {
                        $locale = $locale ?? $this->locale;
                        return $this->seoData['title'][$locale] ?? null;
                    }
                    
                    public function hasDirectDescription(?string $locale = null): bool
                    {
                        $locale = $locale ?? $this->locale;
                        return !empty($this->seoData['description'][$locale] ?? '');
                    }
                    
                    public function getDescription(?string $locale = null): ?string
                    {
                        $locale = $locale ?? $this->locale;
                        return $this->seoData['description'][$locale] ?? null;
                    }
                };
            }
            
            public function __get($name)
            {
                if ($name === 'seoSetting') {
                    return $this->seoSetting();
                }
                return parent::__get($name);
            }
        };
    }
    
    /**
     * SEO Meta tag'lerini generate et
     */
    public function generateMetaTags(): array
    {
        $model = $this->detectModel();
        
        // URL'den locale'i tespit et (detectModel ile aynı mantık)
        $fullPath = request()->path();
        $locale = TenantLanguageProvider::getDefaultLanguageCode(); // Dinamik varsayılan
        $segments = explode('/', $fullPath);
        
        $availableLocales = TenantLanguageProvider::getActiveLanguageCodes();
        if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
            $locale = $segments[0];
        }
        
        $siteName = setting('site_title');
        
        
        $tenantCache = app(TenantCacheService::class);
        return $tenantCache->remember(
            TenantCacheService::PREFIX_SEO,
            $model ? "meta_{$model->getMorphClass()}_{$model->getKey()}_{$locale}" : "meta_general_{$locale}",
            TenantCacheService::TTL_HOUR,
            function() use ($model, $locale, $siteName) {
            $data = [
                'title' => $siteName,
                'description' => null,
                'keywords' => null,
                'canonical_url' => null,
                'author' => null,
                'publisher' => null,
                'copyright' => null,
                'og_titles' => null,
                'og_descriptions' => null,
                'og_image' => null,
                'og_type' => 'website',
                'og_locale' => null,
                'og_site_name' => null,
                // Article-specific OG tags (2025 SEO Standards)
                'article_published_time' => null,
                'article_modified_time' => null,
                'article_author' => null,
                'twitter_card' => 'summary',
                'twitter_title' => null,
                'twitter_description' => null,
                'twitter_image' => null,
                'twitter_site' => null,
                'twitter_creator' => null,
                'robots' => 'index, follow',
                'schema' => null,
            ];
            
            if (!$model) {
                return $data;
            }
            
            // Model'in SEO ayarlarını al
            $seoSetting = null;
            if (method_exists($model, 'seoSetting')) {
                $seoSetting = $model->seoSetting;
            }
            
            // 1. TITLE - Geliştirilmiş Hiyerarşi
            if ($seoSetting && $seoSetting->hasDirectTitle($locale) && $seoTitle = $seoSetting->getTitle($locale)) {
                // 1. Öncelik: SEO ayarlarındaki manuel title
                // Site name'i SettingManagement'ten al
                $settingSiteName = setting('site_name') ?: setting('site_title', $siteName) ?: '';
                $siteNameSafe = $siteName ?? '';

                // SEO title zaten site name içeriyorsa ekleme
                if (($settingSiteName && str_contains($seoTitle, $settingSiteName)) || ($siteNameSafe && str_contains($seoTitle, $siteNameSafe))) {
                    $data['title'] = $seoTitle;
                } else {
                    $data['title'] = $seoTitle . ' - ' . ($settingSiteName ?: $siteNameSafe ?: config('app.name'));
                }
            } elseif (method_exists($model, 'getTranslated') && $modelTitle = $model->getTranslated('title', $locale)) {
                // 2. Öncelik: Model'in title alanı + site name
                $settingSiteName = setting('site_name') ?: setting('site_title', $siteName) ?: config('app.name');
                $data['title'] = $modelTitle . ' - ' . $settingSiteName;
            } else {
                // 3. Fallback: Sadece site default title
                $data['title'] = setting('site_name') ?: setting('site_title', $siteName) ?: config('app.name');
            }
            
            // 2. DESCRIPTION - Geliştirilmiş Hiyerarşi
            if ($seoSetting && $seoSetting->hasDirectDescription($locale) && $seoDesc = $seoSetting->getDescription($locale)) {
                // 1. Öncelik: SEO ayarlarındaki manuel description
                $seoDesc = strip_tags($seoDesc);
                $seoDesc = preg_replace('/\s+/', ' ', $seoDesc);
                $seoDesc = trim($seoDesc);
                $data['description'] = $seoDesc;
            } elseif (method_exists($model, 'getSeoFallbackDescription') && $fallbackDesc = $model->getSeoFallbackDescription()) {
                // 2a. Öncelik: Model'in kendi getSeoFallbackDescription() metodu (Shop gibi özel modüller için)
                $data['description'] = $fallbackDesc;
            } elseif (method_exists($model, 'getTranslated') && $body = $model->getTranslated('body', $locale)) {
                // 2b. Öncelik: Model'in body alanından otomatik excerpt
                $cleanBody = strip_tags($body);
                $cleanBody = html_entity_decode($cleanBody, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $cleanBody = preg_replace('/\s+/', ' ', $cleanBody); // Çoklu boşlukları tek boşluğa çevir
                $cleanBody = trim($cleanBody);
                if (!empty($cleanBody)) {
                    $data['description'] = mb_substr($cleanBody, 0, 160);
                    if (mb_strlen($cleanBody) > 160) {
                        $data['description'] .= '...';
                    }
                }
            } else {
                // 3. Fallback: Setting'ten genel site açıklaması
                $fallbackDescription = setting('site_description') ?: setting('site_slogan');
                if ($fallbackDescription) {
                    $data['description'] = mb_substr(strip_tags($fallbackDescription), 0, 160);
                    if (mb_strlen($fallbackDescription) > 160) {
                        $data['description'] .= '...';
                    }
                }
            }
            
            // 3. KEYWORDS
            if ($seoSetting && $keywords = $seoSetting->getTranslated('keywords', $locale)) {
                // getTranslated might return JSON string for arrays
                if (is_string($keywords) && str_starts_with($keywords, '[')) {
                    $decoded = json_decode($keywords, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $keywords = $decoded;
                    }
                }
                $data['keywords'] = is_array($keywords) ? implode(', ', $keywords) : $keywords;
            }
            // Keywords fallback kaldırıldı - Google 2025 artık keywords kullanmıyor
            
            // 3.1. BASIC META FIELDS - Tenant bazlı sistem
            // Author: Öncelik seo_settings.author, fallback setting('site_author')
            $data['author'] = ($seoSetting && $seoSetting->author)
                ? $seoSetting->author
                : setting('site_author', setting('site_title', $siteName));

            // Publisher: site_title kullan (publisher kolonu kaldırıldı)
            $data['publisher'] = setting('site_title', $siteName);
            
            // Copyright - otomatik çok dilli oluşturma
            $copyright = self::generateAutomaticCopyright($siteName, $locale);
            $data['copyright'] = ($seoSetting && $seoSetting->copyright) ? $seoSetting->copyright : $copyright;
            
            // 3.5. CANONICAL URL
            // Öncelik 1: Controller'dan share edilen customCanonicalUrl (homepage slug redirect problemi için)
            $sharedData = view()->getShared();
            if (isset($sharedData['customCanonicalUrl']) && !empty($sharedData['customCanonicalUrl'])) {
                $data['canonical_url'] = $sharedData['customCanonicalUrl'];
            } elseif ($seoSetting && isset($seoSetting->canonical_url) && !empty($seoSetting->canonical_url)) {
                // Öncelik 2: SEO ayarlarındaki manuel canonical URL
                $data['canonical_url'] = $seoSetting->canonical_url;
            } else {
                // Öncelik 3: Primary Domain bazlı canonical URL (www karışıklığını önler)
                try {
                    // Tenant context'te isek ve primary domain varsa onu kullan
                    if (function_exists('tenant') && tenant()) {
                        $primaryDomain = \App\Models\Domain::getPrimaryDomain(tenant()->id);
                        if ($primaryDomain) {
                            // Primary domain üzerinden canonical URL oluştur
                            $data['canonical_url'] = 'https://' . $primaryDomain->domain . request()->getRequestUri();
                        } else {
                            // Fallback: Mevcut URL
                            $data['canonical_url'] = url()->current();
                        }
                    } else {
                        // Central domain: Mevcut URL
                        $data['canonical_url'] = url()->current();
                    }
                } catch (\Exception $e) {
                    // Hata durumunda fallback
                    $data['canonical_url'] = url()->current();
                }
            }
            
            // 4. OPEN GRAPH - Geliştirilmiş Hiyerarşi
            // og:title - Özel fallback hiyerarşisi
            if ($seoSetting) {
                $ogTitleField = $seoSetting->og_titles;
                if (is_array($ogTitleField) && isset($ogTitleField[$locale])) {
                    // 1. Öncelik: OG'ye özel tanımlanmış title
                    $data['og_titles'] = $ogTitleField[$locale];
                } elseif (is_string($ogTitleField) && !empty($ogTitleField)) {
                    // 1.1. Öncelik: OG'ye özel tanımlanmış title (string format)
                    $data['og_titles'] = $ogTitleField;
                }
            }
            if (empty($data['og_titles'])) {
                // 2. Fallback: Normal SEO title kullan
                $data['og_titles'] = $data['title'];
            }
            
            // og:description - Özel fallback hiyerarşisi
            if ($seoSetting) {
                $ogDescField = $seoSetting->og_descriptions;
                if (is_array($ogDescField) && isset($ogDescField[$locale])) {
                    // 1. Öncelik: OG'ye özel tanımlanmış description
                    $data['og_descriptions'] = $ogDescField[$locale];
                } elseif (is_string($ogDescField) && !empty($ogDescField)) {
                    // 1.1. Öncelik: OG'ye özel tanımlanmış description (string format)
                    $data['og_descriptions'] = $ogDescField;
                }
            }
            if (empty($data['og_descriptions'])) {
                // 2. Fallback: Normal SEO description kullan (zaten geliştirilmiş hiyerarşiye sahip)
                $data['og_descriptions'] = $data['description'];
            }
            
            // og:image - 2025 Optimized Priority with Multi-Collection Fallback
            $featuredImage = null;
            $allImages = []; // Tüm görselleri topla (Schema için)

            if ($seoSetting && $seoSetting->og_image) {
                // 1. Öncelik: SEO Settings og_image
                $featuredImage = cdn($seoSetting->og_image);
                $allImages[] = $featuredImage;
            } elseif (method_exists($model, 'getFirstMediaUrl')) {
                // 2. Öncelik: Media Library - Collection priority chain
                // 🎯 STANDART: 'hero' → Ana görsel (SEO, card, detail hero)
                $collectionPriority = [
                    'hero',            // 🎯 STANDART: Ana görsel (yeni sistem)
                    'featured_image',  // Legacy uyumluluk (eski sistem)
                    'gallery',         // Gallery ilk görsel
                    'product_images',  // Shop modülü legacy
                    'images',          // Genel images collection
                    'default',         // Default collection
                ];

                foreach ($collectionPriority as $collection) {
                    $mediaUrl = $model->getFirstMediaUrl($collection);
                    if (!empty($mediaUrl)) {
                        $featuredImage = $mediaUrl;
                        $allImages[] = $featuredImage;

                        // Bu collection'daki diğer görselleri de ekle (Schema için)
                        if (method_exists($model, 'getMedia')) {
                            $collectionMedia = $model->getMedia($collection);
                            foreach ($collectionMedia as $media) {
                                $allImages[] = $media->getUrl();
                            }
                        }
                        break; // İlk bulunan collection'ı kullan
                    }
                }
            }

            // Fallback: Site logo (LogoService kullan)
            if (!$featuredImage) {
                $logoService = app(\App\Services\LogoService::class);
                $logoUrl = $logoService->getSchemaLogoUrl();

                if ($logoUrl) {
                    $featuredImage = $logoUrl;
                    $allImages[] = $featuredImage;
                } else {
                    // Ultimate fallback
                    $featuredImage = asset('logo.png');
                    $allImages[] = $featuredImage;
                }
            }

            $data['og_image'] = $featuredImage;
            $data['all_images'] = array_unique($allImages); // Schema için kullanılacak
            
            // og:type - Model tipine göre
            $data['og_type'] = match($model->getMorphClass()) {
                'Modules\Portfolio\app\Models\Portfolio' => 'article',
                'Modules\Announcement\app\Models\Announcement' => 'article',
                default => 'website'
            };

            // Article-specific OG tags (NewsArticle, BlogPosting için - 2025 SEO Standards)
            if ($data['og_type'] === 'article') {
                // article:published_time
                if (isset($model->created_at)) {
                    $data['article_published_time'] = $model->created_at->toIso8601String();
                }

                // article:modified_time
                if (isset($model->updated_at)) {
                    $data['article_modified_time'] = $model->updated_at->toIso8601String();
                }

                // article:author - VARCHAR kolonu (tek değer, dil bazlı değil)
                // Öncelik: seo_settings.author, fallback: setting('site_author')
                $data['article_author'] = ($seoSetting && $seoSetting->author)
                    ? $seoSetting->author
                    : setting('site_author', setting('site_title', $siteName));
            }

            // og:locale - Setting sistemi entegrasyonu
            $data['og_locale'] = ($seoSetting && $seoSetting->og_locale) 
                ? $seoSetting->og_locale 
                : str_replace('-', '_', $locale);
            
            // og:site_name - Site title'dan al
            $data['og_site_name'] = ($seoSetting && $seoSetting->og_site_name) 
                ? $seoSetting->og_site_name 
                : setting('site_title', $siteName);
            
            // 5. TWITTER CARDS
            $data['twitter_card'] = $data['og_image'] ? 'summary_large_image' : 'summary';
            
            if ($seoSetting && $seoSetting->twitter_title) {
                $data['twitter_title'] = $seoSetting->twitter_title;
            } else {
                $data['twitter_title'] = $data['og_titles'];
            }
            
            if ($seoSetting && $seoSetting->twitter_description) {
                $data['twitter_description'] = $seoSetting->twitter_description;
            } else {
                $data['twitter_description'] = $data['og_descriptions'];
            }
            
            $data['twitter_image'] = $data['og_image'];
            
            // Twitter additional fields - Tenant bazlı sistem
            $data['twitter_site'] = ($seoSetting && $seoSetting->twitter_site) 
                ? $seoSetting->twitter_site 
                : null;
                
            $data['twitter_creator'] = ($seoSetting && $seoSetting->twitter_creator) 
                ? $seoSetting->twitter_creator 
                : null;
            
            // 6. ROBOTS - 2025 Standards (robots_meta JSON array desteği)
            if ($seoSetting && isset($seoSetting->robots_meta)) {
                // Yeni robots_meta JSON array'ini kullan
                $robotsMeta = $seoSetting->robots_meta;
                $robotsDirectives = [];

                // Index/NoIndex
                $robotsDirectives[] = ($robotsMeta['index'] ?? true) ? 'index' : 'noindex';

                // Follow/NoFollow
                $robotsDirectives[] = ($robotsMeta['follow'] ?? true) ? 'follow' : 'nofollow';

                // Archive/NoArchive
                if (isset($robotsMeta['archive']) && !$robotsMeta['archive']) {
                    $robotsDirectives[] = 'noarchive';
                }

                // Snippet/NoSnippet
                if (isset($robotsMeta['snippet']) && !$robotsMeta['snippet']) {
                    $robotsDirectives[] = 'nosnippet';
                }

                $data['robots'] = implode(', ', $robotsDirectives);
            } elseif ($seoSetting && $seoSetting->robots) {
                // Eski robots kolonu ile geriye dönük uyumluluk
                $data['robots'] = $seoSetting->robots;
            } elseif (isset($model->is_active) && !$model->is_active) {
                // Pasif sayfalar için sıkı kısıtlama
                $data['robots'] = 'noindex, nofollow, noarchive, nosnippet, max-snippet:0, max-image-preview:none, max-video-preview:0';
            } else {
                // Default: hepsi açık
                $data['robots'] = 'index, follow';
            }
            
            // 7. SCHEMA.ORG (Multiple Schemas - 2025 Best Practice)
            $schemas = [];

            // 7.1. Ana Schema (NewsArticle/WebPage/etc)
            $schemas['main'] = $this->generateSchema($model, $data);

            // 7.2. WebSite Schema (Sadece Homepage)
            if ($this->isHomepage()) {
                $schemas['website'] = $this->generateWebSiteSchema($data);
            }

            // 7.3. Organization Schema (Global - Her Sayfada)
            $schemas['organization'] = $this->generateOrganizationSchema();

            // 7.4. ItemList Schema (Liste Sayfaları)
            if ($this->isIndexPage($model)) {
                $schemas['itemlist'] = $this->generateItemListSchema($model, $locale);
            }

            // 7.5. Breadcrumb Schema (Otomatik)
            $breadcrumbs = $this->generateAutoBreadcrumbs($model, $locale);
            if (!empty($breadcrumbs)) {
                $schemaGenerator = app(\Modules\SeoManagement\app\Services\SchemaGeneratorService::class);
                $schemas['breadcrumb'] = $schemaGenerator->generateBreadcrumbSchema($breadcrumbs);
            }

            $data['schemas'] = $schemas;
            
            // 8. HREFLANG URLS
            if ($model) {
                $data['hreflang'] = $this->generateHreflangLinks($model);
            }
            
            
            return $data;
        });
    }
    
    /**
     * Schema.org markup generate et - V2 Dinamik
     */
    private function generateSchema(Model $model, array $seoData): array
    {
        try {
            // Yeni dinamik schema generator'ı kullan
            $schemaGenerator = app(\Modules\SeoManagement\app\Services\SchemaGeneratorService::class);
            $currentLocale = app()->getLocale();
            
            $schema = $schemaGenerator->generateSchema($model, $currentLocale);
            
            // SEO ayarlarından gelen verilerle override et
            if (!empty($seoData['title'])) {
                $schema['name'] = $seoData['title'];
                $schema['headline'] = $seoData['title']; // Article types için
            }
            
            if (!empty($seoData['description'])) {
                $schema['description'] = $seoData['description'];
            }
            
            // Images - Tüm görselleri schema'ya ekle (2025 Best Practice)
            if (!empty($seoData['all_images']) && count($seoData['all_images']) > 0) {
                if (count($seoData['all_images']) === 1) {
                    // Tek görsel
                    $schema['image'] = [
                        '@type' => 'ImageObject',
                        'url' => $seoData['all_images'][0]
                    ];
                } else {
                    // Birden fazla görsel - Array olarak ekle
                    $schema['image'] = array_map(function($imageUrl) {
                        return [
                            '@type' => 'ImageObject',
                            'url' => $imageUrl
                        ];
                    }, $seoData['all_images']);
                }
            } elseif (!empty($seoData['og_image'])) {
                // Fallback: Sadece featured image
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $seoData['og_image']
                ];
            }
            
            return $schema;
            
        } catch (\Exception $e) {
            // Fallback - basit schema
            \Log::warning('Dinamik schema generation hatası, fallback kullanılıyor', [
                'error' => $e->getMessage(),
                'model' => get_class($model)
            ]);
            
            return [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $seoData['title'] ?? 'Content',
                'description' => $seoData['description'] ?? '',
                'url' => url()->current()
            ];
        }
    }
    
    /**
     * Hreflang link'lerini generate et
     */
    private function generateHreflangLinks(Model $model): array
    {
        try {
            // UnifiedUrlBuilderService kullan
            $urlBuilder = app(UnifiedUrlBuilderService::class);
            
            // URL'den action'ı dinamik tespit et
            $action = $this->detectActionFromCurrentUrl($model);
            
            return $urlBuilder->generateAlternateLinks($model, $action);
        } catch (\Exception $e) {
            \Log::error('SeoMetaTagService: Error generating hreflang links', [
                'model' => get_class($model),
                'model_id' => $model->getKey(),
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
    
    /**
     * Otomatik breadcrumb oluştur
     *
     * Google Best Practice (2025): Ana sayfa breadcrumb'a dahil edilmez
     * https://developers.google.com/search/docs/appearance/structured-data/breadcrumb
     */
    private function generateAutoBreadcrumbs(?Model $model, string $locale): array
    {
        if (!$model) {
            return [];
        }

        // Homepage ise breadcrumb gösterme (tek sayfa, breadcrumb anlamsız)
        if ($this->isHomepage()) {
            return [];
        }

        $breadcrumbs = [];
        $modelClass = get_class($model);

        // Google Best Practice: Ana sayfa dahil ETMİYORUZ
        // Direkt kategoriden başlıyoruz: Portfolio → Kategori → Ürün

        if (str_contains($modelClass, 'Page')) {
            // Page için sadece sayfa başlığı (eğer homepage değilse)
            // Breadcrumb göstermeye gerek yok - çünkü hiyerarşi yok
            return [];
        } elseif (str_contains($modelClass, 'Portfolio')) {
            // 1. Portfolio index
            $portfolioIndexText = __('Portfolio', [], $locale);
            $breadcrumbs[] = [
                'name' => $portfolioIndexText,
                'url' => url('/portfolio')
            ];

            // 2. Portfolio kategorisi varsa
            if (isset($model->category) && $model->category) {
                $categoryTitle = $model->category->getTranslated('title', $locale);
                $breadcrumbs[] = [
                    'name' => $categoryTitle,
                    'url' => url("/portfolio/category/{$model->category->id}")
                ];
            }

            // 3. Mevcut portfolio item
            if (method_exists($model, 'getTranslated')) {
                $currentTitle = $model->getTranslated('title', $locale);
                if ($currentTitle) {
                    $breadcrumbs[] = [
                        'name' => $currentTitle,
                        'url' => url()->current()
                    ];
                }
            }
        } elseif (str_contains($modelClass, 'PortfolioCategory')) {
            // 1. Portfolio index
            $portfolioIndexText = __('Portfolio', [], $locale);
            $breadcrumbs[] = [
                'name' => $portfolioIndexText,
                'url' => url('/portfolio')
            ];

            // 2. Mevcut kategori
            if (method_exists($model, 'getTranslated')) {
                $currentTitle = $model->getTranslated('title', $locale);
                if ($currentTitle) {
                    $breadcrumbs[] = [
                        'name' => $currentTitle,
                        'url' => url()->current()
                    ];
                }
            }
        } elseif (str_contains($modelClass, 'Announcement')) {
            // 1. Announcements index
            $announcementIndexText = __('Announcements', [], $locale);
            $breadcrumbs[] = [
                'name' => $announcementIndexText,
                'url' => url('/announcements')
            ];

            // 2. Mevcut announcement
            if (method_exists($model, 'getTranslated')) {
                $currentTitle = $model->getTranslated('title', $locale);
                if ($currentTitle) {
                    $breadcrumbs[] = [
                        'name' => $currentTitle,
                        'url' => url()->current()
                    ];
                }
            }
        }

        // Breadcrumb en az 2 seviye olmalı (Google tavsiyesi)
        // Tek seviyeli breadcrumb anlamsız
        if (count($breadcrumbs) < 2) {
            return [];
        }

        return $breadcrumbs;
    }

    /**
     * Otomatik çok dilli copyright oluştur - Public static metod
     */
    public static function generateAutomaticCopyright(?string $siteName, string $locale): string
    {
        $currentYear = date('Y');
        $siteName = $siteName ?? 'Site';

        // Çok dilli copyright metinleri
        $copyrightTexts = [
            'tr' => $currentYear . ' ' . $siteName . '. Tüm hakları saklıdır.',
            'en' => '© ' . $currentYear . ' ' . $siteName . '. All rights reserved.',
            'de' => '© ' . $currentYear . ' ' . $siteName . '. Alle Rechte vorbehalten.',
            'fr' => '© ' . $currentYear . ' ' . $siteName . '. Tous droits réservés.',
            'es' => '© ' . $currentYear . ' ' . $siteName . '. Todos los derechos reservados.',
            'it' => '© ' . $currentYear . ' ' . $siteName . '. Tutti i diritti riservati.',
            'ar' => '© ' . $currentYear . ' ' . $siteName . '. جميع الحقوق محفوظة.',
            'ru' => '© ' . $currentYear . ' ' . $siteName . '. Все права защищены.'
        ];
        
        return $copyrightTexts[$locale] ?? $copyrightTexts['tr'];
    }
    
    /**
     * Mevcut URL'den action'ı dinamik tespit et
     */
    private function detectActionFromCurrentUrl(Model $model): string
    {
        $fullPath = request()->path();
        $segments = explode('/', $fullPath);
        
        // Locale prefix'ini kaldır - DİNAMİK
        $availableLocales = TenantLanguageProvider::getActiveLanguageCodes();
        if (count($segments) > 0 && in_array($segments[0], $availableLocales)) {
            array_shift($segments);
        }
        
        // Model tipine göre action tespit et
        $modelClass = get_class($model);
        
        // Portfolio kategori kontrolü
        if (str_contains($modelClass, 'PortfolioCategory')) {
            return 'category';
        }
        
        // Announcement kategori kontrolü
        if (str_contains($modelClass, 'AnnouncementCategory')) {
            return 'category';
        }
        
        // URL segment analizi - 3 segment varsa muhtemelen category/tag yapısı
        if (count($segments) >= 3) {
            // portfolio/category/web-tasarim formatı
            $possibleAction = $segments[1] ?? '';
            
            // Bilinen action'lar
            $knownActions = ['category', 'tag', 'author', 'type', 'label'];
            if (in_array($possibleAction, $knownActions)) {
                return $possibleAction;
            }
        }
        
        // Varsayılan: show
        return 'show';
    }

    /**
     * Ana sayfada mı kontrol et
     */
    private function isHomepage(): bool
    {
        $currentUrl = url()->current();
        $homeUrl = url('/');

        // Locale'li ana sayfa kontrolü (tr/, en/, ar/)
        $defaultLocale = get_tenant_default_locale();
        $currentLocale = app()->getLocale();

        if ($currentLocale !== $defaultLocale) {
            $homeUrl = url('/' . $currentLocale);
        }

        return $currentUrl === $homeUrl || $currentUrl . '/' === $homeUrl || $currentUrl === $homeUrl . '/';
    }

    /**
     * Liste/Index sayfasında mı kontrol et
     */
    private function isIndexPage(Model $model): bool
    {
        $currentPath = request()->path();
        $modelClass = get_class($model);

        // Model class'ından modül adını çıkar
        if (str_contains($modelClass, 'Announcement')) {
            return str_contains($currentPath, 'announcement') && !str_contains($currentPath, '/announcement/');
        }

        if (str_contains($modelClass, 'Portfolio')) {
            return str_contains($currentPath, 'portfolio') && !str_contains($currentPath, '/portfolio/');
        }

        if (str_contains($modelClass, 'Page')) {
            // Page için özel durum - genelde tek sayfalar
            return false;
        }

        // Genel kural: URL'de modül adı var ama detay yok
        $urlSegments = explode('/', trim($currentPath, '/'));
        return count($urlSegments) === 1; // Sadece /announcement, /portfolio gibi
    }

    /**
     * WebSite Schema oluştur (Sadece Homepage)
     */
    private function generateWebSiteSchema(array $data): array
    {
        $currentLocale = app()->getLocale();
        $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name');
        $siteDescription = $data['description'] ?: setting('site_description') ?: '';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => $siteName,
            'description' => $siteDescription,
            'inLanguage' => $currentLocale,
        ];

        // Arama özelliği ekle (2025 Best Practice)
        $schema['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => url('/search?q={search_term_string}')
            ],
            'query-input' => 'required name=search_term_string'
        ];

        return $schema;
    }

    /**
     * Organization Schema oluştur (Global - Her Sayfada)
     */
    private function generateOrganizationSchema(): array
    {
        $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => $siteName,
            'url' => url('/'),
        ];

        // Logo ekle (LogoService kullan)
        $logoService = app(\App\Services\LogoService::class);
        $logoStructuredData = $logoService->getStructuredData();

        if ($logoStructuredData) {
            $schema['logo'] = $logoStructuredData;
        }

        // İletişim bilgileri
        $contactEmail = setting('contact_email');
        if ($contactEmail) {
            $schema['email'] = $contactEmail;
        }

        $contactPhone = setting('contact_phone');
        if ($contactPhone) {
            $schema['telephone'] = $contactPhone;
        }

        // Sosyal medya hesapları (2025 Best Practice)
        $socialLinks = [];
        $socialPlatforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];

        foreach ($socialPlatforms as $platform) {
            $socialUrl = setting('social_' . $platform);
            if ($socialUrl) {
                $socialLinks[] = $socialUrl;
            }
        }

        if (!empty($socialLinks)) {
            $schema['sameAs'] = $socialLinks;
        }

        return $schema;
    }

    /**
     * ItemList Schema oluştur (Liste Sayfaları için) - DİNAMİK
     */
    private function generateItemListSchema(Model $model, string $locale): array
    {
        $modelClass = get_class($model);
        $items = [];

        try {
            // Module index placeholder modelleri için gerçek kaynak modele yönlendir
            if (($model->is_module_index ?? false) && isset($model->module_name)) {
                $listItems = $this->resolveModuleIndexCollection($model->module_name, $locale);
            } else {
                // Dinamik olarak aynı model class'ından aktif kayıtları çek
                $listItems = $modelClass::query()
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }

            // Her item için ListItem oluştur
            foreach ($listItems as $index => $item) {
                // getUrl ve getTranslated methodları varsa kullan
                $url = method_exists($item, 'getUrl') ? $item->getUrl($locale) : url()->current();
                $name = method_exists($item, 'getTranslated')
                    ? $item->getTranslated('title', $locale)
                    : ($item->title ?? 'Item ' . ($index + 1));

                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'url' => $url,
                    'name' => $name
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('ItemList Schema generation hatası', [
                'error' => $e->getMessage(),
                'model' => $modelClass
            ]);
        }

        if (empty($items)) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $items
        ];
    }

    /**
     * Module index sayfası için gerçek kayıt koleksiyonunu döndür.
     */
    private function resolveModuleIndexCollection(string $moduleName, string $locale): \Illuminate\Support\Collection
    {
        return match ($moduleName) {
            'Blog' => \Modules\Blog\App\Models\Blog::query()
                ->published()
                ->with('category')
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get(),
            'Portfolio' => \Modules\Portfolio\App\Models\Portfolio::query()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'Announcement' => \Modules\Announcement\App\Models\Announcement::query()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'Page' => \Modules\Page\App\Models\Page::query()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            default => collect(),
        };
    }
}
