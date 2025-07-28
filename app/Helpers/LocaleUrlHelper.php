<?php

use App\Services\ModuleSlugService;

if (!function_exists('locale_url')) {
    /**
     * Locale-aware URL oluştur
     * Tenant'ın modül slug customization'ını da dikkate alır
     */
    function locale_url($path, $locale = null) {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = get_tenant_default_locale();
        
        // Varsayılan dil için prefix yok
        if ($locale === $defaultLocale) {
            return url($path);
        }
        
        // Diğer diller için prefix ekle
        return url("/{$locale}/{$path}");
    }
}

if (!function_exists('module_locale_url')) {
    /**
     * Modül için locale-aware URL oluştur
     * Hem dil hem de tenant-specific modül slug'ını handle eder
     */
    function module_locale_url($moduleName, $action, $params = [], $locale = null) {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = get_tenant_default_locale();
        
        // Modül slug'ını al (tenant customization dahil)
        $moduleSlug = ModuleSlugService::getSlug($moduleName, $action);
        
        // Parametreleri URL'e dönüştür
        $paramString = is_array($params) ? implode('/', $params) : $params;
        $path = $moduleSlug;
        if ($paramString) {
            $path .= '/' . $paramString;
        }
        
        // Varsayılan dil için prefix yok
        if ($locale === $defaultLocale) {
            return url($path);
        }
        
        // Diğer diller için prefix ekle
        return url("/{$locale}/{$path}");
    }
}

if (!function_exists('current_url_for_locale')) {
    /**
     * Mevcut URL'i başka bir dil için oluştur
     * Tenant-aware module slug'ları da handle eder
     */
    function current_url_for_locale($targetLocale) {
        try {
            $request = request();
            $currentLocale = app()->getLocale();
            $defaultLocale = get_tenant_default_locale();
            $path = $request->path();
            
            // Ana sayfa kontrolü
            if ($path === '/' || $path === '') {
                if ($targetLocale === $defaultLocale) {
                    return url('/');
                }
                return url('/' . $targetLocale);
            }
            
            // Mevcut URL'den locale prefix'ini temizle (varsa)
            $pathParts = explode('/', $path);
            $firstPart = $pathParts[0] ?? '';
            
            // İlk kısım bir dil kodu mu kontrol et
            $validLocales = array_column(available_tenant_languages(), 'code');
            if (in_array($firstPart, $validLocales)) {
                // Dil prefix'ini kaldır
                array_shift($pathParts);
                $path = implode('/', $pathParts);
            }
            
            // Yeni locale için URL oluştur
            if ($targetLocale === $defaultLocale) {
                // Varsayılan dil için prefix yok
                return url($path);
            } else {
                // Diğer diller için prefix ekle
                return url("/{$targetLocale}/{$path}");
            }
            
        } catch (\Exception $e) {
            // Fallback - language switch route kullan
            return url('/language/' . $targetLocale);
        }
    }
}

if (!function_exists('switch_language_url')) {
    /**
     * Dil değiştirme URL'i oluştur
     * İçerik dilini değiştirir ve doğru slug'a yönlendirir
     */
    function switch_language_url($targetLocale) {
        // Basit dil değiştirme route'u kullan
        // Controller cache temizleme ve redirect işlemlerini yapacak
        return url('/language/' . $targetLocale);
    }
}

if (!function_exists('generate_page_url')) {
    /**
     * Page modülü için locale-aware URL oluştur
     * Multi-language slug desteği ile
     */
    function generate_page_url($page, $locale = null) {
        $locale = $locale ?? app()->getLocale();
        
        // Page'in ilgili dildeki slug'ını al
        $slug = $page->getTranslated('slug', $locale);
        
        // Page modülü için tenant-specific slug'ı al
        $moduleSlug = ModuleSlugService::getSlug('Page', 'show');
        
        return module_locale_url('Page', 'show', [$slug], $locale);
    }
}

if (!function_exists('generate_portfolio_url')) {
    /**
     * Portfolio modülü için locale-aware URL oluştur
     */
    function generate_portfolio_url($portfolio, $locale = null) {
        $locale = $locale ?? app()->getLocale();
        
        // Portfolio'nun ilgili dildeki slug'ını al
        $slug = $portfolio->getTranslated('slug', $locale);
        
        return module_locale_url('Portfolio', 'show', [$slug], $locale);
    }
}

if (!function_exists('generate_announcement_url')) {
    /**
     * Announcement modülü için locale-aware URL oluştur
     */
    function generate_announcement_url($announcement, $locale = null) {
        $locale = $locale ?? app()->getLocale();
        
        // Announcement'ın ilgili dildeki slug'ını al
        $slug = $announcement->getTranslated('slug', $locale);
        
        return module_locale_url('Announcement', 'show', [$slug], $locale);
    }
}