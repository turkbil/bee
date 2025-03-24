<?php

namespace Modules\UserManagement\App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasModulePermissions
{
    /**
     * Kullanıcının belirli bir modül ve izin tipine erişimi olup olmadığını kontrol eder
     */
    public function hasModulePermission(string $moduleName, string $permissionType): bool
    {
        // Root veya Admin kontrolü
        if ($this->hasRole('root') || $this->hasRole('admin')) {
            return true;
        }

        // Önbellekten kontrol et
        $cacheKey = "user_{$this->id}_module_{$moduleName}_permission_{$permissionType}";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName, $permissionType) {
            // 1. Spatie permission kontrolü
            $permissionName = "{$moduleName}.{$permissionType}";
            if ($this->hasPermissionTo($permissionName)) {
                return true;
            }
            
            // 2. Özel modül bazlı izin kontrolü
            return $this->userModulePermissions()
                ->where('module_name', $moduleName)
                ->where('permission_type', $permissionType)
                ->where('is_active', true)
                ->exists();
        });
    }

    /**
     * Kullanıcının bir modüle ait tüm izinlerini getirir
     *
     * @param string $moduleName Modül adı
     * @return array İzin tipleri dizisi
     */
    public function getModulePermissions(string $moduleName): array
    {
        // Önbellekten kontrol et
        $cacheKey = "user_{$this->id}_module_{$moduleName}_permissions";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($moduleName) {
            return $this->userModulePermissions()
                ->where('module_name', $moduleName)
                ->where('is_active', true)
                ->pluck('permission_type')
                ->toArray();
        });
    }

    /**
     * Kullanıcının bir modüle ait belirli izinleri var mı kontrol eder
     *
     * @param string $moduleName Modül adı
     * @param array $permissionTypes İzin tipleri dizisi
     * @return bool Tüm izinlere sahipse true, değilse false
     */
    public function hasModulePermissions(string $moduleName, array $permissionTypes): bool
    {
        // Süper admin kontrolü
        if ($this->hasRole('root') || $this->hasRole('admin')) {
            return true;
        }
        
        $userPermissions = $this->getModulePermissions($moduleName);
        
        foreach ($permissionTypes as $permissionType) {
            if (!in_array($permissionType, $userPermissions)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Kullanıcının tüm modül izinleri ilişkisi
     */
    public function userModulePermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }
    
    /**
     * Kullanıcıya modül izni ekler
     *
     * @param string $moduleName Modül adı
     * @param string|array $permissionTypes İzin tipi veya tipleri
     * @return void
     */
    public function giveModulePermissionTo(string $moduleName, $permissionTypes): void
    {
        if (!is_array($permissionTypes)) {
            $permissionTypes = [$permissionTypes];
        }
        
        foreach ($permissionTypes as $permissionType) {
            $this->userModulePermissions()->updateOrCreate(
                [
                    'module_name' => $moduleName,
                    'permission_type' => $permissionType,
                ],
                [
                    'is_active' => true
                ]
            );
        }
        
        // Önbelleği temizle
        $this->clearModulePermissionCache($moduleName);
    }
    
    /**
     * Kullanıcıdan modül izni kaldırır
     *
     * @param string $moduleName Modül adı
     * @param string|array $permissionTypes İzin tipi veya tipleri
     * @return void
     */
    public function revokeModulePermissionTo(string $moduleName, $permissionTypes): void
    {
        if (!is_array($permissionTypes)) {
            $permissionTypes = [$permissionTypes];
        }
        
        $this->userModulePermissions()
            ->where('module_name', $moduleName)
            ->whereIn('permission_type', $permissionTypes)
            ->delete();
        
        // Önbelleği temizle
        $this->clearModulePermissionCache($moduleName);
    }
    
    /**
     * Kullanıcının modül izinleri önbelleğini temizler
     *
     * @param string $moduleName Modül adı
     * @return void
     */
    private function clearModulePermissionCache(string $moduleName): void
    {
        $permissionTypes = UserModulePermission::getPermissionTypes();
        
        foreach ($permissionTypes as $type => $name) {
            Cache::forget("user_{$this->id}_module_{$moduleName}_permission_{$type}");
        }
        
        Cache::forget("user_{$this->id}_module_{$moduleName}_permissions");
    }
    
    /**
     * Kullanıcının root (tam yetkili) rolü olup olmadığını kontrol eder
     */
    public function isRoot(): bool
    {
        return $this->hasRole('root');
    }
    
    /**
     * Kullanıcının admin rolü olup olmadığını kontrol eder
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Kullanıcının editor rolü olup olmadığını kontrol eder
     */
    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }
}