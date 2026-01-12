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
 * Klasör Yapısı:
 * - Tenant2/PromptService.php (ixtif.com)
 * - Tenant2/ProductSearchService.php
 * - Tenant2/DebugHelper.php
 * - Tenant1001/PromptService.php (muzibu.com.tr)
 * - Tenant1001/ProductSearchService.php
 * - Tenant1001/ResponseProcessor.php
 * - Tenant1001/SubscriptionHelper.php
 *
 * Kullanım:
 * - TenantServiceFactory::getPromptService() → Tenant{ID}/PromptService
 * - TenantServiceFactory::getProductSearchService() → Tenant{ID}/ProductSearchService
 * - TenantServiceFactory::processResponse() → Tenant{ID}/ResponseProcessor
 * - TenantServiceFactory::runDebugHelper() → Tenant{ID}/DebugHelper
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
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\PromptService";

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
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\ProductSearchService";

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
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\PromptService";

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
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\ProductSearchService";

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
     * AI yanıtını tenant-specific post-processing ile işler
     *
     * Her tenant kendi ResponseProcessor'ını kullanır:
     * - Tenant1001/ResponseProcessor.php (Muzibu - fiyat/playlist düzeltmeleri)
     * - Tenant2/ResponseProcessor.php (İxtif - varsa)
     *
     * @param string $response AI'dan gelen yanıt
     * @param string $userMessage Kullanıcının mesajı
     * @return string İşlenmiş yanıt
     */
    public static function processResponse(string $response, string $userMessage): string
    {
        $tenantId = self::getCurrentTenantId();

        if (!$tenantId) {
            return $response;
        }

        // Tenant-specific ResponseProcessor var mı kontrol et
        $processorClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\ResponseProcessor";

        if (class_exists($processorClass) && method_exists($processorClass, 'process')) {
            return $processorClass::process($response, $userMessage);
        }

        // Yoksa yanıtı olduğu gibi döndür
        return $response;
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
        $basePath = base_path('Modules/AI/App/Services');

        // Tenant{ID} klasörlerini tara
        $tenantDirs = glob($basePath . '/Tenant*', GLOB_ONLYDIR);
        foreach ($tenantDirs as $dir) {
            $dirName = basename($dir);
            if (preg_match('/Tenant(\d+)/', $dirName, $matches)) {
                $tenantId = $matches[1];
                $files = glob($dir . '/*.php');
                foreach ($files as $file) {
                    $services[$tenantId][] = basename($file, '.php');
                }
            }
        }

        return $services;
    }

    /**
     * Tenant'a özel debug helper çalıştır
     *
     * @param string $method Debug metod adı (logIxtifProducts, logIxtifReferences vb.)
     * @param array $args Metod parametreleri
     * @param int|null $tenantId Tenant ID
     * @return mixed
     */
    public static function runDebugHelper(string $method, array $args = [], ?int $tenantId = null)
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        if (!$tenantId) {
            return null;
        }

        // Tenant-specific DebugHelper var mı kontrol et
        $helperClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\DebugHelper";

        if (class_exists($helperClass) && method_exists($helperClass, $method)) {
            return call_user_func_array([$helperClass, $method], $args);
        }

        return null;
    }

    /**
     * Tenant için DebugHelper var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasDebugHelper(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $helperClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\DebugHelper";

        return class_exists($helperClass);
    }

    /**
     * Tenant'a özel search layer adını döndürür
     *
     * @param int|null $tenantId
     * @return string
     */
    public static function getSearchLayerName(?int $tenantId = null): string
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $helperClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\DebugHelper";

        if (class_exists($helperClass) && method_exists($helperClass, 'getSearchLayerName')) {
            return $helperClass::getSearchLayerName();
        }

        return 'tenant_price_query'; // Default
    }

    /**
     * Tenant'a özel başlık formatlama
     *
     * Her tenant kendi TitleFormatter'ını kullanabilir:
     * - Tenant2/TitleFormatter.php (iXTİF - "2. Ton" → "2 Ton" düzeltmesi)
     *
     * @param string $title Ürün başlığı
     * @param int|null $tenantId Tenant ID
     * @return string Formatlanmış başlık
     */
    public static function formatTitle(string $title, ?int $tenantId = null): string
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        if (!$tenantId) {
            return $title;
        }

        // Tenant-specific TitleFormatter var mı kontrol et
        $formatterClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\TitleFormatter";

        if (class_exists($formatterClass) && method_exists($formatterClass, 'format')) {
            return $formatterClass::format($title);
        }

        // Yoksa başlığı olduğu gibi döndür
        return $title;
    }

    /**
     * Tenant için TitleFormatter var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasTitleFormatter(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $formatterClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\TitleFormatter";

        return class_exists($formatterClass);
    }

    /**
     * Tenant'a özel fiyat sorgusu işleyicisi
     *
     * Fiyat sorguları tenant-specific mantık gerektirir.
     * Örn: iXtif için yedek parça hariç, homepage ürünler önce.
     *
     * @param string $userMessage Kullanıcı mesajı
     * @param int $limit Sonuç limiti
     * @param int|null $tenantId Tenant ID
     * @return array|null Fiyat sorgusu ise sonuçlar, değilse null
     */
    public static function handlePriceQuery(string $userMessage, int $limit = 5, ?int $tenantId = null): ?array
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        if (!$tenantId) {
            return null;
        }

        // Tenant-specific ProductSearchService var mı?
        $productSearchService = self::getProductSearchService($tenantId);

        if ($productSearchService && method_exists($productSearchService, 'handlePriceQuery')) {
            return $productSearchService->handlePriceQuery($userMessage, $limit);
        }

        // Tenant'a özel price query handler yok
        return null;
    }

    /**
     * Tenant için özel fiyat sorgusu desteği var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasPriceQueryHandler(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        if (!$tenantId) {
            return false;
        }

        $productSearchService = self::getProductSearchService($tenantId);

        return $productSearchService && method_exists($productSearchService, 'handlePriceQuery');
    }

    /**
     * Tenant'a özel kullanıcı abonelik durumu
     *
     * Her tenant kendi SubscriptionHelper'ını kullanabilir:
     * - Tenant1001/SubscriptionHelper.php (Muzibu - Premium/Free/Guest)
     *
     * AI context'e kullanıcı durumu eklemek için kullanılır.
     * Premium olmayan kullanıcılara playlist/dinle özelliği sunulmamalı.
     *
     * @param int|null $tenantId Tenant ID
     * @return array|null Abonelik durumu veya null
     */
    public static function getUserSubscriptionContext(?int $tenantId = null): ?array
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();

        if (!$tenantId) {
            return null;
        }

        // Tenant-specific SubscriptionHelper var mı kontrol et
        $helperClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\SubscriptionHelper";

        if (class_exists($helperClass) && method_exists($helperClass, 'getSubscriptionStatus')) {
            return $helperClass::getSubscriptionStatus(auth()->user());
        }

        // Yoksa null döndür - subscription helper olmayan tenant'lar için
        return null;
    }

    /**
     * Tenant için SubscriptionHelper var mı kontrol eder
     *
     * @param int|null $tenantId
     * @return bool
     */
    public static function hasSubscriptionHelper(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? self::getCurrentTenantId();
        $helperClass = "\\Modules\\AI\\App\\Services\\Tenant{$tenantId}\\SubscriptionHelper";

        return class_exists($helperClass);
    }
}
