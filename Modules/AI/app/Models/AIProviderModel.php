<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * AI Provider Model Management
 * 
 * Provider'lara bağlı model yönetimi ve sıralama sistemi
 */
class AIProviderModel extends Model
{
    use HasFactory;
    protected $connection = 'central';

    protected $table = 'ai_provider_models';

    protected $fillable = [
        'provider_id',
        'model_name',
        'credit_per_1k_input_tokens',
        'credit_per_1k_output_tokens',
        'base_cost_usd',
        'markup_percentage',
        'operation_rates', // YENİ - Hybrid sistem
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'credit_per_1k_input_tokens' => 'decimal:4',
        'credit_per_1k_output_tokens' => 'decimal:4',
        'base_cost_usd' => 'decimal:6',
        'markup_percentage' => 'decimal:2',
        'operation_rates' => 'array', // YENİ - JSON cast
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
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
    public static function getForModel(string $providerName, string $modelName): ?self
    {
        $cacheKey = "provider_model:{$providerName}:{$modelName}";
        
        return Cache::remember($cacheKey, 300, function() use ($providerName, $modelName) {
            return self::whereHas('provider', function($query) use ($providerName) {
                $query->where('name', $providerName)
                      ->where('is_active', true);
            })
            ->where('model_name', $modelName)
            ->where('is_active', true)
            ->first();
        });
    }

    /**
     * Provider için tüm aktif modeller - sıralı
     */
    public static function getProviderModels(int $providerId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('provider_id', $providerId)
            ->where('is_active', true)
            ->orderBy('credit_per_1k_input_tokens', 'asc')
            ->orderBy('is_default', 'desc')
            ->orderBy('model_name')
            ->get();
    }

    /**
     * Varsayılan modeli getir (önce tenant, sonra provider, sonra global)
     */
    public static function getDefaultModel(?int $providerId = null): ?self
    {
        $cacheKey = "default_model:" . ($providerId ?: 'global');
        
        return Cache::remember($cacheKey, 300, function() use ($providerId) {
            // 1. Önce belirtilen provider'da varsayılan model ara
            if ($providerId) {
                $model = self::where('provider_id', $providerId)
                    ->where('is_default', true)
                    ->where('is_active', true)
                    ->orderBy('credit_per_1k_input_tokens', 'asc')
                    ->first();
                    
                if ($model) {
                    return $model;
                }
            }
            
            // 2. Global varsayılan model ara
            $model = self::whereHas('provider', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_default', true)
            ->where('is_active', true)
            ->orderBy('credit_per_1k_input_tokens', 'asc')
            ->orderBy('is_default', 'desc')
            ->first();
            
            if ($model) {
                return $model;
            }
            
            // 3. En yüksek sıralamadaki model
            return self::whereHas('provider', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('credit_per_1k_input_tokens', 'asc')
            ->orderBy('is_default', 'desc')
            ->first();
        });
    }

    /**
     * Tenant için model seçim hierarchy
     */
    public static function getModelForTenant(?string $tenantProvider = null, ?string $tenantModel = null): ?self
    {
        // 1. Tenant'ın belirttiği provider ve model
        if ($tenantProvider && $tenantModel) {
            $model = self::whereHas('provider', function($query) use ($tenantProvider) {
                $query->where('name', $tenantProvider)->where('is_active', true);
            })
            ->where('model_name', $tenantModel)
            ->where('is_active', true)
            ->first();
            
            if ($model) {
                return $model;
            }
        }
        
        // 2. Tenant'ın belirttiği provider'ın varsayılan modeli
        if ($tenantProvider) {
            $provider = AIProvider::where('name', $tenantProvider)
                ->where('is_active', true)
                ->first();
                
            if ($provider) {
                $model = self::where('provider_id', $provider->id)
                    ->where('is_default', true)
                    ->where('is_active', true)
                    ->orderBy('credit_per_1k_input_tokens', 'asc')
            ->orderBy('is_default', 'desc')
                    ->first();
                    
                if ($model) {
                    return $model;
                }
            }
        }
        
        // 3. Global varsayılan provider'ın 1 nolu modeli
        $defaultProvider = AIProvider::where('is_default', true)
            ->where('is_active', true)
            ->first();
            
        if ($defaultProvider) {
            $model = self::where('provider_id', $defaultProvider->id)
                ->where('is_active', true)
                ->orderBy('credit_per_1k_input_tokens', 'asc')
                ->orderBy('is_default', 'desc')
                ->first();
                
            if ($model) {
                return $model;
            }
        }
        
        // 4. Genel varsayılan
        return self::getDefaultModel();
    }

    /**
     * Sıralama güncellemesi
     */
    public static function updateSortOrder(array $sortData): bool
    {
        try {
            foreach ($sortData as $item) {
                self::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }
            
            // Cache temizle
            Cache::forget('provider_models_*');
            Cache::forget('default_model:*');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('AI Provider Model sort order update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aktif modeller scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Varsayılan modeller scope
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Sıralı modeller scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('credit_per_1k_input_tokens', 'asc')
                    ->orderBy('is_default', 'desc')
                    ->orderBy('model_name');
    }

    /**
     * Provider scope
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Backward compatibility için eski method isimleri
     */
    public static function getRateForModel(string $providerName, string $modelName): ?self
    {
        return self::getForModel($providerName, $modelName);
    }

    public static function getProviderRates(int $providerId): \Illuminate\Database\Eloquent\Collection
    {
        return self::getProviderModels($providerId);
    }

    /**
     * Display name for admin panels
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->provider->display_name . ' - ' . $this->model_name;
    }

    /**
     * Cost info for display
     */
    public function getCostInfoAttribute(): string
    {
        return "Input: {$this->credit_per_1k_input_tokens}/1k, Output: {$this->credit_per_1k_output_tokens}/1k";
    }
}