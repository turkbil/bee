<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Limit extends Model
{
    use HasFactory;

    protected $table = 'ai_limits';

    protected $fillable = [
        'tenant_id',
        'daily_limit',
        'monthly_limit',
        'used_today',
        'used_month',
        'reset_at',
    ];

    protected $casts = [
        'daily_limit' => 'integer',
        'monthly_limit' => 'integer',
        'used_today' => 'integer',
        'used_month' => 'integer',
        'reset_at' => 'datetime',
    ];

    /**
     * Günlük kullanım limitini kontrol et
     *
     * @return bool
     */
    public function checkDailyLimit(): bool
    {
        // Eğer reset_at bugünden önceyse, sayacı sıfırla
        if ($this->reset_at && $this->reset_at->startOfDay()->lt(now()->startOfDay())) {
            $this->used_today = 0;
            $this->reset_at = now();
            $this->save();
        }

        return $this->used_today < $this->daily_limit;
    }

    /**
     * Aylık kullanım limitini kontrol et
     *
     * @return bool
     */
    public function checkMonthlyLimit(): bool
    {
        // Eğer bu ayın başlangıcındaysa, aylık sayacı sıfırla
        if (now()->startOfMonth()->eq(now()->startOfDay())) {
            $this->used_month = 0;
            $this->save();
        }

        return $this->used_month < $this->monthly_limit;
    }

    /**
     * Kullanım sayacını artır
     *
     * @param int $tokens
     * @return void
     */
    public function incrementUsage(int $tokens = 1): void
    {
        $this->used_today += $tokens;
        $this->used_month += $tokens;
        $this->save();
    }

    /**
     * Tenant ilişkisi
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}