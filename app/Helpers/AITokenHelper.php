<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Models\Tenant;
use Modules\AI\App\Models\AICreditPackage;
use Modules\AI\App\Models\AICreditPurchase;
use Modules\AI\App\Models\AICreditUsage;
use App\Services\AITokenService;

/**
 * AI Token Helper Functions - YENİLENMİŞ SİSTEM
 * 
 * Bu dosya AI token hesaplamalarını merkezi olarak yönetir.
 * Tüm token hesaplama ve yönetim işlemleri bu helper'dan yapılır.
 * 
 * TEMEL FONKSİYONLAR:
 * - ai_get_token_balance(): Mevcut token bakiyesi (satın alınan - harcanan)
 * - ai_get_total_purchased(): Toplam satın alınan token
 * - ai_get_total_used(): Toplam harcanan token
 * - ai_get_token_stats(): Kapsamlı token istatistikleri
 * - ai_widget_token_data(): Widget için hazır veri
 */

if (!function_exists('ai_get_token_balance')) {
    /**
     * Tenant'ın mevcut token bakiyesini getir (DOĞRU HESAPLAMA)
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_token_balance(?string $tenantId = null): int
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        // Cache'den kontrol et
        $cacheKey = "ai_token_balance_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            // Toplam satın alınan kredi miktarı (Eloquent model kullan)
            $totalPurchased = AICreditPurchase::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('credit_amount');
            
            // Harcanan kredi miktarı (Eloquent model kullan)
            $totalUsed = AICreditUsage::where('tenant_id', $tenantId)
                ->sum('credits_used');
            
            return max(0, $totalPurchased - $totalUsed);
        });
    }
}

if (!function_exists('ai_get_total_purchased')) {
    /**
     * Tenant'ın satın aldığı toplam token miktarını getir
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_total_purchased(?string $tenantId = null): int
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $cacheKey = "ai_total_purchased_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            return AICreditPurchase::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('credit_amount');
        });
    }
}

if (!function_exists('ai_get_total_used')) {
    /**
     * Tenant'ın harcadığı toplam token miktarını getir
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_total_used(?string $tenantId = null): int
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $cacheKey = "ai_total_used_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
                ->sum('credits_used');
        });
    }
}

if (!function_exists('ai_get_token_stats')) {
    /**
     * Tenant'ın token kullanım istatistiklerini getir (KOMPLETİ)
     * 
     * @param string|null $tenantId
     * @return array
     */
    function ai_get_token_stats(?string $tenantId = null): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $cacheKey = "ai_token_stats_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            $totalPurchased = ai_get_total_purchased($tenantId);
            $totalUsed = ai_get_total_used($tenantId);
            $remaining = max(0, $totalPurchased - $totalUsed);
            
            // Daily usage (bugünkü kullanım)
            $dailyUsage = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
                ->whereDate('created_at', today())
                ->sum('credits_used');
            
            // Monthly usage (bu ayki kullanım)
            $monthlyUsage = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('credits_used');
            
            $usagePercentage = $totalPurchased > 0 ? round(($totalUsed / $totalPurchased) * 100, 2) : 0;
            $remainingPercentage = $totalPurchased > 0 ? round(($remaining / $totalPurchased) * 100, 2) : 0;
            
            return [
                'total_purchased' => $totalPurchased,
                'total_used' => $totalUsed,
                'remaining' => $remaining,
                'daily_usage' => $dailyUsage,
                'monthly_usage' => $monthlyUsage,
                'usage_percentage' => $usagePercentage,
                'remaining_percentage' => $remainingPercentage,
                'is_running_low' => $remainingPercentage < 20 && $remaining < 1000,
                'is_out_of_tokens' => $remaining <= 0
            ];
        });
    }
}

