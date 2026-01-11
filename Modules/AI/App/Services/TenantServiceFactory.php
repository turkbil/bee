<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Contracts\TenantPromptServiceInterface;
use Modules\AI\App\Services\Tenant\DefaultPromptService;

/**
 * Tenant Service Factory
 *
 * Tenant ID'ye göre otomatik olarak doğru servisi bulur.
 * Yeni tenant eklendiğinde KOD DEĞİŞMEZ - sadece yeni service dosyası oluşturulur.
 *
 * Örnek:
 * - Tenant 2 → Tenant2PromptService (varsa)
 * - Tenant 1001 → Tenant1001PromptService (varsa)
 * - Tenant 999 → DefaultPromptService (yoksa)
 */
class TenantServiceFactory
{
    /**
     * Cached service instances
     */
    private static array $instances = [];

    /**
     * Tenant'a özel PromptService'i döndürür
     *
     * @param int|null $tenantId Tenant ID (null ise mevcut tenant kullanılır)
     * @return TenantPromptServiceInterface
     */
    public static function getPromptService(?int $tenantId = null): TenantPromptServiceInterface
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        // Cache kontrolü
        $cacheKey = "prompt_service_{$tenantId}";
        if (isset(self::$instances[$cacheKey])) {
            return self::$instances[$cacheKey];
        }

        // Tenant-specific service var mı kontrol et
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}PromptService";

        if (class_exists($serviceClass)) {
            $service = app($serviceClass);
            \Log::debug("TenantServiceFactory: Loaded {$serviceClass}");
        } else {
            // Yoksa default service kullan
            $service = app(DefaultPromptService::class);
            \Log::debug("TenantServiceFactory: Using DefaultPromptService for tenant {$tenantId}");
        }

        // Cache'e kaydet
        self::$instances[$cacheKey] = $service;

        return $service;
    }

    /**
     * Tenant'a özel ProductSearchService'i döndürür
     *
     * @param int|null $tenantId Tenant ID
     * @return mixed ProductSearchService veya null
     */
    public static function getProductSearchService(?int $tenantId = null)
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        // Cache kontrolü
        $cacheKey = "product_search_{$tenantId}";
        if (isset(self::$instances[$cacheKey])) {
            return self::$instances[$cacheKey];
        }

        // Tenant-specific service var mı kontrol et
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}ProductSearchService";

        if (class_exists($serviceClass)) {
            $service = app($serviceClass);
            \Log::debug("TenantServiceFactory: Loaded {$serviceClass}");
        } else {
            // Yoksa null döndür - default product search yok
            $service = null;
            \Log::debug("TenantServiceFactory: No ProductSearchService for tenant {$tenantId}");
        }

        // Cache'e kaydet
        self::$instances[$cacheKey] = $service;

        return $service;
    }

    /**
     * Tenant için prompt service var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasPromptService(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}PromptService";

        return class_exists($serviceClass);
    }

    /**
     * Tenant için product search service var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasProductSearchService(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}ProductSearchService";

        return class_exists($serviceClass);
    }

    /**
     * Mevcut tenant ID'sini döndürür
     *
     * @return int|null
     */
    public static function getCurrentTenantId(): ?int
    {
        return tenant('id') ?? null;
    }

    /**
     * Cache'i temizler (testing için)
     */
    public static function clearCache(): void
    {
        self::$instances = [];
    }

    /**
     * Tüm mevcut tenant service'lerini listeler
     *
     * @return array
     */
    public static function listAvailableServices(): array
    {
        $services = [];
        $path = base_path('Modules/AI/app/Services/Tenant');

        if (is_dir($path)) {
            $files = glob($path . '/Tenant*Service.php');
            foreach ($files as $file) {
                $filename = basename($file, '.php');
                if (preg_match('/Tenant(\d+)(\w+)Service/', $filename, $matches)) {
                    $tenantId = $matches[1];
                    $type = $matches[2];
                    $services[$tenantId][] = $type;
                }
            }
        }

        return $services;
    }
}
