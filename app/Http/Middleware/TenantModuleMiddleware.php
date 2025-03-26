<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ModuleAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

class TenantModuleMiddleware
{
    protected $moduleAccessService;
    
    public function __construct(ModuleAccessService $moduleAccessService)
    {
        $this->moduleAccessService = $moduleAccessService;
    }
    
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
        
        Log::info("TenantModuleMiddleware: User: {$user->email}, Role: " . implode(',', $user->getRoleNames()->toArray()) . ", Module: {$moduleName}, Permission: {$permissionType}");
        
        // Root her zaman erişebilir
        if ($user->isRoot()) {
            return $next($request);
        }
        
        // Admin yetki kontrolü
        if ($user->isAdmin()) {
            // Central'da ise ve kısıtlı modüller listesinde mi kontrol et
            if (TenantHelpers::isCentral() && 
                in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                Log::warning("Admin kullanıcısı {$user->id} kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
            }
            
            // Tenant'ta ise modül tenant'a atanmış mı kontrol et
            if (TenantHelpers::isTenant()) {
                $module = $this->moduleAccessService->getModuleByName($moduleName);
                if (!$module) {
                    Log::error("Modül bulunamadı: {$moduleName}");
                    abort(403, 'Modül bulunamadı.');
                }
                
                $isModuleAssigned = $this->moduleAccessService->isModuleAssignedToTenant($module->module_id, tenant()->id);
                if (!$isModuleAssigned) {
                    Log::warning("Admin kullanıcısı {$user->id} tenant'a atanmamış modüle erişmeye çalışıyor: {$moduleName}");
                    abort(403, 'Bu modül bu tenant\'a atanmamış.');
                }
            }
            
            return $next($request);
        }
        
        // Editor ve diğer roller için izin kontrolü
        $canAccess = $this->moduleAccessService->canAccess($moduleName, $permissionType);
        
        if (!$canAccess) {
            Log::warning("Kullanıcı {$user->id} ({$user->email}) yetkisiz erişim denemesi: {$moduleName}.{$permissionType}");
            
            // API isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
                    'module' => $moduleName,
                    'permission' => $permissionType
                ], 403);
            }
            
            // Normal istek ise 403 sayfası göster
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }
        
        return $next($request);
    }
}