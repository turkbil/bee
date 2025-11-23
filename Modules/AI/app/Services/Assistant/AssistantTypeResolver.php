<?php

namespace Modules\AI\App\Services\Assistant;

use Modules\AI\App\Contracts\ModuleSearchInterface;
use App\Models\AITenantDirective;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Assistant Type Resolver
 *
 * Tenant'a göre doğru modül servislerini çözümler.
 * Birden fazla modül desteği vardır.
 *
 * @package Modules\AI\App\Services\Assistant
 */
class AssistantTypeResolver
{
    /**
     * Modül servis mapping
     * type => service class
     */
    protected array $serviceMap = [
        'shop' => \Modules\AI\App\Services\Assistant\Modules\ShopSearchService::class,
        'content' => \Modules\AI\App\Services\Assistant\Modules\ContentSearchService::class,
        'booking' => \Modules\AI\App\Services\Assistant\Modules\BookingSearchService::class,
        'info' => \Modules\AI\App\Services\Assistant\Modules\InfoSearchService::class,
        'music' => \Modules\AI\App\Services\Assistant\Modules\MusicSearchService::class,
        'generic' => \Modules\AI\App\Services\Assistant\Modules\GenericSearchService::class,
    ];

    /**
     * Tenant için assistant konfigürasyonunu çözümle
     *
     * @param int|null $tenantId
     * @return array
     */
    public function resolve(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? tenant('id');

        // Cache key
        $cacheKey = "ai_assistant_config_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            // Tenant'ın modül tiplerini al
            $moduleTypes = $this->getModuleTypes($tenantId);

            // Her modül için servis çözümle
            $services = [];
            $quickActions = [];
            $promptRules = [];

            foreach ($moduleTypes as $type) {
                $service = $this->resolveService($type, $tenantId);
                if ($service) {
                    $services[$type] = $service;

                    // Quick actions birleştir
                    $moduleActions = $service->getQuickActions();
                    foreach ($moduleActions as $action) {
                        $action['module_type'] = $type;
                        $quickActions[] = $action;
                    }

                    // Prompt rules birleştir
                    $promptRules[$type] = $service->getPromptRules();
                }
            }

            // Primary service (ilk modül)
            $primaryType = $moduleTypes[0] ?? 'generic';
            $primaryService = $services[$primaryType] ?? $this->resolveService('generic', $tenantId);

            return [
                'tenant_id' => $tenantId,
                'module_types' => $moduleTypes,
                'primary_type' => $primaryType,
                'services' => $services,
                'primary_service' => $primaryService,
                'quick_actions' => $quickActions,
                'prompt_rules' => $promptRules,
                'is_multi_module' => count($moduleTypes) > 1,
            ];
        });
    }

    /**
     * Tenant'ın modül tiplerini al
     *
     * @param int|null $tenantId
     * @return array
     */
    public function getModuleTypes(?int $tenantId): array
    {
        try {
            // 1. Önce tenant directive'den dene
            $directive = AITenantDirective::where('tenant_id', $tenantId)
                ->where('directive_key', 'ai_assistant_types')
                ->where('is_active', true)
                ->first();

            if ($directive && $directive->directive_value) {
                $types = json_decode($directive->directive_value, true);
                if (is_array($types) && !empty($types)) {
                    return $types;
                }
            }

            // 2. Eski tek tip directive'i kontrol et (geriye dönük uyumluluk)
            $singleType = AITenantDirective::where('tenant_id', $tenantId)
                ->where('directive_key', 'ai_assistant_type')
                ->where('is_active', true)
                ->first();

            if ($singleType && $singleType->directive_value) {
                return [$singleType->directive_value];
            }

            // 3. Global default kontrol et
            $globalDirective = AITenantDirective::whereNull('tenant_id')
                ->where('directive_key', 'ai_assistant_types')
                ->where('is_active', true)
                ->first();

            if ($globalDirective && $globalDirective->directive_value) {
                $types = json_decode($globalDirective->directive_value, true);
                if (is_array($types) && !empty($types)) {
                    return $types;
                }
            }

            // 4. Fallback: Shop modülü aktif mi kontrol et
            if ($this->hasActiveShopModule($tenantId)) {
                return ['shop'];
            }

            // 5. Son fallback: generic
            return ['generic'];

        } catch (\Exception $e) {
            Log::warning('AssistantTypeResolver: Could not get module types', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return ['generic'];
        }
    }

    /**
     * Modül tipi için servis çözümle
     *
     * @param string $type
     * @param int|null $tenantId
     * @return ModuleSearchInterface|null
     */
    public function resolveService(string $type, ?int $tenantId = null): ?ModuleSearchInterface
    {
        $serviceClass = $this->serviceMap[$type] ?? $this->serviceMap['generic'];

        if (!class_exists($serviceClass)) {
            Log::warning("AssistantTypeResolver: Service class not found", [
                'type' => $type,
                'class' => $serviceClass
            ]);
            // Fallback to generic
            $serviceClass = $this->serviceMap['generic'];
        }

        try {
            return app($serviceClass);
        } catch (\Exception $e) {
            Log::error("AssistantTypeResolver: Could not instantiate service", [
                'type' => $type,
                'class' => $serviceClass,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Kullanıcı mesajına göre en uygun modülü seç
     *
     * @param string $message
     * @param array $config resolve() sonucu
     * @return string Seçilen modül tipi
     */
    public function detectBestModule(string $message, array $config): string
    {
        // Tek modül varsa direkt döndür
        if (!$config['is_multi_module']) {
            return $config['primary_type'];
        }

        $lowerMessage = mb_strtolower($message);

        // Her modül için keyword kontrol
        $moduleScores = [];

        foreach ($config['services'] as $type => $service) {
            $score = 0;

            // Modülün filter detection'ını kullan
            $filters = $service->detectFilters($message);
            if ($filters !== null) {
                $score += 10;
            }

            // Modül tipine göre keyword kontrolü
            $keywords = $this->getModuleKeywords($type);
            foreach ($keywords as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    $score += 5;
                }
            }

            $moduleScores[$type] = $score;
        }

        // En yüksek skorlu modülü seç
        arsort($moduleScores);
        $bestModule = array_key_first($moduleScores);

        // Eğer hiçbir skor yoksa primary döndür
        if ($moduleScores[$bestModule] === 0) {
            return $config['primary_type'];
        }

        return $bestModule;
    }

    /**
     * Modül tipi için anahtar kelimeler
     *
     * @param string $type
     * @return array
     */
    protected function getModuleKeywords(string $type): array
    {
        return match ($type) {
            'shop' => ['ürün', 'fiyat', 'stok', 'satın', 'sipariş', 'sepet', 'katalog'],
            'content' => ['blog', 'yazı', 'makale', 'haber', 'içerik', 'yayın'],
            'booking' => ['randevu', 'rezervasyon', 'tarih', 'saat', 'müsait', 'iptal'],
            'info' => ['bilgi', 'sss', 'soru', 'yardım', 'destek', 'nasıl'],
            'music' => ['şarkı', 'müzik', 'playlist', 'albüm', 'sanatçı', 'dinle'],
            default => [],
        };
    }

    /**
     * Shop modülü aktif mi kontrol et
     *
     * @param int|null $tenantId
     * @return bool
     */
    protected function hasActiveShopModule(?int $tenantId): bool
    {
        try {
            // Shop modülü var mı ve ürün var mı kontrol et
            if (class_exists(\Modules\Shop\App\Models\ShopProduct::class)) {
                return \Modules\Shop\App\Models\ShopProduct::where('is_active', true)->exists();
            }
        } catch (\Exception $e) {
            // Shop modülü yüklü değil
        }
        return false;
    }

    /**
     * Belirli bir tenant için cache'i temizle
     *
     * @param int|null $tenantId
     * @return void
     */
    public static function clearCache(?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? tenant('id');
        Cache::forget("ai_assistant_config_{$tenantId}");
    }

    /**
     * Tüm cache'i temizle
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        // Redis pattern ile temizle veya tüm tenant'lar için tek tek
        Cache::flush(); // Dikkatli kullan!
    }

    /**
     * Yeni modül servisi kaydet
     *
     * @param string $type
     * @param string $serviceClass
     * @return void
     */
    public function registerService(string $type, string $serviceClass): void
    {
        $this->serviceMap[$type] = $serviceClass;
    }
}