if (!function_exists('ai_widget_token_data')) {
    /**
     * Widget için token istatistiklerini getir (YENİ DOĞRU SİSTEM)
     * 
     * @param string|null $tenantId
     * @return array
     */
    function ai_widget_token_data(?string $tenantId = null): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $cacheKey = "ai_widget_stats_{$tenantId}";
        
        return Cache::remember($cacheKey, 60, function () use ($tenantId) {
            $stats = ai_get_token_stats($tenantId);
            
            // Widget için ek bilgiler
            $stats['formatted_remaining'] = ai_format_token_count($stats['remaining']);
            $stats['formatted_total'] = ai_format_token_count($stats['total_purchased']);
            $stats['formatted_used'] = ai_format_token_count($stats['total_used']);
            
            // Monthly kullanım bilgileri - şimdilik total_used ile aynı
            $stats['monthly_usage'] = $stats['total_used'];
            $stats['monthly_limit'] = 0; // Limit yok - unlimited
            $stats['daily_usage'] = $stats['total_used']; // Geçici - sonra günlük hesaplama eklenecek
            
            $stats['formatted_daily'] = ai_format_token_count($stats['daily_usage']);
            $stats['formatted_monthly'] = ai_format_token_count($stats['monthly_usage']);
            
            // Widget compatibilty için gerekli key'ler
            $stats['remaining_tokens'] = $stats['remaining'];
            $stats['total_tokens'] = $stats['total_purchased'];
            
            // Provider bilgileri - dinamik olarak al
            try {
                $defaultProvider = \Modules\AI\App\Models\AIProvider::getDefault();
                if ($defaultProvider) {
                    $stats['provider'] = $defaultProvider->name;
                    $stats['provider_active'] = $defaultProvider->is_active;
                } else {
                    $stats['provider'] = 'openai';
                    $stats['provider_active'] = true;
                }
            } catch (\Exception $e) {
                $stats['provider'] = 'openai';
                $stats['provider_active'] = true;
            }
            
            // Durum belirleme
            if ($stats['remaining'] <= 0) {
                $stats['status'] = 'out_of_tokens';
                $stats['status_text'] = 'Token tükendi';
                $stats['status_color'] = 'danger';
            } elseif ($stats['remaining_percentage'] < 20) {
                $stats['status'] = 'running_low';
                $stats['status_text'] = 'Token azalıyor';
                $stats['status_color'] = 'warning';
            } else {
                $stats['status'] = 'sufficient';
                $stats['status_text'] = 'Token yeterli';
                $stats['status_color'] = 'success';
            }
            
            return $stats;
        });
    }
}

if (!function_exists('ai_format_token_count')) {
    /**
     * Token sayısını okunabilir formatta göster
     * 
     * @param int $tokenCount
     * @return string
     */
    function ai_format_token_count(int $tokenCount): string
    {
        if ($tokenCount >= 1000000) {
            $value = $tokenCount / 1000000;
            return ($value == intval($value)) ? intval($value) . 'M' : number_format($value, 1) . 'M';
        } else {
            // Tüm değerler K formatında gösterilecek
            $value = $tokenCount / 1000;
            
            // 100'den küçük değerler için minimum 0.1K göster
            if ($tokenCount < 100 && $tokenCount > 0) {
                return '0.1K';
            }
            
            if ($value >= 1 && $value == intval($value)) {
                return intval($value) . 'K';
            } else {
                return number_format($value, 1) . 'K';
            }
        }
    }
}

if (!function_exists('ai_clear_token_cache')) {
    /**
     * Token cache'ini temizle
     * 
     * @param string|null $tenantId
     * @return void
     */
    function ai_clear_token_cache(?string $tenantId = null): void
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        Cache::forget("ai_token_balance_{$tenantId}");
        Cache::forget("ai_total_purchased_{$tenantId}");
        Cache::forget("ai_total_used_{$tenantId}");
        Cache::forget("ai_token_stats_{$tenantId}");
        Cache::forget("ai_widget_stats_{$tenantId}");
    }
}

// ESKİ SİSTEM FONKSİYONLARI (UYUMLULUK İÇİN)

