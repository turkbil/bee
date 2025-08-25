<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\Tenant;

class TenantCacheService
{
    /**
     * Standart TTL değerleri (saniye)
     */
    public const TTL_MINUTE = 60;
    public const TTL_FIVE_MINUTES = 300;
    public const TTL_TEN_MINUTES = 600;
    public const TTL_THIRTY_MINUTES = 1800;
    public const TTL_HOUR = 3600;
    public const TTL_SIX_HOURS = 21600;
    public const TTL_DAY = 86400;
    public const TTL_WEEK = 604800;
    public const TTL_MONTH = 2592000;

    /**
     * Cache key prefix sabitleri
     */
    public const PREFIX_TENANT = 'tenant';
    public const PREFIX_SESSION = 'session';
    public const PREFIX_LANGUAGE = 'lang';
    public const PREFIX_MODULE = 'module';
    public const PREFIX_SETTING = 'setting';
    public const PREFIX_USER = 'user';
    public const PREFIX_ROUTE = 'route';
    public const PREFIX_WIDGET = 'widget';
    public const PREFIX_MENU = 'menu';
    public const PREFIX_PAGE = 'page';
    public const PREFIX_SEO = 'seo';
    public const PREFIX_AI = 'ai';
    public const PREFIX_PORTFOLIO = 'portfolio';
    public const PREFIX_ANNOUNCEMENT = 'announcement';
    public const PREFIX_PERMISSION = 'permission';
    public const PREFIX_THEME = 'theme';

    protected ?Tenant $tenant;
    protected string $tenantId;
    protected string $cacheDriver;

    public function __construct()
    {
        $this->tenant = tenant();
        $this->tenantId = $this->tenant ? $this->tenant->id : 'central';
        $this->cacheDriver = config('cache.default');
    }

    /**
     * Standart cache key oluştur
     * 
     * Format: tenant:{tenant_id}:{prefix}:{key}
     */
    public function key(string $prefix, string $key, array $params = []): string
    {
        $keyParts = [
            self::PREFIX_TENANT,
            $this->tenantId,
            $prefix,
            $key
        ];

        // Parametreler varsa MD5 hash olarak ekle
        if (!empty($params)) {
            $keyParts[] = md5(serialize($params));
        }

        return implode(':', array_filter($keyParts));
    }

    /**
     * Tenant için cache tag'leri oluştur
     */
    public function tags(array $additionalTags = []): array
    {
        $baseTags = [
            self::PREFIX_TENANT . ':' . $this->tenantId,
            self::PREFIX_TENANT . ':all'
        ];

        return array_merge($baseTags, $additionalTags);
    }

