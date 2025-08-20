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
     * Base token rate getir
     */
    public function getBaseTokenRate(): float
    {
        return Cache::remember('base_token_rate', self::CACHE_TTL, function() {
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
            return AIModelCreditRate::getRateForModel($providerName, $modelName);
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