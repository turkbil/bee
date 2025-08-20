<?php

namespace App\Helpers;

use App\Models\Tenant;

/**
 * Tenant Bazlı SEO Helper
 * 
 * Mevcut tenant'tan SEO varsayılan değerlerini alır
 */
class TenantSeoHelper
{
    /**
     * Mevcut tenant'tan SEO değerini al
     */
    public static function getTenantSeoValue(string $field, ?string $fallback = null): ?string
    {
        try {
            $tenant = tenant();
            
            if (!$tenant) {
                return $fallback;
            }
            
            $value = $tenant->{$field};
            
            return !empty($value) ? $value : $fallback;
            
        } catch (\Exception $e) {
            \Log::warning("TenantSeoHelper error: {$e->getMessage()}", [
                'field' => $field,
                'fallback' => $fallback
            ]);
            
            return $fallback;
        }
    }
    
    /**
     * Tenant SEO author al
     */
    public static function getAuthor(?string $fallback = null): ?string
    {
        return self::getTenantSeoValue('seo_default_author', $fallback);
    }
    
    /**
     * Tenant SEO publisher al
     */
    public static function getPublisher(?string $fallback = null): ?string
    {
        return self::getTenantSeoValue('seo_default_publisher', $fallback);
    }
    
    /**
     * Tenant SEO copyright al - DEPRECATED
     * Copyright artık SeoMetaTagService tarafından otomatik generate ediliyor
     */
    public static function getCopyright(?string $fallback = null): ?string
    {
        // Copyright artık otomatik generate ediliyor, bu metod backward compatibility için
        return $fallback;
    }
    
    /**
     * Tenant SEO og:site_name al
     */
    public static function getOgSiteName(?string $fallback = null): ?string
    {
        return self::getTenantSeoValue('seo_default_og_site_name', $fallback);
    }
    
    /**
     * Tenant SEO twitter:site al
     */
    public static function getTwitterSite(?string $fallback = null): ?string
    {
        return self::getTenantSeoValue('seo_default_twitter_site', $fallback);
    }
    
    /**
     * Tenant SEO twitter:creator al
     */
    public static function getTwitterCreator(?string $fallback = null): ?string
    {
        return self::getTenantSeoValue('seo_default_twitter_creator', $fallback);
    }
    
    /**
     * Tüm tenant SEO değerlerini array olarak al
     */
    public static function getAllSeoDefaults(): array
    {
        return [
            'author' => self::getAuthor(),
            'publisher' => self::getPublisher(),
            // Copyright artık SeoMetaTagService tarafından otomatik generate ediliyor
            'og_site_name' => self::getOgSiteName(),
            'twitter_site' => self::getTwitterSite(),
            'twitter_creator' => self::getTwitterCreator(),
        ];
    }
}