<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\Limit;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;

class LimitService
{
    protected $tenantId;

    /**
     * Constructor
     *
     * @param int|null $tenantId
     */
    public function __construct(?int $tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * Kullanım limitlerini kontrol et
     *
     * @return bool
     */
    public function checkLimits(): bool
    {
        // Tenant ID yoksa, limitsiz olarak çalış
        if ($this->tenantId === null) {
            return true;
        }
        
        $limit = $this->getLimitRecord();
        
        if (!$limit) {
            return true; // Limit kaydı yoksa sınırlandırma yok
        }
        
        return $limit->checkDailyLimit() && $limit->checkMonthlyLimit();
    }

    /**
     * Kullanım sayacını artır
     *
     * @param int $tokens
     * @return void
     */
    public function incrementUsage(int $tokens = 1): void
    {
        // Tenant ID yoksa, sayacı artırma
        if ($this->tenantId === null) {
            return;
        }
        
        $limit = $this->getLimitRecord();
        
        if ($limit) {
            $limit->incrementUsage($tokens);
        }
    }

    /**
     * Kalan günlük limit miktarını getir
     *
     * @return int
     */
    public function getRemainingDailyLimit(): int
    {
        // Tenant ID yoksa, sınırsız limit döndür
        if ($this->tenantId === null) {
            return PHP_INT_MAX;
        }
        
        $limit = $this->getLimitRecord();
        
        if (!$limit) {
            return PHP_INT_MAX; // Limit kaydı yoksa sınırsız
        }
        
        return max(0, $limit->daily_limit - $limit->used_today);
    }

    /**
     * Kalan aylık limit miktarını getir
     *
     * @return int
     */
    public function getRemainingMonthlyLimit(): int
    {
        // Tenant ID yoksa, sınırsız limit döndür
        if ($this->tenantId === null) {
            return PHP_INT_MAX;
        }
        
        $limit = $this->getLimitRecord();
        
        if (!$limit) {
            return PHP_INT_MAX; // Limit kaydı yoksa sınırsız
        }
        
        return max(0, $limit->monthly_limit - $limit->used_month);
    }

    /**
     * Limit kaydını getir, yoksa oluştur
     *
     * @return Limit|null
     */
    protected function getLimitRecord(): ?Limit
    {
        if ($this->tenantId === null) {
            return null;
        }
        
        $cacheKey = "ai_limit_tenant_{$this->tenantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return TenantHelpers::central(function () {
                $limit = Limit::where('tenant_id', $this->tenantId)->first();
                
                if (!$limit) {
                    $limit = Limit::create([
                        'tenant_id' => $this->tenantId,
                        'daily_limit' => 100,
                        'monthly_limit' => 3000,
                        'used_today' => 0,
                        'used_month' => 0,
                        'reset_at' => now(),
                    ]);
                }
                
                return $limit;
            });
        });
    }
}