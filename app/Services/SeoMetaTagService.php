<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
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
            
            // Portfolio modülü
            if (isset($parameters['id']) && request()->is('admin/portfolio/manage/*')) {
                $model = \Modules\Portfolio\app\Models\Portfolio::find($parameters['id']);
                if ($model) return $model;
            }
            
            // Announcement modülü
            if (isset($parameters['id']) && request()->is('admin/announcement/*')) {
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
                    // Portfolio kategori kontrolü
                    $category = \Modules\Portfolio\app\Models\PortfolioCategory::where('is_active', true)
                        ->where(function($query) use ($categorySlug, $locale) {
                            $query->whereJsonContains('slug->' . $locale, $categorySlug)
                                  ->orWhere('slug', 'LIKE', '%"' . $categorySlug . '"%');
                        })
                        ->first();
                    if ($category) return $category;
                    
                    // Announcement kategori kontrolü
                    if (class_exists('\Modules\Announcement\app\Models\AnnouncementCategory')) {
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
        
        \Log::info('SeoMetaTagService::generateMetaTags', [
            'model' => $model ? get_class($model) : 'null',
            'model_id' => $model ? $model->getKey() : 'null',
            'locale' => $locale,
            'siteName' => $siteName,
            'path' => request()->path()
        ]);
        
        // Cache key
        $cacheKey = $model 
            ? "seo_meta_{$model->getMorphClass()}_{$model->getKey()}_{$locale}"
            : "seo_meta_general_{$locale}";
            
        return Cache::remember($cacheKey, 3600, function() use ($model, $locale, $siteName) {
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
            
            // 1. TITLE
            if ($seoSetting && $seoSetting->hasDirectTitle($locale) && $seoTitle = $seoSetting->getTitle($locale)) {
                $data['title'] = $seoTitle . ' - ' . $siteName;
            } elseif (method_exists($model, 'getTranslated') && $modelTitle = $model->getTranslated('title', $locale)) {
                
                // DEBUG: Title translation'ı logla
                \Log::info('🐛 Title Translation Debug', [
                    'model_class' => get_class($model),
                    'model_id' => $model->getKey(),
                    'requested_locale' => $locale,
                    'raw_title' => $model->getRawOriginal('title'),
                    'translated_title' => $modelTitle,
                    'app_locale' => app()->getLocale(),
                    'full_path' => request()->path(),
                    'detected_locale_logic' => 'LOCALE_DEBUG_' . $locale
                ]);
                
                $data['title'] = $modelTitle . ' - ' . $siteName;
            }
            
            // 2. DESCRIPTION
            if ($seoSetting && $seoSetting->hasDirectDescription($locale) && $seoDesc = $seoSetting->getDescription($locale)) {
                // SEO description'ı temizle
                $seoDesc = strip_tags($seoDesc);
                $seoDesc = preg_replace('/\s+/', ' ', $seoDesc);
                $seoDesc = trim($seoDesc);
                $data['description'] = $seoDesc;
            } elseif (method_exists($model, 'getTranslated') && $body = $model->getTranslated('body', $locale)) {
                // Body'den excerpt al (HTML temizle, 160 karakter)
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
            
            // 3.1. BASIC META FIELDS
            if ($seoSetting && $seoSetting->author) {
                $data['author'] = $seoSetting->author;
            }
            
            if ($seoSetting && $seoSetting->publisher) {
                $data['publisher'] = $seoSetting->publisher;
            }
            
            if ($seoSetting && $seoSetting->copyright) {
                $data['copyright'] = $seoSetting->copyright;
            }
            
            // 3.5. CANONICAL URL
            if ($seoSetting && isset($seoSetting->canonical_url) && !empty($seoSetting->canonical_url)) {
                $data['canonical_url'] = $seoSetting->canonical_url;
            } else {
                // Varsayılan: mevcut URL
                $data['canonical_url'] = url()->current();
            }
            
            // 4. OPEN GRAPH
            // og:title
            if ($seoSetting) {
                $ogTitleField = $seoSetting->og_titles;
                if (is_array($ogTitleField) && isset($ogTitleField[$locale])) {
                    $data['og_titles'] = $ogTitleField[$locale];
                } elseif (is_string($ogTitleField) && !empty($ogTitleField)) {
                    $data['og_titles'] = $ogTitleField;
                }
            }
            if (empty($data['og_titles'])) {
                $data['og_titles'] = $data['title'];
            }
            
            // og:description
            if ($seoSetting) {
                $ogDescField = $seoSetting->og_descriptions;
                if (is_array($ogDescField) && isset($ogDescField[$locale])) {
                    $data['og_descriptions'] = $ogDescField[$locale];
                } elseif (is_string($ogDescField) && !empty($ogDescField)) {
                    $data['og_descriptions'] = $ogDescField;
                }
            }
            if (empty($data['og_descriptions'])) {
                $data['og_descriptions'] = $data['description'];
            }
            
            // og:image
            if ($seoSetting && $seoSetting->og_image) {
                $data['og_image'] = cdn($seoSetting->og_image);
            } elseif (method_exists($model, 'getFirstMediaUrl')) {
                $data['og_image'] = $model->getFirstMediaUrl('featured');
            } elseif (isset($model->image) && $model->image) {
                $data['og_image'] = cdn($model->image);
            }
            
            // og:type - Model tipine göre
            $data['og_type'] = match($model->getMorphClass()) {
                'Modules\Portfolio\app\Models\Portfolio' => 'article',
                'Modules\Announcement\app\Models\Announcement' => 'article',
                default => 'website'
            };
            
            // og:locale
            if ($seoSetting && $seoSetting->og_locale) {
                $data['og_locale'] = $seoSetting->og_locale;
            } else {
                // Varsayılan: mevcut locale
                $data['og_locale'] = str_replace('-', '_', $locale);
            }
            
            // og:site_name
            if ($seoSetting && $seoSetting->og_site_name) {
                $data['og_site_name'] = $seoSetting->og_site_name;
            } else {
                $data['og_site_name'] = $siteName;
            }
            
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
            
            // Twitter additional fields
            if ($seoSetting && $seoSetting->twitter_site) {
                $data['twitter_site'] = $seoSetting->twitter_site;
            }
            
            if ($seoSetting && $seoSetting->twitter_creator) {
                $data['twitter_creator'] = $seoSetting->twitter_creator;
            }
            
            // 6. ROBOTS
            if ($seoSetting && $seoSetting->robots) {
                $data['robots'] = $seoSetting->robots;
            } elseif (isset($model->is_active) && !$model->is_active) {
                $data['robots'] = 'noindex, nofollow';
            }
            
            // 7. SCHEMA.ORG
            $data['schema'] = $this->generateSchema($model, $data);
            
            // 8. HREFLANG URLS
            if ($model) {
                $data['hreflang'] = $this->generateHreflangLinks($model);
            }
            
            \Log::info('SeoMetaTagService::generateMetaTags result', [
                'title' => $data['title'],
                'has_description' => !empty($data['description']),
                'has_keywords' => !empty($data['keywords']),
                'schema_type' => $data['schema']['@type'] ?? 'null',
                'has_hreflang' => !empty($data['hreflang'])
            ]);
            
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
            
            if (!empty($seoData['og_image'])) {
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
}