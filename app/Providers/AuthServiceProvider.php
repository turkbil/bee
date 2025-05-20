<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Services\ModuleAccessService;
use App\Helpers\TenantHelpers;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Modül erişimleri için gate tanımları
        Gate::define('access-module', function ($user, $moduleName, $permissionType = 'view') {
            return app(ModuleAccessService::class)->canAccess($moduleName, $permissionType);
        });

        // Tenant erişimi gate tanımı
        Gate::define('access-tenant', function ($user, $tenantId = null) {
            // Root her zaman erişebilir
            if ($user->isRoot()) {
                return true;
            }
            
            // Mevcut tenant kontrolü
            if (TenantHelpers::isTenant()) {
                $currentTenantId = tenant()->id;
                
                // Belirli bir tenant ID belirtilmişse kontrolü yap
                if ($tenantId !== null && $currentTenantId != $tenantId) {
                    return false;
                }
                
                return $user->isAdmin() || $user->isEditor(); // Tenant'ta admin veya editor rolü olan erişebilir
            }
            
            // Central'da tenant yönetimi sadece root ve admin'e açık
            return $user->isRoot() || $user->isAdmin();
        });
        
        // Rol işlemleri için gate tanımı
        Gate::define('role-action', function ($user, $action = 'view') {
            if ($user->isRoot()) {
                return true;
            }
            
            // Admin kısıtlı rol işlemlerini yapabilir
            if ($user->isAdmin()) {
                // Admin root rolüne müdahale edemez veya silemez
                if (in_array($action, ['create-root', 'update-root', 'delete-root'])) {
                    return false;
                }
                
                // Admin herhangi bir rolü silemez (güvenlik için)
                if ($action === 'delete') {
                    return false;
                }
                
                return true;
            }
            
            return false;
        });
        
        // Kullanıcı işlemleri için gate tanımı
        Gate::define('user-action', function ($user, $targetUser = null, $action = 'view') {
            // Root tüm kullanıcılar üzerinde işlem yapabilir
            if ($user->isRoot()) {
                return true;
            }
            
            // Admin kontrolü
            if ($user->isAdmin()) {
                // Admin root kullanıcıları yönetemez
                if ($targetUser && $targetUser->isRoot()) {
                    return false;
                }
                
                // Admin kendisi üzerinde işlem yapabilir
                if ($targetUser && $targetUser->id === $user->id) {
                    return true;
                }
                
                return true;
            }
            
            // Editor sadece kendi profilini yönetebilir
            if ($user->isEditor() && $targetUser && $targetUser->id === $user->id) {
                // Kendi üzerinde view/update yapabilir, diğer işlemleri yapamaz
                return in_array($action, ['view', 'update']);
            }
            
            return false;
        });
    }
}