<?php

namespace App\Contracts;

interface ModuleAccessServiceInterface
{
    /**
     * Kullanıcının belirtilen modül ve izin tipine erişimi olup olmadığını kontrol eder
     */
    public function canAccess(string $moduleName, string $permissionType = 'view'): bool;
    
    /**
     * Modül tenant'a atanmış mı kontrol eder
     */
    public function isModuleAssignedToTenant(string $moduleId, string $tenantId): bool;
    
    /**
     * Modül adına göre modül modelini getirir
     */
    public function getModuleByName(string $moduleName): ?object;
    
    /**
     * Kullanıcının erişebileceği modülleri listeler
     */
    public function getAccessibleModules(): array;
    
    /**
     * Modül erişim cache'ini temizler
     */
    public function clearModuleAccessCache(?string $userId = null): void;
}