if (!function_exists('ai_token_balance')) {
    /**
     * Tenant'ın AI token bakiyesini döndür (ESKİ SİSTEM - YENİ SİSTEME YÖNLENDİRİLİYOR)
     */
    function ai_token_balance(?Tenant $tenant = null): int
    {
        $tenantId = $tenant ? $tenant->id : (tenant('id') ?: 'default');
        return ai_get_token_balance($tenantId);
    }
}

if (!function_exists('ai_tokens_remaining_monthly')) {
    /**
     * Bu ay kalan token miktarını döndür
     */
    function ai_tokens_remaining_monthly(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->remaining_monthly_tokens : 0;
    }
}

if (!function_exists('can_use_ai_tokens')) {
    /**
     * AI token kullanılabilir mi kontrol et
     */
    function can_use_ai_tokens(int $tokensNeeded = 1, ?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant || !$tenant->ai_enabled) {
            return false;
        }

        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->canUseTokens($tenant, $tokensNeeded);
    }
}

if (!function_exists('ai_enabled')) {
    /**
     * AI kullanımı aktif mi kontrol et
     */
    function ai_enabled(?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_enabled : false;
    }
}

if (!function_exists('ai_monthly_limit')) {
    /**
     * Aylık AI token limitini döndür
     */
    function ai_monthly_limit(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_monthly_token_limit : 0;
    }
}

if (!function_exists('ai_monthly_usage')) {
    /**
     * Bu ay kullanılan AI token miktarını döndür
     */
    function ai_monthly_usage(?Tenant $tenant = null): int
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_tokens_used_this_month : 0;
    }
}

if (!function_exists('ai_usage_percentage')) {
    /**
     * Aylık kullanım yüzdesini hesapla
     */
    function ai_usage_percentage(?Tenant $tenant = null): float
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant || $tenant->ai_monthly_token_limit <= 0) {
            return 0;
        }

        return min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100);
    }
}

if (!function_exists('ai_last_used')) {
    /**
     * Son AI kullanım tarihini döndür
     */
    function ai_last_used(?Tenant $tenant = null): ?\Carbon\Carbon
    {
        $tenant = $tenant ?? tenant();
        return $tenant ? $tenant->ai_last_used_at : null;
    }
}

if (!function_exists('ai_token_packages')) {
    /**
     * Aktif AI credit paketlerini döndür
     */
    function ai_token_packages(): \Illuminate\Database\Eloquent\Collection
    {
        return AICreditPackage::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    }
}

if (!function_exists('format_token_amount')) {
    /**
     * Token miktarını formatlı şekilde döndür
     */
    function format_token_amount(int $amount): string
    {
        return format_credit($amount);
    }
}

if (!function_exists('estimate_ai_cost')) {
    /**
     * AI işlemi için token maliyetini tahmin et
     */
    function estimate_ai_cost(string $operation, array $params = []): int
    {
        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->estimateTokenCost($operation, $params);
    }
}

if (!function_exists('use_ai_tokens')) {
    /**
     * AI token kullan ve kaydet
     */
    function use_ai_tokens(int $tokensUsed, string $usageType = 'chat', ?string $description = null, ?string $referenceId = null, ?Tenant $tenant = null): bool
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant) {
            return false;
        }

        $aiTokenService = app(AITokenService::class);
        return $aiTokenService->useTokens($tenant, $tokensUsed, $usageType, $description, $referenceId);
    }
}

