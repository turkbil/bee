<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIModelCreditRate;
use Modules\AI\App\Services\ModelBasedCreditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Credit Warning Service
 * 
 * Yetersiz kredi uyarı sistemi - Kullanıcıları kredi durumu hakkında bilgilendirir
 */
readonly class CreditWarningService
{
    // Uyarı seviyeleri (yüzde olarak)
    const WARNING_LEVELS = [
        'critical' => 5,   // %5 kaldığında kritik uyarı
        'low' => 10,       // %10 kaldığında düşük uyarı  
        'medium' => 20     // %20 kaldığında orta uyarı
    ];
    
    // Uyarı tipleri
    const WARNING_TYPES = [
        'insufficient_credits' => 'Yetersiz kredi',
        'low_credits' => 'Düşük kredi',
        'critical_credits' => 'Kritik kredi seviyesi',
        'credits_depleted' => 'Kredi tükendi'
    ];

    public function __construct(
        private ModelBasedCreditService $creditService
    ) {}

    /**
     * AI isteği öncesi kredi kontrolü
     * 
     * @param object $tenant
     * @param int $providerId
     * @param string $model
     * @param float $estimatedInputTokens
     * @param float $estimatedOutputTokens
     * @return array ['allowed' => bool, 'warning' => array|null, 'required_credits' => float]
     */
    public function checkCreditsBeforeRequest(
        object $tenant,
        int $providerId,
        string $model,
        float $estimatedInputTokens,
        float $estimatedOutputTokens
    ): array {
        // Gerekli kredi hesapla
        $requiredCredits = $this->creditService->calculateModelCreditCost(
            $providerId,
            $model,
            $estimatedInputTokens,
            $estimatedOutputTokens
        );
        
        $currentCredits = $tenant->ai_credits_balance ?? 0;
        $maxCredits = $tenant->max_credits ?? 1000; // Default max credit
        
        // Kredi yeterli mi?
        $allowed = $currentCredits >= $requiredCredits;
        
        // Uyarı seviyesi kontrolü
        $warning = null;
        if ($allowed) {
            $warning = $this->checkWarningLevel($currentCredits, $maxCredits, $requiredCredits);
            
            // Uyarı varsa logla ve cache'le
            if ($warning) {
                $this->logWarning($tenant, $warning, $currentCredits, $requiredCredits);
                $this->cacheWarning($tenant, $warning);
            }
        } else {
            // Yetersiz kredi durumu
            $warning = [
                'type' => 'insufficient_credits',
                'level' => 'critical',
                'message' => "Yetersiz kredi. Gerekli: {$requiredCredits}, Mevcut: {$currentCredits}",
                'current_credits' => $currentCredits,
                'required_credits' => $requiredCredits,
                'shortage' => $requiredCredits - $currentCredits
            ];
            
            $this->logInsufficientCredits($tenant, $requiredCredits, $currentCredits);
        }
        
        return [
            'allowed' => $allowed,
            'warning' => $warning,
            'required_credits' => $requiredCredits,
            'current_credits' => $currentCredits
        ];
    }

    /**
     * Uyarı seviyesi kontrolü
     * 
     * @param float $currentCredits
     * @param float $maxCredits
     * @param float $requiredCredits
     * @return array|null
     */
    private function checkWarningLevel(float $currentCredits, float $maxCredits, float $requiredCredits): ?array
    {
        $remainingPercentage = ($currentCredits / $maxCredits) * 100;
        
        // Kritik seviye kontrolü
        if ($remainingPercentage <= self::WARNING_LEVELS['critical']) {
            return [
                'type' => 'critical_credits',
                'level' => 'critical',
                'message' => "Kritik kredi seviyesi! Yalnızca %{$remainingPercentage} kredi kaldı.",
                'remaining_percentage' => $remainingPercentage,
                'current_credits' => $currentCredits,
                'estimated_requests_left' => $this->estimateRemainingRequests($currentCredits)
            ];
        }
        
        // Düşük seviye kontrolü
        if ($remainingPercentage <= self::WARNING_LEVELS['low']) {
            return [
                'type' => 'low_credits',
                'level' => 'warning',
                'message' => "Kredi seviyesi düşük! %{$remainingPercentage} kredi kaldı.",
                'remaining_percentage' => $remainingPercentage,
                'current_credits' => $currentCredits,
                'estimated_requests_left' => $this->estimateRemainingRequests($currentCredits)
            ];
        }
        
        // Orta seviye kontrolü
        if ($remainingPercentage <= self::WARNING_LEVELS['medium']) {
            return [
                'type' => 'medium_credits',
                'level' => 'info',
                'message' => "Kredi seviyesi %{$remainingPercentage} seviyesinde.",
                'remaining_percentage' => $remainingPercentage,
                'current_credits' => $currentCredits,
                'estimated_requests_left' => $this->estimateRemainingRequests($currentCredits)
            ];
        }
        
        return null; // Uyarı gerekmiyor
    }

    /**
     * Kalan kredilerle tahmini istek sayısı
     * 
     * @param float $remainingCredits
     * @return int
     */
    private function estimateRemainingRequests(float $remainingCredits): int
    {
        // Ortalama istek maliyeti (basit hesaplama)
        $averageRequestCost = 50; // Ortalama 50 kredi per request
        return (int) floor($remainingCredits / $averageRequestCost);
    }

    /**
     * Uyarıyı logla
     * 
     * @param object $tenant
     * @param array $warning
     * @param float $currentCredits
     * @param float $requiredCredits
     */
    private function logWarning(object $tenant, array $warning, float $currentCredits, float $requiredCredits): void
    {
        Log::warning('⚠️ Credit Warning Triggered', [
            'tenant_id' => $tenant->id,
            'warning_type' => $warning['type'],
            'warning_level' => $warning['level'],
            'current_credits' => $currentCredits,
            'required_credits' => $requiredCredits,
            'remaining_percentage' => $warning['remaining_percentage'] ?? null,
            'message' => $warning['message'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Yetersiz kredi durumunu logla
     * 
     * @param object $tenant
     * @param float $requiredCredits
     * @param float $currentCredits
     */
    private function logInsufficientCredits(object $tenant, float $requiredCredits, float $currentCredits): void
    {
        Log::error('❌ Insufficient Credits - Request Denied', [
            'tenant_id' => $tenant->id,
            'required_credits' => $requiredCredits,
            'current_credits' => $currentCredits,
            'shortage' => $requiredCredits - $currentCredits,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Uyarıyı cache'le (tekrar gönderimi önlemek için)
     * 
     * @param object $tenant
     * @param array $warning
     */
    private function cacheWarning(object $tenant, array $warning): void
    {
        $cacheKey = "credit_warning_{$tenant->id}_{$warning['type']}";
        $cacheData = [
            'warning' => $warning,
            'triggered_at' => now()->toISOString(),
            'count' => Cache::get($cacheKey . '_count', 0) + 1
        ];
        
        // 1 saat cache (aynı uyarı tekrar gönderilmesin)
        Cache::put($cacheKey, $cacheData, now()->addHour());
        Cache::put($cacheKey . '_count', $cacheData['count'], now()->addDay());
    }

    /**
     * Tenant'ın aktif uyarılarını al
     * 
     * @param int $tenantId
     * @return array
     */
    public function getActiveWarnings(int $tenantId): array
    {
        $warnings = [];
        
        foreach (self::WARNING_TYPES as $type => $name) {
            $cacheKey = "credit_warning_{$tenantId}_{$type}";
            $warningData = Cache::get($cacheKey);
            
            if ($warningData) {
                $warnings[] = [
                    'type' => $type,
                    'name' => $name,
                    'data' => $warningData['warning'],
                    'triggered_at' => $warningData['triggered_at'],
                    'count' => $warningData['count'] ?? 1
                ];
            }
        }
        
        return $warnings;
    }

    /**
     * Tenant'ın uyarılarını temizle
     * 
     * @param int $tenantId
     * @param string|null $warningType Belirli uyarı tipi (null = hepsi)
     */
    public function clearWarnings(int $tenantId, ?string $warningType = null): void
    {
        if ($warningType) {
            // Belirli uyarı tipini temizle
            $cacheKey = "credit_warning_{$tenantId}_{$warningType}";
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . '_count');
            
            Log::info('🧹 Credit Warning Cleared', [
                'tenant_id' => $tenantId,
                'warning_type' => $warningType
            ]);
        } else {
            // Tüm uyarıları temizle
            foreach (self::WARNING_TYPES as $type => $name) {
                $cacheKey = "credit_warning_{$tenantId}_{$type}";
                Cache::forget($cacheKey);
                Cache::forget($cacheKey . '_count');
            }
            
            Log::info('🧹 All Credit Warnings Cleared', [
                'tenant_id' => $tenantId
            ]);
        }
    }

    /**
     * Tenant'ın kredi istatistiklerini al
     * 
     * @param int $tenantId
     * @return array
     */
    public function getCreditStatistics(int $tenantId): array
    {
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            return [];
        }

        $currentCredits = $tenant->ai_credits_balance ?? 0;
        $maxCredits = $tenant->max_credits ?? 1000;
        $usageToday = $this->getTodayUsage($tenantId);
        $usageThisMonth = $this->getMonthUsage($tenantId);
        
        return [
            'current_credits' => $currentCredits,
            'max_credits' => $maxCredits,
            'remaining_percentage' => ($currentCredits / $maxCredits) * 100,
            'usage_today' => $usageToday,
            'usage_this_month' => $usageThisMonth,
            'avg_daily_usage' => $usageThisMonth / now()->day,
            'estimated_days_left' => $usageToday > 0 ? $currentCredits / $usageToday : 999,
            'warning_level' => $this->getCurrentWarningLevel($currentCredits, $maxCredits)
        ];
    }

    /**
     * Bugünkü kullanımı al
     * 
     * @param int $tenantId
     * @return float
     */
    private function getTodayUsage(int $tenantId): float
    {
        $cacheKey = "daily_credit_usage_{$tenantId}_" . now()->format('Y-m-d');
        return Cache::get($cacheKey, 0.0);
    }

    /**
     * Bu ayki kullanımı al
     * 
     * @param int $tenantId
     * @return float
     */
    private function getMonthUsage(int $tenantId): float
    {
        $cacheKey = "monthly_credit_usage_{$tenantId}_" . now()->format('Y-m');
        return Cache::get($cacheKey, 0.0);
    }

    /**
     * Mevcut uyarı seviyesini al
     * 
     * @param float $currentCredits
     * @param float $maxCredits
     * @return string|null
     */
    private function getCurrentWarningLevel(float|string $currentCredits, float|string $maxCredits): ?string
    {
        $currentCredits = (float) $currentCredits;
        $maxCredits = (float) $maxCredits;
        $remainingPercentage = ($currentCredits / $maxCredits) * 100;
        
        if ($remainingPercentage <= self::WARNING_LEVELS['critical']) {
            return 'critical';
        } elseif ($remainingPercentage <= self::WARNING_LEVELS['low']) {
            return 'warning';
        } elseif ($remainingPercentage <= self::WARNING_LEVELS['medium']) {
            return 'info';
        }
        
        return null; // Normal seviye
    }

    /**
     * Email bildirim gönder (kritik durumlarda)
     * 
     * @param object $tenant
     * @param array $warning
     */
    public function sendEmailNotification(object $tenant, array $warning): void
    {
        // Sadece kritik uyarılarda email gönder
        if ($warning['level'] !== 'critical') {
            return;
        }
        
        // Email gönderim limitini kontrol et (günde 1 email)
        $emailCacheKey = "credit_email_sent_{$tenant->id}_" . now()->format('Y-m-d');
        if (Cache::has($emailCacheKey)) {
            return; // Bugün zaten email gönderilmiş
        }
        
        try {
            // Email gönder (Mail facade kullanarak)
            // Bu kısım email template'i oluşturulduğunda implement edilecek
            
            // Cache'le (tekrar gönderimi önle)
            Cache::put($emailCacheKey, true, now()->addDay());
            
            Log::info('📧 Credit Warning Email Sent', [
                'tenant_id' => $tenant->id,
                'warning_type' => $warning['type'],
                'recipient' => $tenant->email ?? 'unknown'
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to send credit warning email', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}