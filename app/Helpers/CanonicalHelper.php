<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Modules\SeoManagement\app\Models\SeoSetting;

class CanonicalHelper
{
    /**
     * Mevcut sayfa için alternate link'leri oluştur
     * NOT: Canonical URL SeoManagement'tan gelir, tenant müdahale edemez
     * 
     * @param Model|null $model İçerik modeli (Page, Portfolio, Announcement vb.)
     * @param string|null $moduleAction Modül action'ı (index, show, category vb.)
     * @return array
     */
    public static function generateAlternateLinks(?Model $model = null, ?string $moduleAction = 'show'): array
    {
        $links = [];
        $currentLocale = app()->getLocale();
        $defaultLocale = get_tenant_default_locale();
        $availableLocales = array_column(available_tenant_languages(), 'code');
        
        // Mevcut route bilgilerini al
        $currentRoute = Route::current();
        $currentPath = request()->path();
        
        // Ana sayfa kontrolü - route name veya homepage flag'ı kontrol et
        $isHomepage = false;
        if ($currentRoute) {
            $routeName = $currentRoute->getName();
            $isHomepage = in_array($routeName, ['home', 'home.locale']);
        }
        
        // Alternatif: model varsa ve is_homepage flag'ı true ise
        if ($model && method_exists($model, 'getTable') && $model->getTable() === 'pages' && isset($model->is_homepage) && $model->is_homepage) {
            $isHomepage = true;
        }
        
        if ($isHomepage) {
            foreach ($availableLocales as $locale) {
                // Varsayılan dil için prefix kullanma, diğerleri için kullan
                if ($locale === $defaultLocale) {
                    $url = url('/');
                } else {
                    $url = url("/{$locale}");
                }
                
                $links[$locale] = [
                    'url' => $url,
                    'hreflang' => $locale,
                    'current' => $locale === $currentLocale
                ];
            }
            
            return $links;
        }
        
        // Model varsa (içerik sayfası)
        if ($model) {
            $moduleName = class_basename($model);
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            
            foreach ($availableLocales as $locale) {
                // Her dil için slug'ı al
                $slug = $model->getTranslated('slug', $locale);
                
                if (!$slug) {
                    // Fallback: varsayılan dildeki slug'ı kullan
                    $slug = $model->getTranslated('slug', $defaultLocale);
                }
                
                if ($slug) {
                    // Modül slug'ını al
                    $moduleSlug = $moduleSlugService->getSlug($moduleName, $moduleAction);
                    
                    // URL oluştur
                    if ($locale === $defaultLocale) {
                        $url = url("{$moduleSlug}/{$slug}");
                    } else {
                        $url = url("{$locale}/{$moduleSlug}/{$slug}");
                    }
                    
                    $links[$locale] = [
                        'url' => $url,
                        'hreflang' => $locale,
                        'current' => $locale === $currentLocale
                    ];
                }
            }
        } else {
            // Model yoksa (liste sayfası vb.), basit locale değişimi yap
            $pathSegments = array_filter(explode('/', $currentPath));
            
            // Mevcut locale prefix'ini kaldır
            if (!empty($pathSegments)) {
                $firstSegment = reset($pathSegments);
                if (in_array($firstSegment, $availableLocales)) {
                    array_shift($pathSegments);
                }
            }
            
            $cleanPath = implode('/', $pathSegments);
            
            foreach ($availableLocales as $locale) {
                if ($locale === $defaultLocale) {
                    $url = url($cleanPath ?: '/');
                } else {
                    $url = url("{$locale}" . ($cleanPath ? "/{$cleanPath}" : ''));
                }
                
                $links[$locale] = [
                    'url' => $url,
                    'hreflang' => $locale,
                    'current' => $locale === $currentLocale
                ];
            }
        }
        
        return $links;
    }
    
    /**
     * Alternate link HTML meta tag'lerini oluştur
     * NOT: Canonical URL SeoManagement tarafından yönetilir
     * 
     * @param array $links
     * @return string
     */
    public static function generateAlternateMetaTags(array $links): string
    {
        $html = '';
        
        // Alternate links (tüm diller için)
        foreach ($links as $locale => $link) {
            $html .= '<link rel="alternate" hreflang="' . $link['hreflang'] . '" href="' . $link['url'] . '">' . PHP_EOL;
        }
        
        // x-default link (varsayılan dil için)
        $defaultLocale = get_tenant_default_locale();
        if (isset($links[$defaultLocale])) {
            $html .= '<link rel="alternate" hreflang="x-default" href="' . $links[$defaultLocale]['url'] . '">' . PHP_EOL;
        }
        
        return $html;
    }
    
    /**
     * Dil değiştirme menüsü için link'leri al
     * 
     * @param Model|null $model
     * @param string|null $moduleAction
     * @return array
     */
    public static function getLanguageSwitcherLinks(?Model $model = null, ?string $moduleAction = 'show'): array
    {
        $links = self::generateAlternateLinks($model, $moduleAction);
        $languageNames = config('app.available_locales', [
            'tr' => 'Türkçe',
            'en' => 'English',
            'ar' => 'العربية'
        ]);
        
        $switcherLinks = [];
        foreach ($links as $locale => $link) {
            $switcherLinks[$locale] = [
                'url' => $link['url'],
                'name' => $languageNames[$locale] ?? strtoupper($locale),
                'active' => $link['current'],
                'locale' => $locale
            ];
        }
        
        return $switcherLinks;
    }
    
    /**
     * SeoManagement'taki hreflang_urls alanını güncelle
     * 
     * @param Model $model
     * @param array $alternateLinks
     */
    public static function updateSeoHreflangUrls(Model $model, array $alternateLinks): void
    {
        if ($model->seoSetting) {
            $hreflangUrls = [];
            foreach ($alternateLinks as $locale => $link) {
                $hreflangUrls[$locale] = $link['url'];
            }
            
            $model->seoSetting->update([
                'hreflang_urls' => $hreflangUrls
            ]);
        }
    }
}