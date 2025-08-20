<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Model-based Credit Management Service
 * 
 * Handles credit checks and deductions for AI model usage
 */
readonly class ModelBasedCreditService
{
    public function __construct()
    {
        // Service dependencies injected
    }

    /**
     * Check if tenant has enough credits for the request
     */
    public function hasEnoughCredits($tenant, float $requiredCredits): bool
    {
        if (!$tenant) {
            return true; // System tenant has unlimited credits
        }

        $currentCredits = $tenant->ai_credits ?? $tenant->credits ?? 0;
        return $currentCredits >= $requiredCredits;
    }

    /**
     * Calculate estimated cost for a request
     */
    public function calculateEstimatedCost(
        int $providerId,
        string $model,
        string $prompt,
        string $expectedOutput = ''
    ): float {
        $tokenEstimate = $this->estimateTokens($prompt);
        $outputEstimate = $expectedOutput ? $this->estimateTokens($expectedOutput)['input_tokens'] : 1000;

        return ai_calculate_model_credits(
            $providerId,
            $model,
            $tokenEstimate['input_tokens'],
            $outputEstimate
        );
    }

    /**
     * Check credit warning levels
     */
    public function getCreditWarningLevel($tenant): array
    {
        if (!$tenant) {
            return ['level' => 'none', 'message' => null];
        }

        $currentCredits = $tenant->ai_credits ?? $tenant->credits ?? 0;

        if ($currentCredits <= 10) {
            return [
                'level' => 'critical',
                'message' => '🚨 KRİTİK: Sadece ' . $currentCredits . ' kredi kaldı! Hemen kredi satın alın.',
                'credits' => $currentCredits,
                'threshold' => 10
            ];
        } elseif ($currentCredits <= 50) {
            return [
                'level' => 'low',
                'message' => '⚠️ DÜŞÜK: ' . $currentCredits . ' kredi kaldı. Yakında kredi satın almanız önerilir.',
                'credits' => $currentCredits,
                'threshold' => 50
            ];
        } elseif ($currentCredits <= 100) {
            return [
                'level' => 'moderate',
                'message' => '💡 DİKKAT: ' . $currentCredits . ' kredi kaldı.',
                'credits' => $currentCredits,
                'threshold' => 100
            ];
        }

        return ['level' => 'sufficient', 'message' => null];
    }

    /**
     * Check if tenant should be shown warning for credits
     */
    public function shouldShowCreditWarning($tenant): bool
    {
        $warning = $this->getCreditWarningLevel($tenant);
        return in_array($warning['level'], ['critical', 'low', 'moderate']);
    }

    /**
     * Check if request should be blocked due to insufficient credits
     */
    public function shouldBlockRequest($tenant, float $requiredCredits): array
    {
        if (!$tenant) {
            return ['block' => false, 'reason' => null];
        }

        $currentCredits = $tenant->ai_credits ?? $tenant->credits ?? 0;

        if ($currentCredits < $requiredCredits) {
            return [
                'block' => true,
                'reason' => 'insufficient_credits',
                'message' => "❌ Yetersiz kredi! Gerekli: {$requiredCredits}, Mevcut: {$currentCredits}",
                'required' => $requiredCredits,
                'available' => $currentCredits,
                'deficit' => $requiredCredits - $currentCredits
            ];
        }

        return ['block' => false, 'reason' => null];
    }

    /**
     * Check if tenant has enough credits for the request
     */
    public function checkCredits(
        $tenant,
        int $providerId,
        string $model,
        int $estimatedInputTokens,
        int $estimatedOutputTokens
    ): array {
        if (!$tenant) {
            return ['sufficient' => true, 'required_credits' => 0];
        }

        // Model bazlı kredi hesaplama
        $requiredCredits = ai_calculate_model_credits(
            $providerId,
            $model,
            $estimatedInputTokens,
            $estimatedOutputTokens
        );

        $sufficient = !$requiredCredits || $tenant->credits >= $requiredCredits;

        return [
            'sufficient' => $sufficient,
            'required_credits' => $requiredCredits,
            'available_credits' => $tenant->credits,
            'model' => $model,
            'estimated_input_tokens' => $estimatedInputTokens,
            'estimated_output_tokens' => $estimatedOutputTokens
        ];
    }

    /**
     * Estimate token usage for prompt
     */
    public function estimateTokens(string $prompt, ?int $maxOutputTokens = null): array
    {
        // Rough estimation: 4 characters per token
        $inputTokens = (int) ceil(strlen($prompt) / 4);
        $outputTokens = $maxOutputTokens ?? 1000; // Default conservative estimate

        return [
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens
        ];
    }

    /**
     * Deduct credits based on actual usage
     */
    public function deductCredits(
        $tenant,
        int $providerId,
        string $model,
        int $actualInputTokens,
        int $actualOutputTokens,
        string $featureType = 'ai_feature',
        ?int $featureId = null
    ): float {
        if (!$tenant) {
            return 0.0;
        }

        $usedCredits = ai_use_credits_with_model(
            $actualInputTokens,
            $actualOutputTokens,
            'openai',
            $model,
            [
                'tenant_id' => $tenant->id,
                'provider_id' => $providerId,
                'feature_type' => $featureType,
                'feature_id' => $featureId
            ]
        );

        // Feature adını da log'a ekle
        $featureName = null;
        if ($featureId && $featureType === 'ai_feature') {
            $feature = \Modules\AI\App\Models\AIFeature::find($featureId);
            $featureName = $feature?->name ?? "Feature #{$featureId}";
        }

        Log::info('🔥 Model-based credit deduction', [
            'tenant_id' => $tenant->id,
            'provider_id' => $providerId,
            'model' => $model,
            'input_tokens' => $actualInputTokens,
            'output_tokens' => $actualOutputTokens,
            'credits_used' => $usedCredits,
            'remaining_credits' => method_exists($tenant, 'fresh') ? $tenant->fresh()->credits : ($tenant->credits ?? 0),
            'feature_type' => $featureType,
            'feature_id' => $featureId,
            'feature_name' => $featureName
        ]);

        return $usedCredits;
    }

    /**
     * Parse token usage from AI response
     */
    public function parseTokenUsage(array $response): array
    {
        // Different providers use different response formats
        $inputTokens = $response['usage']['prompt_tokens'] 
            ?? $response['usage']['input_tokens'] 
            ?? $response['tokens_used']['input'] 
            ?? 0;

        $outputTokens = $response['usage']['completion_tokens']
            ?? $response['usage']['output_tokens']
            ?? $response['tokens_used']['output']
            ?? 0;

        $totalTokens = $response['usage']['total_tokens']
            ?? $response['tokens_used']['total']
            ?? ($inputTokens + $outputTokens);

        return [
            'input_tokens' => (int) $inputTokens,
            'output_tokens' => (int) $outputTokens,
            'total_tokens' => (int) $totalTokens
        ];
    }

    /**
     * Create insufficient credits response
     */
    public function createInsufficientCreditsResponse(
        float $requiredCredits,
        float $availableCredits,
        string $model
    ): array {
        return [
            'success' => false,
            'error' => 'insufficient_credits',
            'message' => "Yetersiz kredi. Gerekli: {$requiredCredits}, Mevcut: {$availableCredits}",
            'required_credits' => $requiredCredits,
            'available_credits' => $availableCredits,
            'model' => $model
        ];
    }

    /**
     * Get model for tenant (tenant model or provider default)
     */
    public function getTenantModel($tenant, $provider, ?string $requestedModel = null): string
    {
        return $requestedModel 
            ?? ($tenant?->default_ai_model) 
            ?? $provider->default_model;
    }
}