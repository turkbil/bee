<?php

namespace Modules\Page\App\Services;

use Modules\Page\App\Models\Page;

class PageCacheService
{
    /**
     * 🚨 GLOBAL PAGE CACHE - Tek request'te tüm component'ler için
     */
    private static array $pageCache = [];
    
    /**
     * Page'i cache'den getir veya DB'den çek
     */
    public static function getPageWithSeo(int $pageId): ?Page
    {
        if (!isset(self::$pageCache[$pageId])) {
            self::$pageCache[$pageId] = Page::with('seoSetting')->find($pageId);
        }
        
        return self::$pageCache[$pageId];
    }
    
    /**
     * Cache'i temizle
     */
    public static function clearCache(?int $pageId = null): void
    {
        if ($pageId) {
            unset(self::$pageCache[$pageId]);
        } else {
            self::$pageCache = [];
        }
    }
    
    /**
     * Cache'de var mı kontrol et
     */
    public static function hasCached(int $pageId): bool
    {
        return isset(self::$pageCache[$pageId]);
    }
}