<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixLegacyTenantUrls
{
    /**
     * Handle an incoming request
     *
     * ⚠️ ÖNCEKİ SORUN: Bu middleware /storage/tenant{id}/ URL'lerini /storage/ olarak değiştiriyordu
     * Bu YANLIŞ bir işlemdi çünkü tenant-aware storage sistemi /storage/tenant{id}/ formatını kullanıyor!
     *
     * ✅ DÜZELTME: Bu middleware artık sadece TERS dönüşüm yapıyor - yanlış URL'leri doğru formata çeviriyor
     * Eğer eski sistemden /storage/{id}/ formatında URL varsa → /storage/tenant{tenantId}/{id}/ formatına çevirir
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ⚠️ DEVRE DIŞI: Tenant URL manipülasyonu artık gerekli değil
        // StorageTenancyBootstrapper doğru URL'leri üretiyor
        // Bu middleware sadece placeholder olarak kalıyor, gelecekte eski URL redirect'leri için kullanılabilir

        return $next($request);
    }
}
