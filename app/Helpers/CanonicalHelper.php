<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Modules\SeoManagement\app\Models\SeoSetting;
use App\Services\UnifiedUrlBuilderService;
use Illuminate\Support\Facades\Log;

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
        try {
            // Unified URL Builder kullan
            $urlBuilder = app(UnifiedUrlBuilderService::class);
            return $urlBuilder->generateAlternateLinks($model, $moduleAction);
        } catch (\Exception $e) {
            Log::error('CanonicalHelper: Error generating alternate links', [
                'model' => $model ? get_class($model) : null,
                'action' => $moduleAction,
                'error' => $e->getMessage()
            ]);
            
            // Fallback: en azından mevcut dil için link döndür
            $currentLocale = app()->getLocale();
            return [
                $currentLocale => [
                    'url' => url()->current(),
                    'hreflang' => $currentLocale,
                    'current' => true
                ]
            ];
        }
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
        try {
            $links = self::generateAlternateLinks($model, $moduleAction);
            
            // Link'lere name bilgisi eklenmiş olarak geliyor
            $switcherLinks = [];
            foreach ($links as $locale => $link) {
                $switcherLinks[$locale] = [
                    'url' => $link['url'],
                    'name' => $link['name'] ?? strtoupper($locale),
                    'active' => $link['current'],
                    'locale' => $locale
                ];
            }
            
            return $switcherLinks;
        } catch (\Exception $e) {
            Log::error('CanonicalHelper: Error generating language switcher links', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback
            return [];
        }
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