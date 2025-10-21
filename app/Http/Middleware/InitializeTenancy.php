<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;

class InitializeTenancy extends BaseMiddleware
{
    protected $tenancy;
    protected $resolver;
    
    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }
    
    public function handle($request, Closure $next)
    {
        // Telescope route'larını skip et - tenant context'i gerektirmiyor
        if ($request->is('telescope') || $request->is('telescope/*')) {
            return $next($request);
        }

        // Tenancy zaten başlatılmışsa tekrar başlatma
        if ($this->tenancy->initialized) {
            return $next($request);
        }

        // API routes için header-based tenant detection
        if ($request->is('api/*')) {
            return $this->handleApiTenancy($request, $next);
        }
        
        $host = $request->getHost();
        // www prefix'i kaldır (tenant domain matching için)
        $host = preg_replace('/^www\./', '', $host);

        // Central domainleri config'den kontrol et
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($host, $centralDomains)) {
            // Config'de tanımlı central domain ise tenancy başlatma
            return $next($request);
        }
        
        try {
            // Cache key oluştur - 15 dakika cache
            $cacheKey = "tenant_domain_data:{$host}";
            
            $domainModel = Cache::remember($cacheKey, 60 * 15, function() use ($host) {
                // Stancl API ile domain ve tenant bilgilerini al
                return Domain::with('tenant')->where('domain', $host)->first();
            });
            
            if (!$domainModel || !$domainModel->tenant) {
                // Tenant bulunamadı - özel error sayfası göster
                return response()->view('errors.tenant-not-found', [
                    'domain' => $host,
                    'message' => 'Bu domain için aktif bir site bulunamadı.'
                ], 404);
            }
            
            $tenant = $domainModel->tenant;
            
            // Tenant pasif ise admin ve login hariç offline sayfasına yönlendir
            if (!$tenant->is_active) {
                // Sadece admin ve login rotalarına izin ver
                if ($request->is('admin') || $request->is('admin/*') || $request->is('login')) {
                    // Admin sayfalarına devam et
                } else {
                    return response()->view('errors.offline', ['domain' => $host], 503);
                }
            }
            
            // Tenant central ise de tenancy başlat (DB değiştirmez ama tenant() helper çalışır)
            if ($tenant->central) {
                // Central tenant için özel başlatma - database'i değiştirmez
                $this->tenancy->initialize($tenant);
                return $next($request);
            }
            
            // Normal tenant'ı başlat
            $this->tenancy->initialize($tenant);

            // 🔥 Dinamik tenant disk registration
            $this->registerTenantDisk($tenant);

            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Tenant başlatma hatası: ' . $e->getMessage(), [
                'host' => $host,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Genel hata sayfası göster
            return response()->view('errors.tenant-not-found', [
                'domain' => $host,
                'message' => 'Site yüklenirken bir hata oluştu.'
            ], 500);
        }
    }
    
    protected function handleApiTenancy($request, Closure $next)
    {
        // API için domain-based tenant detection
        $host = $request->getHost();
        // www prefix'i kaldır (tenant domain matching için)
        $host = preg_replace('/^www\./', '', $host);

        // Central domainleri config'den kontrol et
        $centralDomains = config('tenancy.central_domains', []);

        if (in_array($host, $centralDomains)) {
            // Config'de tanımlı central domain ise tenancy başlatma
            return $next($request);
        }

        try {
            // Cache key oluştur - 15 dakika cache
            $cacheKey = "tenant_domain_data:{$host}";

            $domainModel = Cache::remember($cacheKey, 60 * 15, function() use ($host) {
                return Domain::with('tenant')->where('domain', $host)->first();
            });

            if (!$domainModel || !$domainModel->tenant) {
                // API için JSON error döndür
                return response()->json([
                    'error' => 'Tenant not found',
                    'message' => 'No active tenant found for this domain'
                ], 404);
            }

            $tenant = $domainModel->tenant;

            // Tenant'ı başlat
            $this->tenancy->initialize($tenant);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('API Tenant initialization error: ' . $e->getMessage(), [
                'host' => $host,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Error initializing tenant'
            ], 500);
        }
    }

    /**
     * ⚠️ KRİTİK: Tenant disk konfigürasyonunu runtime'da oluşturur
     *
     * Bu method Spatie Media Library ve dosya yönetimi için ZORUNLUDUR!
     * SİLME, DEĞİŞTİRME veya DEVRE DIŞI BIRAKMA!
     *
     * Neden gerekli:
     * - Her tenant için ayrı disk (tenant1, tenant2, tenant3...)
     * - Hardcode yerine dinamik registration (1000+ tenant için)
     * - Media URL'lerin doğru oluşması için
     * - 403 Forbidden hatalarını önlemek için
     *
     * @param \App\Models\Tenant $tenant
     * @return void
     */
    protected function registerTenantDisk($tenant): void
    {
        $tenantKey = $tenant->id;
        $tenantDiskName = "tenant{$tenantKey}";
        $root = storage_path("tenant{$tenantKey}/app/public");

        // 🔥 Request'ten gerçek URL al (config('app.url') yanlış domain döndürüyor!)
        $appUrl = request() ? request()->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

        // Disk konfigürasyonunu runtime'da ekle
        Config::set("filesystems.disks.{$tenantDiskName}", [
            'driver' => 'local',
            'root' => $root,
            'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantKey}" : null,
            'visibility' => 'public',
            'throw' => false,
        ]);

        // Storage facade'ı yeniden yükle (cache temizliği için)
        app()->forgetInstance('filesystem');
    }
}