if (!function_exists('ai_token_status')) {
    /**
     * AI token durumu hakkında özet bilgi döndür
     */
    function ai_token_status(?Tenant $tenant = null): array
    {
        $tenant = $tenant ?? tenant();
        
        if (!$tenant) {
            return [
                'enabled' => false,
                'balance' => 0,
                'monthly_used' => 0,
                'monthly_limit' => 0,
                'monthly_remaining' => 0,
                'usage_percentage' => 0,
                'can_use' => false,
                'last_used' => null
            ];
        }

        return [
            'enabled' => $tenant->ai_enabled,
            'balance' => $tenant->ai_tokens_balance,
            'monthly_used' => $tenant->ai_tokens_used_this_month,
            'monthly_limit' => $tenant->ai_monthly_token_limit,
            'monthly_remaining' => $tenant->remaining_monthly_tokens,
            'usage_percentage' => ai_usage_percentage($tenant),
            'can_use' => can_use_ai_tokens(1, $tenant),
            'last_used' => $tenant->ai_last_used_at
        ];
    }
}

// YENİ SİSTEME AKTARILAN FONKSİYONLAR

if (!function_exists('ai_can_use_tokens')) {
    /**
     * Belirtilen token miktarının kullanılabilir olup olmadığını kontrol et
     * 
     * @param int $tokensNeeded
     * @param string|null $tenantId
     * @return bool
     */
    function ai_can_use_tokens(int $tokensNeeded, ?string $tenantId = null): bool
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        $remaining = ai_get_token_balance($tenantId);
        
        return $remaining >= $tokensNeeded;
    }
}

if (!function_exists('ai_use_tokens')) {
    /**
     * GLOBAL AI MONITORING - Her AI kullanımında otomatik kredi düşme
     * 
     * @param int $tokensUsed Input tokens
     * @param string $module Feature slug (örn: chat, seo-analysis)
     * @param string $action İşlem türü
     * @param string|null $tenantId Tenant ID
     * @param array $metadata Ek veriler
     * @param int $outputTokens Output tokens
     * @param string $userInput Kullanıcı girdisi
     * @param string $aiResponse AI yanıtı
     * @return array Detaylı kullanım sonucu
     */
    function ai_use_tokens(
        int $tokensUsed, 
        string $module, 
        string $action, 
        ?string $tenantId = null, 
        array $metadata = [],
        int $outputTokens = 0,
        string $userInput = '',
        string $aiResponse = ''
    ): array {
        try {
            // Global monitoring service'i al
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            
            // Kredi hesapla (basit formula - geliştirilecek)
            $creditsUsed = ($tokensUsed + $outputTokens) / 1000;
            $creditCost = $creditsUsed * 0.00001;
            
            // Kullanım verileri
            $usageData = [
                'tenant_id' => $tenantId ?: tenant('id') ?: '1',
                'user_id' => auth()->id() ?: 1,
                'feature_slug' => $module,
                'input_tokens' => $tokensUsed,
                'output_tokens' => $outputTokens,
                'credits_used' => $creditsUsed,
                'credit_cost' => $creditCost,
                'user_input' => $userInput,
                'ai_response' => $aiResponse,
                'request_type' => $action,
                ...$metadata
            ];
            
            // Global monitoring service ile kaydet
            $result = $monitoringService->recordAIUsage($usageData);
            
            // Başarılı ise eski format için true döndür
            if ($result['success']) {
                return [
                    'success' => true,
                    'usage_id' => $result['usage_id'],
                    'credits_used' => $result['credits_used'],
                    'remaining_balance' => $result['remaining_balance'],
                    'debug' => $result['debug']
                ];
            }
            
            return [
                'success' => false,
                'error' => $result['message'],
                'error_code' => $result['error_code']
            ];
            
        } catch (\Exception $e) {
            \Log::error('AI Token Usage Failed', [
                'error' => $e->getMessage(),
                'module' => $module,
                'tokens' => $tokensUsed,
                'tenant_id' => $tenantId
            ]);
            
            return [
                'success' => false,
                'error' => 'System error: ' . $e->getMessage(),
                'error_code' => 'system_error'
            ];
        }
    }
}

if (!function_exists('ai_get_usage_history')) {
    /**
     * Tenant'ın token kullanım geçmişini getir
     * 
     * @param string|null $tenantId
     * @param int $limit
     * @return array
     */
    function ai_get_usage_history(?string $tenantId = null, int $limit = 50): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $usage = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
            
        return $usage;
    }
}

