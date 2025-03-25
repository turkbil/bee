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
        
        // ModuleAccessService ile erişim kontrolü yap
        if ($this->moduleAccessService->canAccess($moduleName, $permissionType)) {
            return $next($request);
        }
        
        // Yetkisiz erişim
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
}