<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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

        // Hangi modülü erişmeye çalıştığını belirle - alt yolları da yakala
        preg_match('/^admin\/([^\/]+)/', $path, $matches);
        $moduleName = $matches[1] ?? null;

        // Path-to-module mapping (bazı path'ler farklı modüllere ait)
        $pathModuleMap = [
            'orders' => 'cart',     // /admin/orders → Cart modülü
            'checkout' => 'cart',   // /admin/checkout → Cart modülü
        ];

        if ($moduleName && isset($pathModuleMap[$moduleName])) {
            $moduleName = $pathModuleMap[$moduleName];
        }

        // Root her yere erişebilir
        if ($user->isRoot()) {
            return $next($request);
        }
        
        // Admin izin kontrolü
        if ($user->isAdmin()) {
            // Dashboard, cache, debug gibi sistem route'ları için modül kontrolü yapma
            $systemRoutes = ['dashboard', 'cache', 'debug', 'ai', 'language', 'access-denied'];
            if (in_array($moduleName, $systemRoutes)) {
                return $next($request);
            }

            // Central ise ve kısıtlı bir modüle erişmeye çalışıyorsa kontrol et
            if (TenantHelpers::isCentral() &&
                $moduleName &&
                in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                Log::warning("Admin kullanıcısı {$user->id} kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
            }

            // Tenant ise, tenant'a atanan modüllere erişebilir
            if (TenantHelpers::isTenant() && $moduleName) {
                // Cache ile modül erişim kontrolü - 10 dakika
                $cacheKey = "module_tenant_access:{$moduleName}:" . tenant()->id;
                
                $hasAccess = Cache::remember($cacheKey, 60 * 10, function() use ($moduleName) {
                    $module = $this->moduleAccessService->getModuleByName($moduleName);
                    return $module && $this->moduleAccessService->isModuleAssignedToTenant($module->module_id, tenant()->id);
                });
                
                if (!$hasAccess) {
                    Log::warning("Admin kullanıcısı {$user->id} tenant'a atanmamış modüle erişmeye çalışıyor: {$moduleName}");
                    abort(403, 'Bu modül bu tenant\'a atanmamış.');
                }
            }
            
            return $next($request);
        }
        
        // Editor rolü kontrolü - modüle özel izinleri var mı kontrol et
        if ($user->isEditor()) {
            // Dashboard, cache, debug gibi sistem route'ları için modül kontrolü yapma
            $systemRoutes = ['dashboard', 'cache', 'debug', 'ai', 'language', 'access-denied'];
            if (in_array($moduleName, $systemRoutes)) {
                return $next($request);
            }

            // Modül erişim kontrolü
            if ($moduleName) {
                $canAccess = $this->moduleAccessService->canAccess($moduleName, 'view');
                if (!$canAccess) {
                    Log::warning("Editör kullanıcısı {$user->id} yetkisiz modüle erişmeye çalışıyor: {$moduleName}");
                    abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
                }
            }
            return $next($request);
        }
        
        abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
    }
}