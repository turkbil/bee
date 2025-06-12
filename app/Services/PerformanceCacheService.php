<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceCacheService
{
    // Cache tags
    const TAG_MODULES = 'modules';
    const TAG_THEMES = 'themes';
    const TAG_SLUGS = 'slugs';
    const TAG_PAGES = 'pages';
    
    // Cache süreler (dakika)
    const TTL_SHORT = 15;       // 15 dakika
    const TTL_MEDIUM = 60;      // 1 saat  
    const TTL_LONG = 60 * 24;   // 24 saat
    
    /**
     * Tagged cache ile optimized remember
     */
    public static function remember(string $key, array $tags, int $ttl, callable $callback)
    {
        try {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            // Cache hatası durumunda direkt callback çalıştır
            Log::warning('PerformanceCacheService: Cache error, falling back to direct execution', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        }
    }
    
    /**
     * Tag'e göre cache temizle
     */
    public static function flushByTag(string $tag): void
    {
        try {
            Cache::tags([$tag])->flush();
            Log::info("PerformanceCacheService: Flushed cache for tag: {$tag}");
        } catch (\Exception $e) {
            Log::error('PerformanceCacheService: Failed to flush cache', [
                'tag' => $tag,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Birden fazla tag'i temizle
     */
    public static function flushByTags(array $tags): void
    {
        foreach ($tags as $tag) {
            self::flushByTag($tag);
        }
    }
    
    /**
     * Tüm performance cache'lerini temizle
     */
    public static function flushAll(): void
    {
        self::flushByTags([
            self::TAG_MODULES,
            self::TAG_THEMES, 
            self::TAG_SLUGS,
            self::TAG_PAGES
        ]);
    }
}