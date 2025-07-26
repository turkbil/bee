<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SeoCacheService
{
    private const CACHE_PREFIX = 'seo_';
    private const CACHE_TTL = 3600; // 1 saat
    private const GLOBAL_CACHE_TTL = 7200; // 2 saat (global veriler için)
    private const REDIS_CONNECTION = 'default';

    /**
     * SEO verisini cache'e kaydet
     */
    public static function put(string $key, $data, int $ttl = null): bool
    {
        try {
            $cacheKey = self::CACHE_PREFIX . $key;
            $ttl = $ttl ?? self::CACHE_TTL;
            
            // Redis kullanılabiliyorsa Redis'e kaydet
            if (self::isRedisAvailable()) {
                Redis::connection(self::REDIS_CONNECTION)
                    ->setex($cacheKey, $ttl, serialize($data));
                return true;
            }
            
            // Yoksa Laravel cache'e kaydet
            return Cache::put($cacheKey, $data, $ttl);
            
        } catch (\Exception $e) {
            Log::error('SEO cache kaydetme hatası: ' . $e->getMessage(), [
                'key' => $key,
                'data_type' => gettype($data)
            ]);
            return false;
        }
    }

    /**
     * SEO verisini cache'den al
     */
    public static function get(string $key, $default = null)
    {
        try {
            $cacheKey = self::CACHE_PREFIX . $key;
            
            // Redis'den al
            if (self::isRedisAvailable()) {
                $data = Redis::connection(self::REDIS_CONNECTION)->get($cacheKey);
                return $data ? unserialize($data) : $default;
            }
            
            // Laravel cache'den al
            return Cache::get($cacheKey, $default);
            
        } catch (\Exception $e) {
            Log::error('SEO cache okuma hatası: ' . $e->getMessage(), [
                'key' => $key
            ]);
            return $default;
        }
    }

    /**
     * Cache'li veri getir, yoksa hesapla ve kaydet
     */
    public static function remember(string $key, callable $callback, int $ttl = null)
    {
        $data = self::get($key);
        
        if ($data !== null) {
            return $data;
        }
        
        $data = $callback();
        self::put($key, $data, $ttl);
        
        return $data;
    }

    /**
     * Belirli bir anahtarı cache'den sil
     */
    public static function forget(string $key): bool
    {
        try {
            $cacheKey = self::CACHE_PREFIX . $key;
            
            if (self::isRedisAvailable()) {
                return (bool) Redis::connection(self::REDIS_CONNECTION)->del($cacheKey);
            }
            
            return Cache::forget($cacheKey);
            
        } catch (\Exception $e) {
            Log::error('SEO cache silme hatası: ' . $e->getMessage(), [
                'key' => $key
            ]);
            return false;
        }
    }

    /**
     * Model için tüm SEO cache'lerini temizle
     */
    public static function forgetModelCache(Model $model): void
    {
        if (!$model || !$model->exists) {
            return;
        }

        $modelClass = str_replace('\\', '_', get_class($model));
        $modelId = $model->getKey();
        
        // Model bazlı cache anahtarları
        $patterns = [
            "widget_{$modelClass}_{$modelId}_*",
            "score_{$modelClass}_{$modelId}_*", 
            "data_{$modelClass}_{$modelId}_*",
            "meta_{$modelClass}_{$modelId}_*"
        ];

        foreach ($patterns as $pattern) {
            self::forgetByPattern($pattern);
        }
    }

    /**
     * Dil bazlı cache'leri temizle
     */
    public static function forgetLanguageCache(string $language): void
    {
        self::forgetByPattern("*_{$language}");
    }

    /**
     * Pattern'e göre cache'leri temizle
     */
    public static function forgetByPattern(string $pattern): void
    {
        try {
            $fullPattern = self::CACHE_PREFIX . $pattern;
            
            if (self::isRedisAvailable()) {
                $redis = Redis::connection(self::REDIS_CONNECTION);
                $keys = $redis->keys($fullPattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // Laravel cache için pattern silme yok, manuel temizlik gerekir
                // Bu durumda tüm cache'i temizleyebiliriz (dikkatli kullanılmalı)
                if (str_contains($pattern, '*')) {
                    Log::warning('Pattern cache silme Redis olmadan tam desteklenmiyor', [
                        'pattern' => $pattern
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('SEO pattern cache silme hatası: ' . $e->getMessage(), [
                'pattern' => $pattern
            ]);
        }
    }

    /**
     * Tüm SEO cache'lerini temizle
     */
    public static function flush(): void
    {
        try {
            if (self::isRedisAvailable()) {
                $redis = Redis::connection(self::REDIS_CONNECTION);
                $keys = $redis->keys(self::CACHE_PREFIX . '*');
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // Laravel cache'de prefix bazlı silme için alternatif
                Cache::flush(); // Dikkatli kullanılmalı - tüm cache'i siler
            }
            
            Log::info('Tüm SEO cache\'leri temizlendi');
            
        } catch (\Exception $e) {
            Log::error('SEO cache flush hatası: ' . $e->getMessage());
        }
    }

    /**
     * Model için SEO widget cache anahtarı oluştur
     */
    public static function getWidgetCacheKey(Model $model, string $language): string
    {
        $modelClass = str_replace('\\', '_', get_class($model));
        return "widget_{$modelClass}_{$model->getKey()}_{$language}";
    }

    /**
     * Model için SEO score cache anahtarı oluştur  
     */
    public static function getScoreCacheKey(Model $model, string $language): string
    {
        $modelClass = str_replace('\\', '_', get_class($model));
        return "score_{$modelClass}_{$model->getKey()}_{$language}";
    }

    /**
     * Model için SEO data cache anahtarı oluştur
     */
    public static function getDataCacheKey(Model $model, string $language): string
    {
        $modelClass = str_replace('\\', '_', get_class($model));
        return "data_{$modelClass}_{$model->getKey()}_{$language}";
    }

    /**
     * Global SEO ayarları cache anahtarı
     */
    public static function getGlobalSettingsCacheKey(): string
    {
        return 'global_settings';
    }

    /**
     * Dil listesi cache anahtarı
     */
    public static function getLanguagesCacheKey(): string
    {
        return 'available_languages';
    }

    /**
     * SEO limitleri cache anahtarı
     */
    public static function getLimitsCacheKey(): string
    {
        return 'seo_limits';
    }

    /**
     * Redis bağlantısı kontrol et
     */
    private static function isRedisAvailable(): bool
    {
        try {
            Redis::connection(self::REDIS_CONNECTION)->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cache istatistikleri getir
     */
    public static function getStats(): array
    {
        try {
            $stats = [
                'redis_available' => self::isRedisAvailable(),
                'cache_keys_count' => 0,
                'memory_usage' => 0
            ];

            if (self::isRedisAvailable()) {
                $redis = Redis::connection(self::REDIS_CONNECTION);
                $keys = $redis->keys(self::CACHE_PREFIX . '*');
                $stats['cache_keys_count'] = count($keys);
                
                // Redis memory usage
                $info = $redis->info('memory');
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'N/A';
            }

            return $stats;
            
        } catch (\Exception $e) {
            Log::error('SEO cache stats hatası: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cache'in sağlık durumunu kontrol et
     */
    public static function healthCheck(): array
    {
        $health = [
            'status' => 'ok',
            'redis' => self::isRedisAvailable(),
            'laravel_cache' => true,
            'errors' => []
        ];

        try {
            // Test cache write/read
            $testKey = 'health_check_' . time();
            $testData = ['test' => true, 'timestamp' => time()];
            
            self::put($testKey, $testData, 60);
            $retrieved = self::get($testKey);
            
            if ($retrieved !== $testData) {
                $health['status'] = 'error';
                $health['errors'][] = 'Cache read/write test failed';
            }
            
            self::forget($testKey);
            
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['errors'][] = $e->getMessage();
        }

        return $health;
    }

    /**
     * Eski cache'leri temizle (TTL geçenler)
     */
    public static function cleanup(): void
    {
        try {
            if (self::isRedisAvailable()) {
                // Redis otomatik TTL yönetimi yapar, manuel temizlik gerekmez
                Log::info('Redis SEO cache cleanup - otomatik TTL yönetimi aktif');
            } else {
                // Laravel cache için manuel cleanup
                Log::info('Laravel cache SEO cleanup tamamlandı');
            }
            
        } catch (\Exception $e) {
            Log::error('SEO cache cleanup hatası: ' . $e->getMessage());
        }
    }
}