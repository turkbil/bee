<?php

namespace Modules\UserManagement\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Root yetkisine sahip kullanıcı için hiçbir kısıtlama yok
        if ($user->isRoot()) {
            return $next($request);
        }
        
        // Admin ise ve gereken yetki var mı kontrol et
        if ($user->isAdmin()) {
            // Admin için belirli kısıtlamalar varsa buraya eklenebilir
            return $next($request);
        }
        
        // Editör ise modül bazlı yetki kontrolü yap
        if ($user->isEditor() && $user->hasModulePermission($moduleName, $permissionType)) {
            return $next($request);
        }
        
        // Hiçbir koşul sağlanmadıysa erişimi reddet
        abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
    }
}