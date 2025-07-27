<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class GlobalCacheService
{
    /**
     * 🚨 GLOBAL MODEL CACHE - Tek request'te tüm component'ler için
     */
    private static array $modelCache = [];
    
    /**
     * Model'i cache'den getir veya DB'den çek
     */
    public static function getModelWithRelations(string $modelClass, int $modelId, array $relations = []): ?Model
    {
        $cacheKey = self::generateCacheKey($modelClass, $modelId, $relations);
        
        if (!isset(self::$modelCache[$cacheKey])) {
            $query = $modelClass::query();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            self::$modelCache[$cacheKey] = $query->find($modelId);
        }
        
        return self::$modelCache[$cacheKey];
    }
    
    /**
     * Belirli model için cache'i temizle
     */
    public static function clearModelCache(string $modelClass, ?int $modelId = null): void
    {
        if ($modelId) {
            // Belirli model için tüm cache'leri temizle
            $pattern = self::getModelCachePattern($modelClass, $modelId);
            foreach (self::$modelCache as $key => $value) {
                if (str_starts_with($key, $pattern)) {
                    unset(self::$modelCache[$key]);
                }
            }
        } else {
            // Model sınıfının tüm cache'lerini temizle
            $pattern = self::getModelCachePattern($modelClass);
            foreach (self::$modelCache as $key => $value) {
                if (str_starts_with($key, $pattern)) {
                    unset(self::$modelCache[$key]);
                }
            }
        }
    }
    
    /**
     * Tüm cache'i temizle
     */
    public static function clearAllCache(): void
    {
        self::$modelCache = [];
    }
    
    /**
     * Cache'de var mı kontrol et
     */
    public static function hasCached(string $modelClass, int $modelId, array $relations = []): bool
    {
        $cacheKey = self::generateCacheKey($modelClass, $modelId, $relations);
        return isset(self::$modelCache[$cacheKey]);
    }
    
    /**
     * Cache key oluştur
     */
    private static function generateCacheKey(string $modelClass, int $modelId, array $relations = []): string
    {
        $baseKey = class_basename($modelClass) . '_' . $modelId;
        
        if (!empty($relations)) {
            sort($relations);
            $relationsKey = implode('_', $relations);
            return $baseKey . '_with_' . md5($relationsKey);
        }
        
        return $baseKey;
    }
    
    /**
     * Model cache pattern'i oluştur
     */
    private static function getModelCachePattern(string $modelClass, ?int $modelId = null): string
    {
        $basePattern = class_basename($modelClass);
        
        if ($modelId) {
            return $basePattern . '_' . $modelId;
        }
        
        return $basePattern . '_';
    }
    
    /**
     * Cache istatistikleri al
     */
    public static function getCacheStats(): array
    {
        $stats = [];
        $totalCount = count(self::$modelCache);
        
        foreach (self::$modelCache as $key => $value) {
            $modelName = explode('_', $key)[0];
            $stats[$modelName] = ($stats[$modelName] ?? 0) + 1;
        }
        
        return [
            'total_cached_items' => $totalCount,
            'models' => $stats,
            'memory_usage' => memory_get_usage(true)
        ];
    }
    
    // Backward compatibility methods for Page module
    
    /**
     * Page için eski API uyumluluğu
     */
    public static function getPageWithSeo(int $pageId): ?Model
    {
        return self::getModelWithRelations(
            \Modules\Page\App\Models\Page::class,
            $pageId,
            ['seoSetting']
        );
    }
    
    /**
     * Cache'i temizle (eski API)
     */
    public static function clearCache(?int $pageId = null): void
    {
        if ($pageId) {
            self::clearModelCache(\Modules\Page\App\Models\Page::class, $pageId);
        } else {
            self::clearAllCache();
        }
    }
}