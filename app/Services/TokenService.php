<?php

namespace App\Services;

use App\Models\Tenant;
use Modules\AI\App\Models\AITokenUsage;
use Modules\AI\App\Models\AITokenPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TokenService
{
    private static $instance = null;
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Kampanya çarpanını al
     */
    public function getCampaignMultiplier(): float
    {
        try {
            $setting = \Modules\AI\App\Models\Setting::where('key', 'token_campaign_multiplier')->first();
            return $setting ? (float)$setting->value : 1.0;
        } catch (\Exception $e) {
            \Log::warning('TokenService: Campaign multiplier setting not found', ['error' => $e->getMessage()]);
            return 1.0;
        }
    }
    
    /**
     * Token gösterimini formatla (temiz sayı)
     */
    public function formatTokenAmount(int $amount): string
    {
        if ($amount >= 1000000) {
            $value = $amount / 1000000;
            return ($value == intval($value)) ? intval($value) . 'M' : number_format($value, 1) . 'M';
        } else {
            // Tüm değerler K formatında gösterilecek
            $value = $amount / 1000;
            
            // 100'den küçük değerler için minimum 0.1K göster
            if ($amount < 100 && $amount > 0) {
                return '0.1K';
            }
            
            if ($value >= 1 && $value == intval($value)) {
                return intval($value) . 'K';
            } else {
                return number_format($value, 1) . 'K';
            }
        }
    }
    
    /**
     * Ham token miktarını al (formatlanmamış)
     */
    public function getRawTokenAmount(int $amount): int
    {
        $multiplier = $this->getCampaignMultiplier();
        return (int)($amount * $multiplier);
    }
    
    /**
     * Tenant'ın toplam token bakiyesini al
     */
    public function getTenantTokenBalance(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            // Fallback: get current tenant from context or first tenant
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                \Log::error('TokenService: Could not get tenant', ['error' => $e->getMessage()]);
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        return (int)$tenant->ai_tokens_balance;
    }
    
    /**
     * Tenant'ın bu ay kullandığı token miktarını al
     */
    public function getTenantMonthlyUsage(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        // Gerçek aylık kullanımı hesapla (öncelikli)
        try {
            $firstOfMonth = now()->startOfMonth();
            $realMonthly = \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenant->id)
                ->where('used_at', '>=', $firstOfMonth)
                ->sum('tokens_used') ?? 0;
            
            return $realMonthly;
        } catch (\Exception $e) {
            // Hata durumunda tenant tablosundaki değeri kullan
            $tenantMonthly = (int)$tenant->ai_tokens_used_this_month;
            return $tenantMonthly;
        }
    }
    
    /**
     * Tenant'ın aylık token limitini al
     */
    public function getTenantMonthlyLimit(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        return (int)$tenant->ai_monthly_token_limit;
    }
    
    /**
     * Bugün kullanılan token miktarını al
     */
    public function getTodayUsage(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        $cacheKey = "token_today_usage_{$tenant->id}";
        
        return Cache::remember($cacheKey, 300, function() use ($tenant) {
            try {
                return \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenant->id)
                    ->whereDate('used_at', Carbon::today())
                    ->sum('tokens_used') ?? 0;
            } catch (\Exception $e) {
                \Log::error('TokenService: Could not get today usage', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }
    
    /**
     * Günlük ortalama kullanımı al
     */
    public function getDailyAverage(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        $cacheKey = "token_daily_average_{$tenant->id}";
        
        return Cache::remember($cacheKey, 3600, function() use ($tenant) {
            try {
                $thirtyDaysAgo = Carbon::now()->subDays(30);
                
                $totalUsage = \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenant->id)
                    ->where('used_at', '>=', $thirtyDaysAgo)
                    ->sum('tokens_used') ?? 0;
                
                return (int)($totalUsage / 30);
            } catch (\Exception $e) {
                \Log::error('TokenService: Could not get daily average', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }
    
    /**
     * Toplam satın alınan token miktarını al
     */
    public function getTotalPurchasedTokens(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        $cacheKey = "token_total_purchased_{$tenant->id}";
        
        return Cache::remember($cacheKey, 1800, function() use ($tenant) {
            try {
                $purchasedFromRecords = \Modules\AI\App\Models\AITokenPurchase::where('tenant_id', $tenant->id)
                    ->where('status', 'completed')
                    ->sum('token_amount') ?? 0;
                
                // Eğer satın alım kaydı yoksa ama tenant'da balance varsa,
                // 500K başlangıç değeri olarak kabul et (sistem geneli)
                if ($purchasedFromRecords <= 0) {
                    // Sistem geneli başlangıç token'ı (500K olarak kabul ediyoruz)
                    return 500000;
                }
                
                return $purchasedFromRecords;
            } catch (\Exception $e) {
                \Log::error('TokenService: Could not get purchased tokens', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }
    
    /**
     * Toplam kullanılan token miktarını al
     */
    public function getTotalUsedTokens(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        $cacheKey = "token_total_used_{$tenant->id}";
        
        return Cache::remember($cacheKey, 1800, function() use ($tenant) {
            try {
                return \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenant->id)
                    ->sum('tokens_used') ?? 0;
            } catch (\Exception $e) {
                \Log::error('TokenService: Could not get used tokens', ['error' => $e->getMessage()]);
                return 0;
            }
        });
    }
    
    /**
     * Kalan token miktarını hesapla (gerçek bakiye)
     */
    public function getRemainingTokens(?Tenant $tenant = null): int
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            try {
                $tenant = Tenant::first();
            } catch (\Exception $e) {
                return 0;
            }
        }
        
        if (!$tenant) {
            return 0;
        }
        
        // Basit hesaplama: Satın alınan - kullanılan
        try {
            $purchased = $this->getTotalPurchasedTokens($tenant);
            $used = $this->getTotalUsedTokens($tenant);
            $remaining = $purchased - $used;
            
            return max(0, $remaining);
        } catch (\Exception $e) {
            // Fallback: Tenant'daki ai_tokens_balance kullan
            return (int)$tenant->ai_tokens_balance;
        }
    }
    
    /**
     * Token kullanım yüzdesini al
     */
    public function getUsagePercentage(?Tenant $tenant = null): float
    {
        $purchased = $this->getTotalPurchasedTokens($tenant);
        $used = $this->getTotalUsedTokens($tenant);
        
        if ($purchased <= 0) {
            return 0.0;
        }
        
        return min(100.0, ($used / $purchased) * 100);
    }
    
    /**
     * Bugünkü kullanım yüzdesini al (günlük ortalamaya göre)
     */
    public function getTodayUsagePercentage(?Tenant $tenant = null): float
    {
        $todayUsage = $this->getTodayUsage($tenant);
        $dailyAverage = $this->getDailyAverage($tenant);
        
        if ($dailyAverage <= 0) {
            return 0.0;
        }
        
        return min(100.0, ($todayUsage / $dailyAverage) * 100);
    }
    
    /**
     * Günlük ortalama kullanım yüzdesini al (aylık limite göre)
     */
    public function getDailyAveragePercentage(?Tenant $tenant = null): float
    {
        $dailyAverage = $this->getDailyAverage($tenant);
        $monthlyLimit = $this->getTenantMonthlyLimit($tenant);
        
        if ($monthlyLimit <= 0) {
            return 0.0;
        }
        
        $dailyLimit = $monthlyLimit / 30;
        return min(100.0, ($dailyAverage / $dailyLimit) * 100);
    }
    
    /**
     * Cache'leri temizle
     */
    public function clearCaches(?Tenant $tenant = null): void
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            return;
        }
        
        $cacheKeys = [
            "token_today_usage_{$tenant->id}",
            "token_daily_average_{$tenant->id}",
            "token_total_purchased_{$tenant->id}",
            "token_total_used_{$tenant->id}"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
    
    /**
     * Kampanya durumunu kontrol et
     */
    public function isCampaignActive(): bool
    {
        $multiplier = $this->getCampaignMultiplier();
        return $multiplier !== 1.0;
    }
    
    /**
     * Kampanya bilgisini al
     */
    public function getCampaignInfo(): array
    {
        $multiplier = $this->getCampaignMultiplier();
        $isActive = $this->isCampaignActive();
        
        return [
            'active' => $isActive,
            'multiplier' => $multiplier,
            'discount_percentage' => $isActive ? (int)((1 - (1 / $multiplier)) * 100) : 0,
            'bonus_percentage' => $isActive && $multiplier > 1 ? (int)(($multiplier - 1) * 100) : 0
        ];
    }
}