<?php

namespace App\Services;

use App\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ModuleService
{
    protected ModuleRepositoryInterface $moduleRepository;
    
    public function __construct(ModuleRepositoryInterface $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }
    
    /**
     * Tüm modülleri getirir, tenant origin bilgisi ekler
     */
    public function getModulesByTenant(?string $tenantId = null): Collection
    {
        try {
            return $this->moduleRepository->getModulesForTenant($tenantId);
            
        } catch (\Exception $e) {
            Log::error('Error getting modules by tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
    
    /**
     * Aktif modülleri getirir
     */
    public function getActiveModules(): Collection
    {
        try {
            return $this->moduleRepository->getActiveModules();
            
        } catch (\Exception $e) {
            Log::error('Error getting active modules', [
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
    
    /**
     * Tenant modül atamalarını getirir
     */
    public function getTenantModuleAssignments(string $tenantId): Collection
    {
        try {
            return $this->moduleRepository->getTenantModuleAssignments($tenantId);
            
        } catch (\Exception $e) {
            Log::error('Error getting tenant module assignments', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
    
    /**
     * Modül bilgilerini ID ile getirir
     */
    public function getModuleById(int $moduleId): ?object
    {
        try {
            return $this->moduleRepository->findById($moduleId);
            
        } catch (\Exception $e) {
            Log::error('Error getting module by ID', [
                'module_id' => $moduleId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Modül bilgilerini isim ile getirir
     */
    public function getModuleByName(string $moduleName): ?object
    {
        try {
            return $this->moduleRepository->findByName($moduleName);
            
        } catch (\Exception $e) {
            Log::error('Error getting module by name', [
                'module_name' => $moduleName,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Modül aktif mi kontrol eder
     */
    public function isModuleActive(string $moduleName): bool
    {
        $module = $this->getModuleByName($moduleName);
        return $module && $module->is_active;
    }
    
    /**
     * Tenant için modül aktif mi kontrol eder
     */
    public function isModuleActiveForTenant(string $moduleName, string $tenantId): bool
    {
        $assignments = $this->getTenantModuleAssignments($tenantId);
        
        return $assignments->contains(function ($assignment) use ($moduleName) {
            return $assignment->name === $moduleName && $assignment->tenant_active;
        });
    }
    
    /**
     * Modül cache'ini temizler
     */
    public function clearModuleCache(?string $tenantId = null): void
    {
        $this->moduleRepository->clearModuleCache($tenantId);
        
        if (app()->environment(['local', 'staging'])) {
            Log::debug('Module service cache cleared', ['tenant_id' => $tenantId]);
        }
    }
    
    /**
     * Modülleri tipe göre gruplar
     */
    public function groupModulesByType(?Collection $modules = null): Collection
    {
        try {
            // Eğer modül collection'ı verilmemişse, aktif modülleri al
            $modules = $modules ?: $this->getActiveModules();
            
            return $modules->groupBy('type')->map(function ($moduleGroup, $type) {
                return $moduleGroup->values();
            });
            
        } catch (\Exception $e) {
            Log::error('Error grouping modules by type', [
                'error' => $e->getMessage()
            ]);
            
            return collect();
        }
    }
    
    /**
     * Modül istatistiklerini getirir
     */
    public function getModuleStats(): array
    {
        try {
            $activeModules = $this->getActiveModules();
            
            return [
                'total_active' => $activeModules->count(),
                'by_type' => $activeModules->groupBy('type')->map->count(),
                'recently_updated' => $activeModules->where('updated_at', '>=', now()->subDays(7))->count()
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting module stats', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'total_active' => 0,
                'by_type' => [],
                'recently_updated' => 0
            ];
        }
    }
}