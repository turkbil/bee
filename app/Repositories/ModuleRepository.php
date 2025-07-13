<?php

namespace App\Repositories;

use App\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;
use Modules\ModuleManagement\App\Models\Module;

class ModuleRepository implements ModuleRepositoryInterface
{
    /**
     * Cache TTL dakika cinsinden
     */
    protected const CACHE_TTL = 360; // 6 saat
    
    /**
     * Tenant için modülleri getirir
     */
    public function getModulesForTenant(?string $tenantId = null): Collection
    {
        $cacheKey = $this->generateCacheKey('modules_tenant', $tenantId);
        $cacheTags = $this->getCacheTags($tenantId);
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($tenantId) {
            return $this->fetchModulesForTenant($tenantId);
        });
    }
    
    /**
     * Aktif modülleri getirir
     */
    public function getActiveModules(): Collection
    {
        $cacheKey = 'active_modules';
        $cacheTags = ['modules'];
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () {
            return TenantHelpers::central(function () {
                return Module::where('is_active', true)
                    ->orderBy('display_name')
                    ->get();
            });
        });
    }
    
    /**
     * Modül tenant atamalarını getirir
     */
    public function getTenantModuleAssignments(string $tenantId): Collection
    {
        $cacheKey = $this->generateCacheKey('tenant_module_assignments', $tenantId);
        $cacheTags = $this->getCacheTags($tenantId);
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($tenantId) {
            return TenantHelpers::central(function () use ($tenantId) {
                return DB::table('module_tenants')
                    ->join('modules', 'module_tenants.module_id', '=', 'modules.module_id')
                    ->where('module_tenants.tenant_id', $tenantId)
                    ->where('module_tenants.is_active', true)
                    ->where('modules.is_active', true)
                    ->select([
                        'modules.module_id',
                        'modules.name',
                        'modules.display_name',
                        'modules.type',
                        'module_tenants.is_active as tenant_active',
                        'module_tenants.assigned_at'
                    ])
                    ->orderBy('modules.display_name')
                    ->get();
            });
        });
    }
    
    /**
     * Modül cache'ini temizler
     */
    public function clearModuleCache(?string $tenantId = null): void
    {
        if ($tenantId) {
            // Belirli tenant cache'ini temizle
            $tags = $this->getCacheTags($tenantId);
            foreach ($tags as $tag) {
                Cache::tags($tag)->flush();
            }
        } else {
            // Tüm modül cache'lerini temizle
            Cache::tags(['modules'])->flush();
            
            // Tüm tenant modül cache'lerini de temizle
            $redis = Cache::getRedis();
            $pattern = '*:modules:*';
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Module cache cleared', ['tenant_id' => $tenantId]);
        }
    }
    
    /**
     * ID ile modül getirir
     */
    public function findById(int $moduleId): ?object
    {
        $cacheKey = "module_by_id:{$moduleId}";
        $cacheTags = ['modules'];
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($moduleId) {
            return TenantHelpers::central(function () use ($moduleId) {
                return Module::find($moduleId);
            });
        });
    }
    
    /**
     * İsim ile modül getirir
     */
    public function findByName(string $moduleName): ?object
    {
        $cacheKey = "module_by_name:{$moduleName}";
        $cacheTags = ['modules'];
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($moduleName) {
            return TenantHelpers::central(function () use ($moduleName) {
                return Module::where('name', $moduleName)->first();
            });
        });
    }
    
    /**
     * Tenant için modülleri fetch et
     */
    protected function fetchModulesForTenant(?string $tenantId): Collection
    {
        return TenantHelpers::central(function () use ($tenantId) {
            $query = Module::where('modules.is_active', true);
            
            if ($tenantId) {
                // Tenant'a atanmış modülleri getir
                $query->join('module_tenants', 'modules.module_id', '=', 'module_tenants.module_id')
                      ->where('module_tenants.tenant_id', $tenantId)
                      ->where('module_tenants.is_active', true)
                      ->select([
                          'modules.*',
                          'module_tenants.assigned_at',
                          'module_tenants.is_active as tenant_active'
                      ]);
            }
            
            return $query->orderBy('modules.display_name')->get();
        });
    }
    
    /**
     * Cache key oluştur
     */
    protected function generateCacheKey(string $prefix, ?string $tenantId): string
    {
        $tenantPart = $tenantId ? "tenant_{$tenantId}" : 'central';
        return "{$prefix}:{$tenantPart}";
    }
    
    /**
     * Cache tag'leri al
     */
    protected function getCacheTags(?string $tenantId): array
    {
        $tags = ['modules'];
        
        if ($tenantId) {
            $tags[] = "tenant_{$tenantId}:modules";
        }
        
        return $tags;
    }
}