<?php

namespace Modules\UserManagement\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class ModulePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleName
     * @param  string  $permissionType
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduleName, string $permissionType = 'view')
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Kullanıcı aktif değilse erişime izin verme
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Hesabınız pasif durumda. Lütfen yönetici ile iletişime geçin.');
        }
        
        // Eğer 'manage' rotası ve update izni isteniyorsa, ID parametresine göre gerçek izin tipini belirle
        if (strpos($request->path(), 'manage') !== false && $permissionType === 'update') {
            $id = $request->route('id');
            
            // ID yoksa, bu bir create işlemidir
            if ($id === null || $id === '') {
                $permissionType = 'create';
                Log::info("Route ID parametresi bulunmadı, izin tipi 'create' olarak değiştirildi.");
            }
        }
        
        // Modül tenant için aktif mi kontrol et
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $moduleService = app(\App\Services\ModuleAccessService::class);
            $module = $moduleService->getModuleByName($moduleName);
            
            if (!$module) {
                Log::error("ModulePermissionMiddleware: Module not found: {$moduleName}");
                abort(403, 'Erişim reddedildi.');
            }
            
            $isModuleAssigned = $moduleService->isModuleAssignedToTenant($module->module_id, tenant()->id);
            if (!$isModuleAssigned) {
                Log::warning("Kullanıcı {$user->id} ({$user->email}) tenant'a atanmamış modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Erişim reddedildi.');
            }
        }
        
        // İzin var mı kontrol et
        $permissionName = "{$moduleName}.{$permissionType}";
        $permissionExists = Permission::where('name', $permissionName)->where('guard_name', 'web')->exists();
        
        if (!$permissionExists) {
            Log::error("Permission not found: {$permissionName}");
            abort(403, 'Erişim reddedildi.');
        }
        
        Log::info("ModulePermissionMiddleware: User ID: {$user->id}, Email: {$user->email}, Module: {$moduleName}, Permission: {$permissionType}, Roles: " . implode(',', $user->getRoleNames()->toArray()));
        
        // Root yetkisine sahip kullanıcı için hiçbir kısıtlama yok - AMA TENANT'A ATANMAMIŞ MODÜL KONTROLÜ VAR
        if ($user->hasRole('root')) {
            return $next($request);
        }
        
        // Admin ise belirli kısıtlamalar uygula
        if ($user->hasRole('admin')) {
            // Admin için ilave kısıtlama - tenant'ta modülün atanmış olması gerekir
            if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
                // Zaten yukarıda kontrol edildi
            } else {
                // Central'da ise, admin'in erişemeyeceği modülleri kontrol et
                if (in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                    Log::warning("Admin {$user->id} ({$user->email}) kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                    abort(403, 'Erişim reddedildi.');
                }
            }
            
            return $next($request);
        }
        
        // Editor veya diğer roller için, önce kullanıcının modül bazlı izni var mı kontrol et
        if (!$user->hasModulePermission($moduleName, $permissionType)) {
            Log::warning("User {$user->id} ({$user->email}) doesn't have permission {$permissionType} for module {$moduleName}");
            abort(403, 'Erişim reddedildi.');
        }
        
        // Tüm kontroller geçildi, erişime izin ver
        return $next($request);
    }
}