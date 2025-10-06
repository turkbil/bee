<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class AIProvider extends Model
{
    use HasFactory;
    protected $connection = 'central';

    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'display_name',
        'service_class',
        'default_model',
        'available_models',
        'default_settings',
        'api_key',
        'base_url',
        'is_active',
        'is_default',
        'priority',
        'average_response_time',
        'description',
        'token_cost_multiplier',
        'tokens_per_request_estimate',
        'cost_structure',
        'tracks_usage',
    ];

    protected $casts = [
        'available_models' => 'array',
        'default_settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'average_response_time' => 'decimal:2',
        'token_cost_multiplier' => 'decimal:4',
        'tokens_per_request_estimate' => 'integer',
        'cost_structure' => 'array',
        'tracks_usage' => 'boolean',
    ];

    /**
     * API key accessor - Şifre çöz
     */
    public function getApiKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            \Log::warning('API key decryption failed for provider: ' . $this->name);
            return $value; // Eski format için fallback
        }
    }

    /**
     * API key mutator - Şifre ile sakla
     */
    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Varsayılan provider'ı getir
     */
    public static function getDefault()
    {
        return self::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Aktif provider'ları getir
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('average_response_time', 'asc')
            ->get();
    }

    /**
     * Provider'ın servis sınıfını örnekle - GÜNCEL GLOBAL STANDART
     */
    public function getServiceInstance()
    {
        $serviceClass = "Modules\\AI\\App\\Services\\{$this->service_class}";
        
        if (!class_exists($serviceClass)) {
            throw new \Exception("Service class not found: {$serviceClass}");
        }

        // Constructor'a provider bilgilerini gönder - GLOBAL STANDART
        $service = new $serviceClass([
            'provider_id' => $this->id,
            'api_key' => $this->api_key, // Otomatik decrypt edilecek
            'base_url' => $this->base_url,
            'model' => $this->default_model,
            'settings' => $this->default_settings
        ]);

        return $service;
    }

    /**
     * Performans güncellemesi
     */
    public function updatePerformance($responseTime)
    {
        // Ortalama yanıt süresini güncelle (weighted average)
        if ($this->average_response_time) {
            $this->average_response_time = ($this->average_response_time * 0.8) + ($responseTime * 0.2);
        } else {
            $this->average_response_time = $responseTime;
        }

        $this->save();
    }

    /**
     * Provider'ın kullanılabilir olup olmadığını kontrol et
     *
     * NOT: API key olmadan da çalışabilir (production'da key eklenebilir)
     * Servis sınıfı yüklenebiliyorsa provider kullanılabilir sayılır
     */
    public function isAvailable()
    {
        // API key kontrolünü kaldırdık - servis sınıfı varsa yeterli
        // API key runtime'da kontrol edilir, boot aşamasında değil
        return $this->is_active && $this->service_class;
    }

    /**
     * Bu provider'ı kullanan tenant'lar
     */
    public function tenants()
    {
        return $this->hasMany(\App\Models\Tenant::class, 'tenant_ai_provider_id');
    }

    /**
     * Bu provider'ın token kullanımları
     */
    public function tokenUsages()
    {
        return $this->hasMany(\Modules\AI\App\Models\AITokenUsage::class, 'ai_provider_id');
    }

    /**
     * Token cost hesapla
     */
    public function calculateTokenCost(int $tokens): float
    {
        return $tokens * $this->token_cost_multiplier;
    }

    /**
     * Provider models relationship - YENİ SİSTEM
     */
    public function providerModels()
    {
        return $this->hasMany(AIProviderModel::class, 'provider_id');
    }

    /**
     * Model credit rates relationship - BACKWARD COMPATIBILITY
     */
    public function modelCreditRates()
    {
        return $this->hasMany(AIProviderModel::class, 'provider_id');
    }

    /**
     * Specific model için rate getir - YENİ SİSTEM
     */
    public function getModelRate(string $modelName): ?AIProviderModel
    {
        return $this->providerModels()
            ->where('model_name', $modelName)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Model için kredi hesapla - YENI SİSTEM
     */
    public function calculateModelCredits(string $modelName, int $inputTokens, int $outputTokens = 0): float
    {
        $rate = $this->getModelRate($modelName);
        
        if (!$rate) {
            // Default rate kullan
            return ($inputTokens + $outputTokens) / 1000 * 1.0;
        }
        
        return $rate->calculateCreditCost($inputTokens, $outputTokens);
    }

    /**
     * Mevcut modeller ve rate'leri - YENİ SİSTEM
     */
    public function getAvailableModelsWithRates()
    {
        return $this->providerModels()
            ->where('is_active', true)
            ->orderBy('credit_per_1k_input_tokens', 'asc')
            ->orderBy('is_default', 'desc')
            ->orderBy('model_name')
            ->get()
            ->map(function($rate) {
                return [
                    'id' => $rate->id,
                    'model_name' => $rate->model_name,
                    'input_rate' => $rate->credit_per_1k_input_tokens,
                    'output_rate' => $rate->credit_per_1k_output_tokens,
                    'is_default' => $rate->is_default,
                    'sort_order' => $rate->sort_order,
                    'cost_info' => $rate->cost_info
                ];
            });
    }

    /**
     * Günlük kullanım istatistikleri
     */
    public function getDailyUsageStats()
    {
        return $this->tokenUsages()
            ->whereDate('used_at', today())
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(tokens_used) as total_tokens,
                AVG(tokens_used) as avg_tokens_per_request,
                SUM(tokens_used * cost_multiplier) as total_cost
            ')
            ->first();
    }

    /**
     * Provider için select option formatı
     */
    public static function getSelectOptions()
    {
        return self::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->map(function ($provider) {
                return [
                    'value' => $provider->id,
                    'label' => $provider->display_name . ' (Cost: x' . $provider->token_cost_multiplier . ')',
                    'priority' => $provider->priority,
                    'cost_multiplier' => $provider->token_cost_multiplier,
                    'has_api_key' => !empty($provider->api_key),
                ];
            })
            ->toArray();
    }

    /**
     * Global AI provider instance getter - MERKEZİ STANDART
     */
    public static function getGlobalProvider()
    {
        // Varsayılan provider'ı al
        $provider = self::where('is_default', true)
            ->where('is_active', true)
            ->first();
            
        // Eğer yoksa en yüksek öncelikli
        if (!$provider) {
            $provider = self::where('is_active', true)
                ->orderBy('priority', 'desc')
                ->first();
        }
        
        if (!$provider) {
            throw new \Exception('No active AI provider found');
        }
        
        return $provider;
    }

    /**
     * AIProvider modelleri relationship - YENİ SİSTEM
     */
    public function models()
    {
        return $this->hasMany(AIProviderModel::class, 'provider_id');
    }
}