<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ModuleAccessCache
{
    /**
     * Cache TTL dakika cinsinden
     */
    protected const CACHE_TTL = 10; // 10 dakika
    
    /**
     * Modül erişim cache'ini getirir
     */
    public function getAccessCache(string $userId, string $moduleName, string $permissionType): ?bool
    {
        $cacheKey = $this->generateCacheKey($userId, $moduleName, $permissionType);
        
        return Cache::tags($this->getCacheTags())->get($cacheKey);
    }
    
    /**
     * Modül erişim cache'ini kaydet
     */
    public function setAccessCache(string $userId, string $moduleName, string $permissionType, bool $hasAccess): void
    {
        $cacheKey = $this->generateCacheKey($userId, $moduleName, $permissionType);
        
        Cache::tags($this->getCacheTags())->put($cacheKey, $hasAccess, now()->addMinutes(self::CACHE_TTL));
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug("Modül erişim cache'i kaydedildi", [
                'cache_key' => $cacheKey,
                'user_id' => $userId,
                'module' => $moduleName,
                'permission' => $permissionType,
                'access' => $hasAccess
            ]);
        }
    }
    
    /**
     * Modül tenant atama cache'ini getirir
     */
    public function getTenantAssignmentCache(string $moduleId, string $tenantId): ?bool
    {
        $cacheKey = $this->generateTenantAssignmentKey($moduleId, $tenantId);
        
        return Cache::tags($this->getCacheTags())->get($cacheKey);
    }
    
    /**
     * Modül tenant atama cache'ini kaydet
     */
    public function setTenantAssignmentCache(string $moduleId, string $tenantId, bool $isAssigned): void
    {
        $cacheKey = $this->generateTenantAssignmentKey($moduleId, $tenantId);
        
        Cache::tags($this->getCacheTags())->put($cacheKey, $isAssigned, now()->addMinutes(self::CACHE_TTL));
    }
    
    /**
     * Kullanıcı erişim cache'ini temizle
     */
    public function clearUserCache(string $userId): void
    {
        $pattern = $this->getUserCachePattern($userId);
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug("Kullanıcı modül erişim cache'i temizleniyor", [
                'user_id' => $userId,
                'pattern' => $pattern
            ]);
        }
        
        // Redis kullanıyorsak pattern ile silme
        $this->clearCacheByPattern($pattern);
    }
    
    /**
     * Tüm modül erişim cache'ini temizle
     */
    public function clearAllCache(): void
    {
        Cache::tags($this->getCacheTags())->flush();
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug("Tüm modül erişim cache'i temizlendi", [
                'tags' => $this->getCacheTags()
            ]);
        }
    }
    
    /**
     * Cache key oluştur
     */
    protected function generateCacheKey(string $userId, string $moduleName, string $permissionType): string
    {
        $tenant = tenant();
        $tenantPart = $tenant ? "tenant_{$tenant->id}" : 'central';
        
        return "module_access:{$tenantPart}:user_{$userId}:module_{$moduleName}:perm_{$permissionType}";
    }
    
    /**
     * Tenant atama cache key'i oluştur
     */
    protected function generateTenantAssignmentKey(string $moduleId, string $tenantId): string
    {
        return "module_tenant_assignment:module_{$moduleId}:tenant_{$tenantId}";
    }
    
    /**
     * Kullanıcı cache pattern'i oluştur
     */
    protected function getUserCachePattern(string $userId): string
    {
        $tenant = tenant();
        $tenantPart = $tenant ? "tenant_{$tenant->id}" : 'central';
        
        return "module_access:{$tenantPart}:user_{$userId}:*";
    }
    
    /**
     * Cache tag'lerini al
     */
    protected function getCacheTags(): array
    {
        $tenant = tenant();
        if ($tenant) {
            return ["tenant_{$tenant->id}:module_access"];
        }
        
        return ['central:module_access'];
    }
    
    /**
     * Pattern ile cache temizleme
     */
    protected function clearCacheByPattern(string $pattern): void
    {
        // Redis store kullanıyorsak Lua script ile pattern temizleme
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // Diğer store'lar için tag temizleme
            Cache::tags($this->getCacheTags())->flush();
        }
    }
}