if (!function_exists('ai_get_purchase_history')) {
    /**
     * Tenant'ın token satın alma geçmişini getir
     * 
     * @param string|null $tenantId
     * @param int $limit
     * @return array
     */
    function ai_get_purchase_history(?string $tenantId = null, int $limit = 10): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        $purchases = AICreditPurchase::with('package')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
            
        return $purchases;
    }
}

if (!function_exists('ai_refresh_token_stats')) {
    /**
     * Token istatistiklerini yenile (cache'i temizle)
     * 
     * @param string|null $tenantId
     * @return void
     */
    function ai_refresh_token_stats(?string $tenantId = null): void
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        ai_clear_token_cache($tenantId);
    }
}

// ESKİ SİSTEM UYUMLULUK FONKSİYONLARI

if (!function_exists('ai_token_widget_data_old')) {
    /**
     * Widget'larda kullanılmak üzere AI token verilerini döndür (ESKİ SİSTEM)
     */
    function ai_token_widget_data_old(?Tenant $tenant = null): array
    {
        $status = ai_token_status($tenant);
        
        return [
            'balance_formatted' => format_token_amount($status['balance']),
            'monthly_used_formatted' => format_token_amount($status['monthly_used']),
            'monthly_limit_formatted' => $status['monthly_limit'] > 0 ? format_token_amount($status['monthly_limit']) : 'Sınırsız',
            'monthly_remaining_formatted' => format_token_amount($status['monthly_remaining']),
            'usage_percentage' => round($status['usage_percentage'], 1),
            'usage_color' => match(true) {
                $status['usage_percentage'] >= 90 => 'danger',
                $status['usage_percentage'] >= 75 => 'warning',
                $status['usage_percentage'] >= 50 => 'info',
                default => 'success'
            },
            'status_text' => match(true) {
                !$status['enabled'] => 'AI Devre Dışı',
                $status['balance'] <= 0 => 'Token Bitmiş',
                $status['usage_percentage'] >= 100 => 'Aylık Limit Aşıldı',
                $status['usage_percentage'] >= 90 => 'Limit Yaklaşıyor',
                default => 'Normal'
            },
            'last_used_human' => $status['last_used'] ? $status['last_used']->diffForHumans() : 'Hiç kullanılmamış'
        ];
    }
}

