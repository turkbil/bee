<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\{AIProvider, AIModelCreditRate};
use Illuminate\Support\Facades\{Cache, Log};

/**
 * Global Credit Calculator Service - YENI SİSTEM
 * 
 * Model bazlı kredi hesaplama ve maliyet yönetimi
 */
readonly class CreditCalculatorService
{
    private const DEFAULT_RATE = 1.0;
    private const CACHE_TTL = 300; // 5 dakika

    /**
     * Model için kredi hesapla
     */
    public function calculateCreditsForModel(
        string $providerName,
        string $modelName,
        int $inputTokens,
        int $outputTokens = 0
    ): float {
        try {
            $rate = $this->getModelRate($providerName, $modelName);

            if (!$rate) {
                Log::warning("Model rate not found, using default", [
                    'provider' => $providerName,
                    'model' => $modelName
                ]);
                return $this->calculateWithDefaultRate($inputTokens, $outputTokens);
            }

            $credits = $rate->calculateCreditCost($inputTokens, $outputTokens);

            Log::info("Credits calculated", [
                'provider' => $providerName,
                'model' => $modelName,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'credits' => $credits
            ]);

            return $credits;

        } catch (\Exception $e) {
            Log::error("Credit calculation failed", [
                'error' => $e->getMessage(),
                'provider' => $providerName,
                'model' => $modelName
            ]);

            return $this->calculateWithDefaultRate($inputTokens, $outputTokens);
        }
    }

    /**
     * Hybrid kredi hesaplama - Operation/Feature based
     *
     * @param string $providerName Provider adı (openai, anthropic, deepseek)
     * @param string $modelName Model adı (gpt-4o, claude-3-5-sonnet, etc.)
     * @param int $inputTokens Input token sayısı
     * @param int $outputTokens Output token sayısı
     * @param string|null $operationType İşlem türü (chat, seo_recommendations, translation, etc.)
     * @param string|null $featureSlug Feature slug (ai_features tablosundan)
     * @param array $context Ek bağlam bilgileri (word_count, page_count, etc.)
     * @return float Hesaplanan kredi miktarı
     */
    public function calculateCreditsWithContext(
        string $providerName,
        string $modelName,
        int $inputTokens,
        int $outputTokens,
        ?string $operationType = null,
        ?string $featureSlug = null,
        array $context = []
    ): float {
        try {
            // 1. ÖNCE FEATURE-BASED (en spesifik - ai_features.token_cost)
            if ($featureSlug) {
                $feature = \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)->first();

                if ($feature && $feature->token_cost) {
                    Log::debug('Using feature-based pricing', [
                        'feature_slug' => $featureSlug,
                        'token_cost' => $feature->token_cost
                    ]);

                    return $this->calculateByConfig(
                        $feature->token_cost,
                        $providerName,
                        $modelName,
                        $inputTokens,
                        $outputTokens,
                        $context
                    );
                }
            }

            // 2. SONRA OPERATION-BASED (genel - ai_provider_models.operation_rates)
            if ($operationType) {
                $providerModel = \Modules\AI\App\Models\AIProviderModel::getForModel($providerName, $modelName);

                if ($providerModel && $providerModel->operation_rates) {
                    $config = $providerModel->operation_rates[$operationType] ?? null;

                    if ($config) {
                        Log::debug('Using operation-based pricing', [
                            'operation_type' => $operationType,
                            'config' => $config
                        ]);

                        return $this->calculateByConfig(
                            $config,
                            $providerName,
                            $modelName,
                            $inputTokens,
                            $outputTokens,
                            $context
                        );
                    }
                }
            }

            // 3. EN SON TOKEN-BASED (fallback - mevcut sistem)
            Log::debug('Using token-based pricing (fallback)', [
                'provider' => $providerName,
                'model' => $modelName
            ]);

            return $this->calculateCreditsForModel(
                $providerName,
                $modelName,
                $inputTokens,
                $outputTokens
            );

        } catch (\Exception $e) {
            Log::error('calculateCreditsWithContext error', [
                'feature_slug' => $featureSlug,
                'operation_type' => $operationType,
                'error' => $e->getMessage()
            ]);

            // Ultimate fallback
            return $this->calculateWithDefaultRate($inputTokens, $outputTokens);
        }
    }

    /**
     * Config bazlı kredi hesaplama (unified method)
     *
     * Desteklenen tipler:
     * - fixed: Sabit kredi
     * - tier: Token bazlı katmanlı fiyatlandırma
     * - word_tier: Kelime sayısı bazlı katmanlı
     * - page_tier: Sayfa sayısı bazlı katmanlı
     * - token_multiplier: Token bazlı çarpan (indirimli)
     * - token_full: Normal token hesaplama
     */
    private function calculateByConfig(
        array $config,
        string $providerName,
        string $modelName,
        int $inputTokens,
        int $outputTokens,
        array $context = []
    ): float {
        $type = $config['type'] ?? 'token_full';

        switch ($type) {
            case 'fixed':
                // Sabit kredi
                return (float) ($config['amount'] ?? 1.0);

            case 'tier':
                // Token bazlı tier
                $totalTokens = $inputTokens + $outputTokens;
                $tiers = $config['tiers'] ?? [];

                foreach ($tiers as $tier) {
                    if ($totalTokens <= ($tier['max_tokens'] ?? PHP_INT_MAX)) {
                        return (float) ($tier['credits'] ?? 1.0);
                    }
                }

                // Son tier'ı döndür
                $lastTier = end($tiers);
                return (float) ($lastTier['credits'] ?? 1.0);

            case 'word_tier':
                // Kelime sayısı bazlı tier
                $wordCount = $context['word_count'] ?? (int) (($inputTokens + $outputTokens) / 4);
                $tiers = $config['tiers'] ?? [];

                foreach ($tiers as $tier) {
                    if ($wordCount <= ($tier['max_words'] ?? PHP_INT_MAX)) {
                        return (float) ($tier['credits'] ?? 1.0);
                    }
                }

                $lastTier = end($tiers);
                return (float) ($lastTier['credits'] ?? 1.0);

            case 'page_tier':
                // Sayfa sayısı bazlı tier
                $pageCount = $context['page_count'] ?? 1;
                $tiers = $config['tiers'] ?? [];

                foreach ($tiers as $tier) {
                    if ($pageCount <= ($tier['max_pages'] ?? PHP_INT_MAX)) {
                        return (float) ($tier['credits'] ?? 1.0);
                    }
                }

                $lastTier = end($tiers);
                return (float) ($lastTier['credits'] ?? 1.0);

            case 'token_multiplier':
                // Token bazlı ama indirimli
                $baseCredits = $this->calculateCreditsForModel(
                    $providerName,
                    $modelName,
                    $inputTokens,
                    $outputTokens
                );

                $multiplier = (float) ($config['multiplier'] ?? 1.0);
                $credits = $baseCredits * $multiplier;

                // Min/Max limitleri uygula
                $minCredits = (float) ($config['min_credits'] ?? 0);
                $maxCredits = (float) ($config['max_credits'] ?? PHP_FLOAT_MAX);

                return max($minCredits, min($maxCredits, $credits));

            case 'token_full':
            default:
                // Normal token hesaplama + özel multiplier
                $baseCredits = $this->calculateCreditsForModel(
                    $providerName,
                    $modelName,
                    $inputTokens,
                    $outputTokens
                );

                $multiplier = (float) ($config['multiplier'] ?? 1.0);
                $credits = $baseCredits * $multiplier;

                // Min/Max limitleri uygula
                $minCredits = (float) ($config['min_credits'] ?? 0);
                $maxCredits = (float) ($config['max_credits'] ?? PHP_FLOAT_MAX);

                return max($minCredits, min($maxCredits, $credits));
        }
    }

    /**
     * Base token rate getir
     */
    public function getBaseTokenRate(): float
    {
        return (float) Cache::remember('base_token_rate', self::CACHE_TTL, function() {
            // Settings tablosundan al, yoksa default - float'a cast et
            $rate = setting('base_token_credit_rate', self::DEFAULT_RATE);
            return (float) $rate;
        });
    }

    /**
     * Base token rate ayarla
     */
    public function setBaseTokenRate(float $rate): bool
    {
        try {
            setting(['base_token_credit_rate' => $rate]);
            Cache::forget('base_token_rate');
            
            Log::info("Base token rate updated", ['rate' => $rate]);
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to update base token rate", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kredi maliyeti tahmin et
     */
    public function estimateCreditCost(
        string $input, 
        ?string $provider = null, 
        ?string $model = null
    ): array {
        $estimatedInputTokens = $this->estimateTokens($input);
        $estimatedOutputTokens = (int) ($estimatedInputTokens * 1.5); // Output genelde %50 daha fazla
        
        if ($provider && $model) {
            $credits = $this->calculateCreditsForModel(
                $provider, 
                $model, 
                $estimatedInputTokens, 
                $estimatedOutputTokens
            );
        } else {
            $credits = $this->calculateWithDefaultRate($estimatedInputTokens, $estimatedOutputTokens);
        }
        
        return [
            'estimated_input_tokens' => $estimatedInputTokens,
            'estimated_output_tokens' => $estimatedOutputTokens,
            'estimated_total_tokens' => $estimatedInputTokens + $estimatedOutputTokens,
            'estimated_credits' => $credits,
            'provider' => $provider,
            'model' => $model
        ];
    }

    /**
     * Model rate detaylarını getir
     */
    public function getModelRateDetails(string $providerName, string $modelName): array
    {
        $rate = $this->getModelRate($providerName, $modelName);
        
        if (!$rate) {
            return [
                'found' => false,
                'provider' => $providerName,
                'model' => $modelName,
                'using_default' => true,
                'rate' => self::DEFAULT_RATE
            ];
        }
        
        return [
            'found' => true,
            'provider' => $providerName,
            'model' => $modelName,
            'input_rate' => $rate->credit_per_1k_input_tokens,
            'output_rate' => $rate->credit_per_1k_output_tokens,
            'markup_percentage' => $rate->markup_percentage,
            'base_cost_usd' => $rate->base_cost_usd,
            'effective_input_rate' => $rate->getEffectiveInputRate(),
            'effective_output_rate' => $rate->getEffectiveOutputRate(),
            'using_default' => false
        ];
    }

    /**
     * Tüm provider'lar için model comparison
     */
    public function compareModelsAcrossProviders(int $inputTokens, int $outputTokens, ?array $providerIds = null): array
    {
        $comparison = [];
        
        $query = AIProvider::where('is_active', true);
        
        if ($providerIds && !empty($providerIds)) {
            $query->whereIn('id', $providerIds);
        }
        
        $providers = $query->get();
        
        foreach ($providers as $provider) {
            $models = $provider->getAvailableModelsWithRates();
            
            $providerModels = [];
            foreach ($models as $modelData) {
                $credits = $this->calculateCreditsForModel(
                    $provider->name,
                    $modelData['model_name'],
                    $inputTokens,
                    $outputTokens
                );
                
                // Input ve output ayrı hesapla
                $inputCredits = $this->calculateInputCredits($provider->name, $modelData['model_name'], $inputTokens);
                $outputCredits = $this->calculateOutputCredits($provider->name, $modelData['model_name'], $outputTokens);
                
                $providerModels[] = [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->name,
                    'model_name' => $modelData['model_name'],
                    'input_credits' => $inputCredits,
                    'output_credits' => $outputCredits,
                    'total_credits' => $credits,
                    'input_rate' => $modelData['input_rate'] ?? 0,
                    'output_rate' => $modelData['output_rate'] ?? 0,
                    'cost_efficiency_score' => $this->calculateCostEfficiencyScore($credits, $inputTokens + $outputTokens)
                ];
            }
            
            if (!empty($providerModels)) {
                $comparison[$provider->id] = $providerModels;
            }
        }
        
        return $comparison;
        
        return [
            'comparison' => $comparison,
            'cheapest' => $comparison[0] ?? null,
            'most_expensive' => end($comparison) ?: null,
            'average_cost' => array_sum(array_column($comparison, 'estimated_credits')) / count($comparison),
            'generated_at' => now()->toISOString()
        ];
    }

    /**
     * Bulk operations için kredi hesaplama
     */
    public function calculateBulkCredits(array $operations): array
    {
        $totalCredits = 0;
        $details = [];
        
        foreach ($operations as $operation) {
            $credits = $this->calculateCreditsForModel(
                $operation['provider'],
                $operation['model'],
                $operation['input_tokens'],
                $operation['output_tokens'] ?? 0
            );
            
            $totalCredits += $credits;
            
            $details[] = [
                'operation_id' => $operation['id'] ?? uniqid(),
                'provider' => $operation['provider'],
                'model' => $operation['model'],
                'input_tokens' => $operation['input_tokens'],
                'output_tokens' => $operation['output_tokens'] ?? 0,
                'credits' => $credits
            ];
        }
        
        return [
            'total_credits' => $totalCredits,
            'operation_count' => count($operations),
            'average_credits_per_operation' => $totalCredits / count($operations),
            'details' => $details
        ];
    }

    /**
     * Input token kredisi hesapla
     */
    public function calculateInputCredits(string $providerName, string $modelName, int $inputTokens): float
    {
        $rate = $this->getModelRate($providerName, $modelName);
        
        if (!$rate) {
            return ($inputTokens / 1000) * self::DEFAULT_RATE;
        }
        
        return $rate->calculateInputCost($inputTokens);
    }
    
    /**
     * Output token kredisi hesapla
     */
    public function calculateOutputCredits(string $providerName, string $modelName, int $outputTokens): float
    {
        $rate = $this->getModelRate($providerName, $modelName);
        
        if (!$rate) {
            return ($outputTokens / 1000) * self::DEFAULT_RATE;
        }
        
        return $rate->calculateOutputCost($outputTokens);
    }

    // Private helper methods

    private function getModelRate(string $providerName, string $modelName): ?AIModelCreditRate
    {
        $cacheKey = "model_rate:{$providerName}:{$modelName}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($providerName, $modelName) {
            // Try AIModelCreditRate first
            $rate = AIModelCreditRate::getRateForModel($providerName, $modelName);

            // Fallback: Use AIProviderModel if AIModelCreditRate is empty
            if (!$rate) {
                $providerModel = \Modules\AI\App\Models\AIProviderModel::whereHas('provider', function($query) use ($providerName) {
                    $query->where('name', $providerName);
                })
                ->where('model_name', $modelName)
                ->where('is_active', true)
                ->first();

                if ($providerModel) {
                    // Create AIModelCreditRate wrapper from AIProviderModel
                    $rate = new AIModelCreditRate();
                    $rate->model_name = $providerModel->model_name;
                    $rate->credit_per_1k_input_tokens = $providerModel->credit_per_1k_input_tokens;
                    $rate->credit_per_1k_output_tokens = $providerModel->credit_per_1k_output_tokens;
                    $rate->markup_percentage = $providerModel->markup_percentage ?? 0;
                    $rate->exists = false; // Mark as not from DB to avoid save issues
                }
            }

            return $rate;
        });
    }

    private function calculateWithDefaultRate(int $inputTokens, int $outputTokens): float
    {
        $baseRate = $this->getBaseTokenRate();
        return (($inputTokens + $outputTokens) / 1000) * $baseRate;
    }

    private function estimateTokens(string $text): int
    {
        // Basit token estimation (yaklaşık 1 token = 4 karakter)
        return (int) ceil(mb_strlen($text) / 4);
    }

    private function calculateCostEfficiencyScore(float $credits, int $tokens): float
    {
        // Düşük kredi/token oranı = yüksek efficiency score
        $ratio = $credits / ($tokens / 1000);
        return round(max(0, 10 - $ratio), 2);
    }
}