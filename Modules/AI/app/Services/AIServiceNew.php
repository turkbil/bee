<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\Core\AITranslationService;
use Modules\AI\App\Services\Core\AIFeatureService;
use Modules\AI\App\Services\Core\AIConversationService;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Services\ConversationService;
use Modules\AI\App\Services\PromptService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\AIProviderManager;
use Modules\AI\App\Services\ModelBasedCreditService;
use Modules\AI\App\Services\SilentFallbackService;
use Modules\AI\App\Services\Context\ContextEngine;
use Modules\AI\App\Services\ConversationTracker;
use App\Helpers\TenantHelpers;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 AI SERVICE - MODULAR ORCHESTRATOR
 * 
 * Bu sınıf artık sadece orchestrator rolü oynuyor.
 * Tüm işlemler specialized service'lere delegated ediliyor.
 */
class AIServiceNew
{
    // Core services
    protected $translationService;
    protected $featureService;
    protected $conversationService;
    
    // Infrastructure services
    protected $deepSeekService;
    protected $conversationServiceOld;
    protected $promptService;
    protected $aiTokenService;
    protected $providerManager;
    protected $silentFallbackService;
    protected $contextEngine;
    
    // Current state
    public $currentProvider;
    protected $currentService;

    public function __construct(
        ?DeepSeekService $deepSeekService = null,
        ?ConversationService $conversationService = null,
        ?PromptService $promptService = null,
        ?AITokenService $aiTokenService = null,
        ?ContextEngine $contextEngine = null
    ) {
        // Infrastructure services
        $this->providerManager = new AIProviderManager();
        $this->silentFallbackService = new SilentFallbackService(
            app(ModelBasedCreditService::class),
            $this->providerManager
        );
        
        $this->deepSeekService = $deepSeekService ?: app(DeepSeekService::class);
        $this->conversationServiceOld = $conversationService ?: app(ConversationService::class);
        $this->promptService = $promptService ?: app(PromptService::class);
        $this->aiTokenService = $aiTokenService ?: app(AITokenService::class);
        $this->contextEngine = $contextEngine ?: app(ContextEngine::class);
        
        // Core specialized services
        $this->translationService = new AITranslationService($this);
        $this->featureService = new AIFeatureService($this);
        $this->conversationService = new AIConversationService();
        
        Log::info('🔧 AIService initialized with modular structure');
    }

    // ========================================================================
    // 🌐 TRANSLATION METHODS - Delegated to AITranslationService
    // ========================================================================

    /**
     * 🔥 ULTRA ASSERTIVE TRANSLATION - Delegated method
     */
    public function translateText(string $text, string $fromLang, string $toLang, array $options = []): string
    {
        return $this->translationService->translateText($text, $fromLang, $toLang, $options);
    }

    /**
     * 🔥 LONG HTML TRANSLATION - Delegated method
     */
    public function translateLongHtmlContent(string $html, string $fromLang, string $toLang, string $context): string
    {
        return $this->translationService->translateLongHtmlContent($html, $fromLang, $toLang, $context);
    }

    // ========================================================================
    // 🎯 FEATURE METHODS - Delegated to AIFeatureService
    // ========================================================================

    /**
     * 🚀 AI FEATURE EXECUTION - Delegated method
     */
    public function askFeature($feature, string $userInput, array $options = []): array
    {
        return $this->featureService->askFeature($feature, $userInput, $options);
    }

    /**
     * 🎯 EXECUTE FEATURE - Delegated method
     */
    public function executeFeature($input, array $options = []): array
    {
        $feature = $options['feature'] ?? null;
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Feature not specified',
                'data' => []
            ];
        }
        
        return $this->askFeature($feature, $input, $options);
    }

    // ========================================================================
    // 💬 CONVERSATION METHODS - Delegated to AIConversationService
    // ========================================================================

    /**
     * 📊 CREATE CONVERSATION RECORD - Delegated method
     */
    public function createConversationRecord(string $userMessage, string $aiResponse, string $type = 'chat', array $metadata = []): void
    {
        $this->conversationService->createConversationRecord($userMessage, $aiResponse, $type, $metadata);
    }

    // ========================================================================
    // 🔧 CORE METHODS - Direct implementation (Provider Management & Processing)
    // ========================================================================

    /**
     * 🔥 CORE REQUEST PROCESSING - Ana işlem motoru
     */
    public function processRequest(
        string $prompt, 
        int $maxTokens = 2000, 
        float $temperature = 0.7, 
        ?string $model = null,
        ?string $systemPrompt = null,
        ?array $metadata = null,
        array $options = []
    ): array {
        try {
            Log::info('🔧 Processing AI request', [
                'prompt_length' => strlen($prompt),
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'model' => $model,
                'has_system_prompt' => !empty($systemPrompt),
                'options' => $options
            ]);

            // Provider selection and request processing
            $tenantId = TenantHelpers::getTenantId();
            $tenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
            
            // Get best available provider
            $this->currentProvider = $this->providerManager->getBestAvailableProvider($tenant);
            
            if (!$this->currentProvider) {
                throw new \Exception('No AI provider available');
            }

            // Process request through provider
            $response = $this->currentProvider->processRequest([
                'prompt' => $prompt,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'model' => $model,
                'system_prompt' => $systemPrompt,
                'metadata' => $metadata,
                'options' => $options
            ]);

            // Apply content filtering if needed (bypass for translations)
            $isTranslation = isset($options['context_type']) && 
                           (str_contains($options['context_type'], 'translation') || 
                            str_contains($options['context_type'], 'translate'));
            
            if (!$isTranslation && !isset($options['bypass_all_filters'])) {
                $response['data']['content'] = $this->enforceStructure($response['data']['content'] ?? '', $options);
            }

            Log::info('✅ AI request processed successfully', [
                'provider' => $this->currentProvider->name,
                'response_length' => strlen($response['data']['content'] ?? ''),
                'success' => $response['success']
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('❌ AI request processing failed', [
                'error' => $e->getMessage(),
                'prompt_preview' => substr($prompt, 0, 100)
            ]);

            // Try silent fallback
            if ($this->silentFallbackService) {
                Log::info('🔄 Attempting silent fallback...');
                return $this->silentFallbackService->attemptFallback($prompt, $maxTokens, $temperature, $model, $systemPrompt, $metadata);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * 🛡️ ENFORCE STRUCTURE - Content filtering
     */
    private function enforceStructure(string $content, array $requirements = []): string
    {
        // Translation context bypass - no filtering for translations
        $isTranslation = isset($requirements['context_type']) && 
                        (str_contains($requirements['context_type'], 'translation') || 
                         str_contains($requirements['context_type'], 'translate'));

        if ($isTranslation) {
            Log::info('🌐 Translation context detected - bypassing all filters');
            return $content;
        }

        // Apply normal content filtering for non-translation requests
        // (This would include removeProhibitedPhrases, cleanHtmlTags etc.)
        
        return $content;
    }

    /**
     * 📊 DEBUG INFO LOGGING
     */
    private function logDebugInfo(array $data): void
    {
        Log::info('🔍 AIService Debug Info', $data);
    }
}