if (!function_exists('ai_get_monthly_usage')) {
    /**
     * Bu ayın toplam token kullanımını getir
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_monthly_usage(?string $tenantId = null): int
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('credits_used');
    }
}

if (!function_exists('ai_get_daily_usage')) {
    /**
     * Bugünün toplam token kullanımını getir
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_daily_usage(?string $tenantId = null): int
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->whereDate('created_at', now()->toDateString())
            ->sum('credits_used');
    }
}

// GLOBAL AI MONITORING FONKSİYONLARI

if (!function_exists('ai_record_conversation_usage')) {
    /**
     * Konuşma tabanlı AI kullanımını global monitoring ile kaydet
     */
    function ai_record_conversation_usage(
        int $conversationId,
        string $userMessage,
        string $aiResponse,
        array $tokenData,
        string $featureSlug = 'chat'
    ): array {
        try {
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            return $monitoringService->recordConversationUsage(
                $conversationId,
                $userMessage,
                $aiResponse,
                $tokenData,
                $featureSlug
            );
        } catch (\Exception $e) {
            \Log::error('Conversation usage recording failed', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversationId
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('ai_record_feature_usage')) {
    /**
     * Feature tabanlı AI kullanımını global monitoring ile kaydet
     */
    function ai_record_feature_usage(
        string $featureSlug,
        string $userInput,
        string $aiResponse,
        array $tokenData
    ): array {
        try {
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            return $monitoringService->recordFeatureUsage(
                $featureSlug,
                $userInput,
                $aiResponse,
                $tokenData
            );
        } catch (\Exception $e) {
            \Log::error('Feature usage recording failed', [
                'error' => $e->getMessage(),
                'feature_slug' => $featureSlug
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('ai_get_comprehensive_analytics')) {
    /**
     * Kapsamlı AI analytics verilerini al
     */
    function ai_get_comprehensive_analytics(?string $tenantId = null, array $filters = []): array
    {
        try {
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            return $monitoringService->getComprehensiveAnalytics($tenantId, $filters);
        } catch (\Exception $e) {
            \Log::error('Analytics retrieval failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}

if (!function_exists('ai_get_realtime_metrics')) {
    /**
     * Real-time AI monitoring metrikleri
     */
    function ai_get_realtime_metrics(?string $tenantId = null): array
    {
        try {
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            return $monitoringService->getRealTimeMetrics($tenantId);
        } catch (\Exception $e) {
            \Log::error('Real-time metrics retrieval failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}

if (!function_exists('ai_get_debug_data')) {
    /**
     * AI debug dashboard verileri
     */
    function ai_get_debug_data(?string $tenantId = null, int $limit = 100): array
    {
        try {
            $monitoringService = app(\Modules\AI\App\Services\GlobalAIMonitoringService::class);
            return $monitoringService->getDebugData($tenantId, $limit);
        } catch (\Exception $e) {
            \Log::error('Debug data retrieval failed', ['error' => $e->getMessage()]);
            return [];
        }
    }
}

if (!function_exists('ai_get_credit_balance')) {
    /**
     * Kalan kredi bakiyesini getir (yeni credit sistemi)
     */
    function ai_get_credit_balance(?string $tenantId = null): float
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        try {
            $creditService = app(\Modules\AI\App\Services\AICreditService::class);
            $balance = $creditService->getTenantCreditBalance((int) $tenantId);
            return $balance['remaining_balance'] ?? 0;
        } catch (\Exception $e) {
            \Log::error('Credit balance retrieval failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);
            // Fallback to direct calculation
            $totalPurchased = \Modules\AI\App\Models\AICreditPurchase::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('credit_amount');
            $totalUsed = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
                ->sum('credits_used');
            return max(0, $totalPurchased - $totalUsed);
        }
    }
}

if (!function_exists('ai_get_total_credits_used')) {
    /**
     * Toplam harcanan kredi miktarını getir
     */
    function ai_get_total_credits_used(?string $tenantId = null): float
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        if (!$tenantId) {
            \Log::warning('ai_get_total_credits_used: No tenant context');
            return 0;
        }
        
        return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->sum('credits_used');
    }
}

if (!function_exists('ai_get_total_credits_purchased')) {
    /**
     * Toplam satın alınan kredi miktarını getir
     */
    function ai_get_total_credits_purchased(?string $tenantId = null): float
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        return \Modules\AI\App\Models\AICreditPurchase::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->sum('credit_amount');
    }
}

if (!function_exists('ai_get_monthly_credits_used')) {
    /**
     * Bu ay harcanan kredi miktarını getir
     */
    function ai_get_monthly_credits_used(?string $tenantId = null): float
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->whereYear('used_at', now()->year)
            ->whereMonth('used_at', now()->month)
            ->sum('credits_used');
    }
}

if (!function_exists('ai_get_daily_credits_used')) {
    /**
     * Bugün harcanan kredi miktarını getir
     */
    function ai_get_daily_credits_used(?string $tenantId = null): float
    {
        $tenantId = $tenantId ?: tenant('id') ?: '1';
        
        return \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
            ->whereDate('used_at', today())
            ->sum('credits_used');
    }
}

if (!function_exists('ai_get_active_provider_name')) {
    /**
     * Aktif AI provider adını getir
     */
    function ai_get_active_provider_name(): ?string
    {
        try {
            $provider = \Modules\AI\App\Models\AIProvider::where('is_active', true)
                ->where('is_default', true)
                ->first();
                
            return $provider ? $provider->name : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}