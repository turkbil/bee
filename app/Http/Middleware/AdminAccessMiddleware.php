<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ModuleAccessService;
use App\Helpers\TenantHelpers;

class AdminAccessMiddleware
{
    protected $moduleAccessService;
    
    public function __construct(ModuleAccessService $moduleAccessService)
    {
        $this->moduleAccessService = $moduleAccessService;
    }
    
    public function handle(Request $request, Closure $next)
    {
        // En azından bir rol kontrolü yapalım
        if (!auth()->check()) {
            Log::warning('Kimlik doğrulama yapılmamış kullanıcı admin erişimi deniyor');
            abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }
        
        $user = auth()->user();
        $path = $request->path();
        
        // Hangi modülü erişmeye çalıştığını belirle
        preg_match('/admin\/([a-zA-Z0-9_]+)/', $path, $matches);
        $moduleName = $matches[1] ?? null;
        
        Log::info("Kullanıcı {$user->id} erişim deniyor: {$path}, Modül: {$moduleName}");
        
        // Root her yere erişebilir
        if ($user->isRoot()) {
            return $next($request);
        }
        
        // Admin izin kontrolü
        if ($user->isAdmin()) {
            // Central ise ve kısıtlı bir modüle erişmeye çalışıyorsa kontrol et
            if (TenantHelpers::isCentral() && 
                $moduleName && 
                in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                Log::warning("Admin kullanıcısı {$user->id} kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
            }
            
            // Tenant ise, tenant'a atanan modüllere erişebilir
            if (TenantHelpers::isTenant() && $moduleName) {
                $module = $this->moduleAccessService->getModuleByName($moduleName);
                if (!$module || !$this->moduleAccessService->isModuleAssignedToTenant($module->module_id, tenant()->id)) {
                    Log::warning("Admin kullanıcısı {$user->id} tenant'a atanmamış modüle erişmeye çalışıyor: {$moduleName}");
                    abort(403, 'Bu modül bu tenant\'a atanmamış.');
                }
            }
            
            return $next($request);
        }
        
        // Editor rolü kontrolü - modüle özel izinleri var mı kontrol et
        if ($user->isEditor() && $moduleName) {
            $canAccess = $this->moduleAccessService->canAccess($moduleName, 'view');
            if (!$canAccess) {
                Log::warning("Editör kullanıcısı {$user->id} yetkisiz modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
            }
            return $next($request);
        }
        
        abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
    }
}