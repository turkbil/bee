<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, LogsActivity;
    
    protected $guarded = [];
    
    public $timestamps = true;
    protected $table = 'tenants';
    public $incrementing = true;
    
    protected $casts = [
        'is_active' => 'boolean',
        'central' => 'boolean',
        'data' => 'array',
        'theme_id' => 'integer',
        'ai_tokens_balance' => 'integer',
        'ai_tokens_used_this_month' => 'integer',
        'ai_monthly_token_limit' => 'integer',
        'ai_enabled' => 'boolean',
        'ai_monthly_reset_at' => 'datetime',
        'ai_last_used_at' => 'datetime',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'is_active', 'data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    public function domains()
    {
        return $this->hasMany(\Stancl\Tenancy\Database\Models\Domain::class, 'tenant_id', 'id');
    }

    public function getDatabaseName()
    {
        return $this->tenancy_db_name;
    }
    
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'title',
            'tenancy_db_name',
            'is_active',
            'central',
            'theme_id',
            'admin_default_locale',
            'tenant_default_locale',
            'data',
        ];
    }

    public function modules()
    {
        return $this->belongsToMany(
            \Modules\ModuleManagement\App\Models\Module::class,
            'module_tenants',
            'tenant_id',
            'module_id'
        )->withPivot('is_active')
        ->withTimestamps();
    }

    /**
     * Tenant languages relationship - tenant context'te çalışır
     */
    public function tenantLanguages()
    {
        // Central tenant ise ana veritabanından, değilse tenant'ın kendi veritabanından
        if ($this->central) {
            // Central tenant - ana veritabanından al
            return \Modules\LanguageManagement\app\Models\TenantLanguage::on('mysql')->query();
        } else {
            // Normal tenant - tenant veritabanından al
            return \Modules\LanguageManagement\app\Models\TenantLanguage::query();
        }
    }

    /**
     * Module settings relationship
     */
    public function moduleSettings()
    {
        // Basit fallback - data field'ından module ayarlarını döndür
        $data = $this->data ?? [];
        return (object) ($data['module_settings'] ?? []);
    }

    /**
     * AI Token purchases relationship
     */
    public function aiTokenPurchases()
    {
        return $this->hasMany(\Modules\AI\App\Models\AITokenPurchase::class);
    }

    /**
     * AI Token usage relationship
     */
    public function aiTokenUsage()
    {
        return $this->hasMany(\Modules\AI\App\Models\AITokenUsage::class);
    }

    /**
     * Check if tenant has enough AI tokens
     */
    public function hasEnoughTokens(int $tokensNeeded): bool
    {
        return $this->ai_enabled && $this->real_token_balance >= $tokensNeeded;
    }

    /**
     * Use AI tokens
     */
    public function useTokens(int $tokensUsed, string $usageType = 'chat', ?string $description = null, ?string $referenceId = null): bool
    {
        if (!$this->hasEnoughTokens($tokensUsed)) {
            return false;
        }

        // Update token balance
        $this->decrement('ai_tokens_balance', $tokensUsed);
        $this->increment('ai_tokens_used_this_month', $tokensUsed);
        $this->update(['ai_last_used_at' => now()]);

        // Log usage
        \Modules\AI\App\Models\AITokenUsage::create([
            'tenant_id' => $this->id,
            'tokens_used' => $tokensUsed,
            'usage_type' => $usageType,
            'description' => $description,
            'reference_id' => $referenceId,
            'used_at' => now()
        ]);

        return true;
    }

    /**
     * Add AI tokens to balance
     */
    public function addTokens(int $tokensToAdd, ?string $reason = null): bool
    {
        $this->increment('ai_tokens_balance', $tokensToAdd);
        
        return true;
    }

    /**
     * Reset monthly token usage
     */
    public function resetMonthlyTokenUsage(): bool
    {
        return $this->update([
            'ai_tokens_used_this_month' => 0,
            'ai_monthly_reset_at' => now()
        ]);
    }

    /**
     * Get real token balance based on purchases minus usage
     */
    public function getRealTokenBalanceAttribute(): int
    {
        $totalPurchased = $this->aiTokenPurchases()
            ->where('status', 'completed')
            ->sum('token_amount') ?? 0;

        $totalUsed = $this->aiTokenUsage()
            ->sum('tokens_used') ?? 0;

        return max(0, $totalPurchased - $totalUsed);
    }

    /**
     * Calculate actual token balance from purchases and usage
     */
    public function calculateRealTokenBalance(): int
    {
        return $this->real_token_balance;
    }

    /**
     * Get remaining tokens for this month
     */
    public function getRemainingMonthlyTokensAttribute(): int
    {
        if ($this->ai_monthly_token_limit <= 0) {
            return $this->real_token_balance;
        }

        $usedThisMonth = $this->ai_tokens_used_this_month ?? 0;
        $remaining = $this->ai_monthly_token_limit - $usedThisMonth;
        
        return max(0, min($remaining, $this->real_token_balance));
    }

    /**
     * Check if monthly limit is exceeded
     */
    public function isMonthlyLimitExceeded(): bool
    {
        if ($this->ai_monthly_token_limit <= 0) {
            return false; // No limit set
        }

        return $this->ai_tokens_used_this_month >= $this->ai_monthly_token_limit;
    }
}