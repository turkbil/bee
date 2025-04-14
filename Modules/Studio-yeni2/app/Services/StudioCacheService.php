<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudioCacheService
{
    /**
     * Önbellek prefix'i
     *
     * @var string
     */
    protected $prefix;
    
    /**
     * Önbellek süresi (dakika)
     *
     * @var int
     */
    protected $ttl;
    
    /**
     * Tenant ID
     *
     * @var string
     */
    protected $tenantId;
    
    /**
     * Yapılandırma
     */
    public function __construct()
    {
        $this->prefix = config('studio.cache.prefix', 'studio_');
        $this->ttl = config('studio.cache.ttl', 60 * 24);
        $this->tenantId = function_exists('tenant') && tenant() ? tenant()->getTenantKey() : 'central';
    }
    
    /**
     * Önbellekten değer al
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($this->getCacheKey($key), $default);
    }
    
    /**
     * Önbelleğe değer kaydet
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return bool
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->ttl;
        
        return Cache::put($this->getCacheKey($key), $value, now()->addMinutes($ttl));
    }
    
    /**
     * Önbellekten değer al veya kaydet
     *
     * @param string $key
     * @param int|null $ttl
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, ?int $ttl, callable $callback)
    {
        $ttl = $ttl ?? $this->ttl;
        
        return Cache::remember($this->getCacheKey($key), now()->addMinutes($ttl), $callback);
    }
    
    /**
     * Önbellekten değer sil
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }
    
    /**
     * Belirli bir modül için önbelleği temizle
     *
     * @param string $module
     * @param int|null $moduleId
     * @return bool
     */
    public function clearByModule(string $module, ?int $moduleId = null): bool
    {
        try {
            if ($moduleId !== null) {
                // Belirli bir modül + ID için önbelleği temizle
                $this->forget("settings_{$module}_{$moduleId}");
                $this->forget("content_{$module}_{$moduleId}");
            } else {
                // Pattern ile önbellek anahtarlarını bul
                $pattern = $this->getCacheKey("{$module}_*");
                
                // Redis kullanılıyor mu kontrol et
                if (config('cache.default') === 'redis') {
                    $this->clearRedisCache($pattern);
                } else {
                    // Manuel olarak ilgili anahtarları temizle
                    $this->forget("all_{$module}");
                    $this->forget("{$module}_settings");
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Modül önbelleği temizlenirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Belirli bir tip için önbelleği temizle
     *
     * @param string $type
     * @return bool
     */
    public function clearByType(string $type): bool
    {
        try {
            // Pattern ile önbellek anahtarlarını bul
            $pattern = $this->getCacheKey("*_{$type}*");
            
            // Redis kullanılıyor mu kontrol et
            if (config('cache.default') === 'redis') {
                $this->clearRedisCache($pattern);
            } else {
                // Manuel olarak ilgili anahtarları temizle
                $this->forget("all_{$type}");
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Tip önbelleği temizlenirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Redis önbelleğini temizle
     *
     * @param string $pattern
     * @return void
     */
    protected function clearRedisCache(string $pattern): void
    {
        $redis = Cache::getRedis();
        $keys = $redis->keys($pattern);
        
        if (!empty($keys)) {
            $redis->del($keys);
        }
    }
    
    /**
     * Önbellek anahtarı oluştur
     *
     * @param string $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return "{$this->prefix}{$this->tenantId}_{$key}";
    }
}