<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * AI Model Credit Rate Model
 * 
 * Model bazlı kredi oranları ve maliyet hesaplama sistemi
 */
class AIModelCreditRate extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_model_credit_rates';

    protected $fillable = [
        'provider_id',
        'model_name',
        'credit_per_1k_input_tokens',
        'credit_per_1k_output_tokens',
        'base_cost_usd',
        'markup_percentage',
        'is_active',
    ];

    protected $casts = [
        'credit_per_1k_input_tokens' => 'decimal:4',
        'credit_per_1k_output_tokens' => 'decimal:4',
        'base_cost_usd' => 'decimal:6',
        'markup_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Provider relationship
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(AIProvider::class, 'provider_id');
    }

    /**
     * Model için kredi hesaplama
     */
    public function calculateCreditCost(int $inputTokens, int $outputTokens = 0): float
    {
        $inputCost = ($inputTokens / 1000) * $this->credit_per_1k_input_tokens;
        $outputCost = ($outputTokens / 1000) * $this->credit_per_1k_output_tokens;
        
        return round($inputCost + $outputCost, 4);
    }

    /**
     * Input token kredisi hesapla
     */
    public function calculateInputCost(int $inputTokens): float
    {
        return round(($inputTokens / 1000) * $this->credit_per_1k_input_tokens, 4);
    }

    /**
     * Output token kredisi hesapla
     */
    public function calculateOutputCost(int $outputTokens): float
    {
        return round(($outputTokens / 1000) * $this->credit_per_1k_output_tokens, 4);
    }

    /**
     * Specific model için rate getir
     */
    public static function getRateForModel(string $providerName, string $modelName): ?self
    {
        $cacheKey = "model_rate:{$providerName}:{$modelName}";
        
        return Cache::remember($cacheKey, 300, function() use ($providerName, $modelName) {
            return self::whereHas('provider', function($query) use ($providerName) {
                $query->where('name', $providerName);
            })
            ->where('model_name', $modelName)
            ->where('is_active', true)
            ->first();
        });
    }

    /**
     * Etkili input rate (markup dahil)
     */
    public function getEffectiveInputRate(): float
    {
        return $this->credit_per_1k_input_tokens * (1 + ($this->markup_percentage / 100));
    }

    /**
     * Etkili output rate (markup dahil)
     */
    public function getEffectiveOutputRate(): float
    {
        return $this->credit_per_1k_output_tokens * (1 + ($this->markup_percentage / 100));
    }

    /**
     * Provider için tüm aktif model rate'leri
     */
    public static function getProviderRates(int $providerId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('provider_id', $providerId)
            ->where('is_active', true)
            ->orderBy('model_name')
            ->get();
    }

    /**
     * En düşük maliyetli modeli bul
     */
    public static function getCheapestModel(int $providerId): ?self
    {
        return self::where('provider_id', $providerId)
            ->where('is_active', true)
            ->orderBy('credit_per_1k_input_tokens')
            ->first();
    }

    /**
     * En hızlı/premium modeli bul (en pahalı genelde en hızlı)
     */
    public static function getPremiumModel(int $providerId): ?self
    {
        return self::where('provider_id', $providerId)
            ->where('is_active', true)
            ->orderByDesc('credit_per_1k_input_tokens')
            ->first();
    }

    /**
     * Model comparison için cost estimate
     */
    public function estimateCost(int $estimatedInputTokens, int $estimatedOutputTokens = 0): array
    {
        $inputCost = $this->calculateCreditCost($estimatedInputTokens, 0);
        $outputCost = $this->calculateCreditCost(0, $estimatedOutputTokens);
        $totalCost = $inputCost + $outputCost;
        
        return [
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'total_cost' => $totalCost,
            'cost_per_1k_tokens' => ($totalCost / (($estimatedInputTokens + $estimatedOutputTokens) / 1000)),
        ];
    }

    /**
     * Bulk rate update
     */
    public static function updateBulkRates(array $updates): int
    {
        $updated = 0;
        
        foreach ($updates as $update) {
            $rate = self::find($update['id']);
            if ($rate) {
                $rate->update($update);
                $updated++;
            }
        }
        
        // Cache temizle
        Cache::forget('model_rates_*');
        
        return $updated;
    }

    /**
     * Global scope - sadece aktif olanları
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Provider name ile scope
     */
    public function scopeForProvider($query, string $providerName)
    {
        return $query->whereHas('provider', function($q) use ($providerName) {
            $q->where('name', $providerName);
        });
    }

    /**
     * Backward compatibility accessor for input_cost_per_1k
     */
    public function getInputCostPer1kAttribute(): float
    {
        return (float) $this->credit_per_1k_input_tokens;
    }

    /**
     * Backward compatibility accessor for output_cost_per_1k
     */
    public function getOutputCostPer1kAttribute(): float
    {
        return (float) $this->credit_per_1k_output_tokens;
    }

    /**
     * Backward compatibility accessor for base_cost
     */
    public function getBaseCostAttribute(): float
    {
        return (float) $this->base_cost_usd;
    }
}