    /**
     * Cache'den veri al veya callback çalıştır
     */
    public function remember(string $prefix, string $key, $ttl, callable $callback, array $params = [])
    {
        $cacheKey = $this->key($prefix, $key, $params);
        
        if ($this->supportsTagging()) {
            return Cache::tags($this->tags([$prefix]))
                ->remember($cacheKey, $ttl, $callback);
        }

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Cache'den veri al
     */
    public function get(string $prefix, string $key, array $params = [], $default = null)
    {
        $cacheKey = $this->key($prefix, $key, $params);
        
        if ($this->supportsTagging()) {
            return Cache::tags($this->tags([$prefix]))->get($cacheKey, $default);
        }

        return Cache::get($cacheKey, $default);
    }

    /**
     * Cache'e veri kaydet
     */
    public function put(string $prefix, string $key, $value, $ttl = null, array $params = []): bool
    {
        $cacheKey = $this->key($prefix, $key, $params);
        $ttl = $ttl ?? self::TTL_HOUR;
        
        if ($this->supportsTagging()) {
            return Cache::tags($this->tags([$prefix]))->put($cacheKey, $value, $ttl);
        }

        return Cache::put($cacheKey, $value, $ttl);
    }

    /**
     * Cache'den sil
     */
    public function forget(string $prefix, string $key, array $params = []): bool
    {
        $cacheKey = $this->key($prefix, $key, $params);
        
        if ($this->supportsTagging()) {
            return Cache::tags($this->tags([$prefix]))->forget($cacheKey);
        }

        return Cache::forget($cacheKey);
    }

    /**
     * Prefix'e göre tüm cache'i temizle
     */
    public function flushByPrefix(string $prefix): void
    {
        if ($this->supportsTagging()) {
            Cache::tags($this->tags([$prefix]))->flush();
        } else {
            $this->flushByPattern($prefix);
        }
    }

    /**
     * Tenant'ın tüm cache'ini temizle
     */
    public function flushTenant(): void
    {
        if ($this->supportsTagging()) {
            Cache::tags([self::PREFIX_TENANT . ':' . $this->tenantId])->flush();
        } else {
            $this->flushByPattern(self::PREFIX_TENANT . ':' . $this->tenantId);
        }

        Log::info('Tenant cache temizlendi', [
            'tenant_id' => $this->tenantId,
            'driver' => $this->cacheDriver
        ]);
    }

    /**
     * Tüm tenant'ların cache'ini temizle (admin için)
     */
    public function flushAllTenants(): void
    {
        if ($this->supportsTagging()) {
            Cache::tags([self::PREFIX_TENANT . ':all'])->flush();
        } else {
            $this->flushByPattern(self::PREFIX_TENANT . ':*');
        }

        Log::info('Tüm tenant cache\'leri temizlendi');
    }

    /**
     * Pattern ile cache temizle (Redis için)
     */
    protected function flushByPattern(string $pattern): void
    {
        if ($this->cacheDriver === 'redis') {
            try {
                $redis = Redis::connection(config('cache.stores.redis.connection', 'cache'));
                $prefix = config('cache.prefix', '');
                $keys = $redis->keys($prefix . $pattern . ':*');
                
                if (!empty($keys)) {
                    // Prefix'i kaldır
                    $keys = array_map(function($key) use ($prefix) {
                        return str_replace($prefix, '', $key);
                    }, $keys);
                    
                    $redis->del($keys);
                }
            } catch (\Exception $e) {
                Log::error('Cache pattern temizleme hatası', [
                    'pattern' => $pattern,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Cache driver tag desteği kontrolü
     */
    protected function supportsTagging(): bool
    {
        return in_array($this->cacheDriver, ['redis', 'memcached']);
    }

    /**
     * Modül cache helper
     */
    public function moduleCache(string $module, string $key, $ttl, callable $callback, array $params = [])
    {
        $fullKey = $module . ':' . $key;
        return $this->remember(self::PREFIX_MODULE, $fullKey, $ttl, $callback, $params);
    }

    /**
     * Dil cache helper
     */
    public function languageCache(string $key, $ttl, callable $callback, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $params = ['locale' => $locale];
        return $this->remember(self::PREFIX_LANGUAGE, $key, $ttl, $callback, $params);
    }

    /**
     * Kullanıcı cache helper
     */
    public function userCache(string $key, $ttl, callable $callback, ?int $userId = null)
    {
        $userId = $userId ?? (auth()->id() ?? 'guest');
        $params = ['user_id' => $userId];
        return $this->remember(self::PREFIX_USER, $key, $ttl, $callback, $params);
    }

    /**
     * Route cache helper
     */
    public function routeCache(string $routeName, $ttl, callable $callback, array $routeParams = [])
    {
        return $this->remember(self::PREFIX_ROUTE, $routeName, $ttl, $callback, $routeParams);
    }

    /**
     * Cache istatistikleri
     */
    public function getStats(): array
    {
        $stats = [
            'tenant_id' => $this->tenantId,
            'cache_driver' => $this->cacheDriver,
            'supports_tagging' => $this->supportsTagging(),
            'prefixes' => [
                'tenant' => self::PREFIX_TENANT . ':' . $this->tenantId,
                'session' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_SESSION,
                'language' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_LANGUAGE,
                'module' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_MODULE,
            ]
        ];

        // Redis kullanılıyorsa memory bilgisi ekle
        if ($this->cacheDriver === 'redis') {
            try {
                $redis = Redis::connection(config('cache.stores.redis.connection', 'cache'));
                $info = $redis->info('memory');
                $stats['redis_memory'] = [
                    'used_memory_human' => $info['used_memory_human'] ?? 'N/A',
                    'used_memory_peak_human' => $info['used_memory_peak_human'] ?? 'N/A'
                ];
            } catch (\Exception $e) {
                $stats['redis_memory'] = 'Unavailable';
            }
        }

        return $stats;
    }

    /**
     * Statik instance (helper için)
     */
    public static function instance(): self
    {
        return app(self::class);
    }
}