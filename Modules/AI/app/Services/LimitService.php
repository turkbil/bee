<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\Limit;
use Illuminate\Support\Facades\Cache;

class LimitService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Yapılandırma
    }

    /**
     * Kullanım limitlerini kontrol et
     *
     * @return bool
     */
    public function checkLimits(): bool
    {
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
        $cacheKey = "ai_limit";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $limit = Limit::first();
            
            if (!$limit) {
                $limit = Limit::create([
                    'daily_limit' => 100,
                    'monthly_limit' => 3000,
                    'used_today' => 0,
                    'used_month' => 0,
                    'reset_at' => now(),
                ]);
            }
            
            return $limit;
        });
    }
}