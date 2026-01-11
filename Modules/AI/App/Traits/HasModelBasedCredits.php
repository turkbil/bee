<?php

declare(strict_types=1);

namespace Modules\AI\App\Traits;

use Modules\AI\App\Services\ModelBasedCreditService;

/**
 * Trait for Model-based Credit Management
 * 
 * Provides model-based credit checking and deduction functionality
 */
trait HasModelBasedCredits
{
    protected ModelBasedCreditService $creditService;

    /**
     * Initialize credit service
     */
    protected function initializeCreditService(): void
    {
        $this->creditService = new ModelBasedCreditService();
    }

    /**
     * Check credits before AI request
     */
    protected function checkModelBasedCredits(
        string $prompt,
        ?int $maxTokens = null,
        ?string $model = null
    ): array {
        $tenant = tenant();
        if (!$tenant) {
            return ['sufficient' => true, 'required_credits' => 0];
        }

        // Initialize if needed
        if (!isset($this->creditService)) {
            $this->initializeCreditService();
        }

        // Get model
        $finalModel = $model ?? ($tenant->default_ai_model) ?? $this->currentProvider->default_model;

        // Estimate tokens
        $tokenEstimate = $this->creditService->estimateTokens($prompt, $maxTokens);

        // Check credits
        return $this->creditService->checkCredits(
            $tenant,
            $this->currentProvider->id,
            $finalModel,
            $tokenEstimate['input_tokens'],
            $tokenEstimate['output_tokens']
        );
    }

    /**
     * Deduct credits after AI response
     */
    protected function deductModelBasedCredits(
        array $response,
        ?string $model = null,
        string $featureType = 'ai_feature',
        ?int $featureId = null
    ): float {
        $tenant = tenant();
        if (!$tenant) {
            return 0.0;
        }

        // Initialize if needed
        if (!isset($this->creditService)) {
            $this->initializeCreditService();
        }

        // Parse token usage
        $tokenUsage = $this->creditService->parseTokenUsage($response);

        // Get model
        $finalModel = $model ?? ($tenant->default_ai_model) ?? $this->currentProvider->default_model;

        // Deduct credits
        return $this->creditService->deductCredits(
            $tenant,
            $this->currentProvider->id,
            $finalModel,
            $tokenUsage['input_tokens'],
            $tokenUsage['output_tokens'],
            $featureType,
            $featureId
        );
    }

    /**
     * Create insufficient credits error response
     */
    protected function createInsufficientCreditsError(
        float $requiredCredits,
        float $availableCredits,
        string $model
    ): array {
        if (!isset($this->creditService)) {
            $this->initializeCreditService();
        }

        return $this->creditService->createInsufficientCreditsResponse(
            $requiredCredits,
            $availableCredits,
            $model
        );
    }

    /**
     * Wrapper for model-based credit check with error response
     */
    protected function validateModelCredits(
        string $prompt,
        ?int $maxTokens = null,
        ?string $model = null
    ): ?array {
        $creditCheck = $this->checkModelBasedCredits($prompt, $maxTokens, $model);

        if (!$creditCheck['sufficient']) {
            return $this->createInsufficientCreditsError(
                $creditCheck['required_credits'],
                $creditCheck['available_credits'],
                $creditCheck['model']
            );
        }

        return null; // No error, sufficient credits
    }
}