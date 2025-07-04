<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        // Cache'den kontrol et
        $cacheKey = "ai_token_balance_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            // Toplam satın alınan token miktarı
            $totalPurchased = DB::table('ai_token_purchases')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('token_amount');
            
            // Harcanan token miktarı
            $totalUsed = DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->sum('tokens_used');
            
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $cacheKey = "ai_total_purchased_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            return DB::table('ai_token_purchases')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('token_amount');
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $cacheKey = "ai_total_used_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            return DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->sum('tokens_used');
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $cacheKey = "ai_token_stats_{$tenantId}";
        
        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            $totalPurchased = ai_get_total_purchased($tenantId);
            $totalUsed = ai_get_total_used($tenantId);
            $remaining = max(0, $totalPurchased - $totalUsed);
            
            // Daily usage (bugünkü kullanım)
            $dailyUsage = DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->whereDate('created_at', today())
                ->sum('tokens_used');
            
            // Monthly usage (bu ayki kullanım)
            $monthlyUsage = DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('tokens_used');
            
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $cacheKey = "ai_widget_stats_{$tenantId}";
        
        return Cache::remember($cacheKey, 60, function () use ($tenantId) {
            $stats = ai_get_token_stats($tenantId);
            
            // Widget için ek bilgiler
            $stats['formatted_remaining'] = ai_format_token_count($stats['remaining']);
            $stats['formatted_total'] = ai_format_token_count($stats['total_purchased']);
            $stats['formatted_used'] = ai_format_token_count($stats['total_used']);
            $stats['formatted_daily'] = ai_format_token_count($stats['daily_usage']);
            $stats['formatted_monthly'] = ai_format_token_count($stats['monthly_usage']);
            
            // Widget compatibilty için gerekli key'ler
            $stats['remaining_tokens'] = $stats['remaining'];
            $stats['total_tokens'] = $stats['total_purchased'];
            
            // Provider bilgileri
            $stats['provider'] = 'deepseek';
            $stats['provider_active'] = true;
            
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
            return number_format($tokenCount / 1000000, 1) . 'M';
        } elseif ($tokenCount >= 1000) {
            return number_format($tokenCount / 1000, 1) . 'K';
        } else {
            return number_format($tokenCount);
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
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
     * Aktif AI token paketlerini döndür
     */
    function ai_token_packages(): \Illuminate\Database\Eloquent\Collection
    {
        return AITokenPackage::active()->ordered()->get();
    }
}

if (!function_exists('format_token_amount')) {
    /**
     * Token miktarını formatlı şekilde döndür
     */
    function format_token_amount(int $amount): string
    {
        return number_format($amount) . ' Token';
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        $remaining = ai_get_token_balance($tenantId);
        
        return $remaining >= $tokensNeeded;
    }
}

if (!function_exists('ai_use_tokens')) {
    /**
     * Token kullanımını kaydet
     * 
     * @param int $tokensUsed
     * @param string $module
     * @param string $action
     * @param string|null $tenantId
     * @param array $metadata
     * @return bool
     */
    function ai_use_tokens(int $tokensUsed, string $module, string $action, ?string $tenantId = null, array $metadata = []): bool
    {
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        // Token kontrolü
        if (!ai_can_use_tokens($tokensUsed, $tenantId)) {
            return false;
        }
        
        // Kullanım kaydı oluştur
        $usageId = DB::table('ai_usage')->insertGetId([
            'tenant_id' => $tenantId,
            'module' => $module,
            'action' => $action,
            'tokens_used' => $tokensUsed,
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($usageId) {
            // Cache'i temizle
            ai_clear_token_cache($tenantId);
            return true;
        }
        
        return false;
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $usage = DB::table('ai_usage')
            ->where('tenant_id', $tenantId)
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $purchases = DB::table('ai_purchases')
            ->leftJoin('ai_token_packages', 'ai_purchases.package_id', '=', 'ai_token_packages.id')
            ->where('ai_purchases.tenant_id', $tenantId)
            ->orderBy('ai_purchases.created_at', 'desc')
            ->limit($limit)
            ->select([
                'ai_purchases.*',
                'ai_token_packages.name as package_name',
                'ai_token_packages.description as package_description'
            ])
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
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
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