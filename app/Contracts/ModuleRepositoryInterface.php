<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ModuleRepositoryInterface
{
    /**
     * Tenant için modülleri getirir
     */
    public function getModulesForTenant(?string $tenantId = null): Collection;
    
    /**
     * Aktif modülleri getirir
     */
    public function getActiveModules(): Collection;
    
    /**
     * Modül tenant atamalarını getirir
     */
    public function getTenantModuleAssignments(string $tenantId): Collection;
    
    /**
     * Modül cache'ini temizler
     */
    public function clearModuleCache(?string $tenantId = null): void;
    
    /**
     * ID ile modül getirir
     */
    public function findById(int $moduleId): ?object;
    
    /**
     * İsim ile modül getirir
     */
    public function findByName(string $moduleName): ?object;
}