<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAccessMiddleware
{
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
        
        if ($user->isRoot()) {
            return $next($request);
        }
        
        if ($user->isAdmin()) {
            // Admin için özel kısıtlama varsa burada kontrol edilebilir
            if ($moduleName && in_array($moduleName, config('module-permissions.admin_restricted_modules', []))) {
                Log::warning("Admin kullanıcısı {$user->id} kısıtlı modüle erişmeye çalışıyor: {$moduleName}");
                abort(403, 'Bu modüle erişim yetkiniz bulunmamaktadır.');
            }
            return $next($request);
        }
        
        if ($user->isEditor()) {
            // Editör için atanmış modül kontrolü
            if ($moduleName) {
                $canAccess = app(\App\Services\ModuleAccessService::class)->canAccess($moduleName, 'view');
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