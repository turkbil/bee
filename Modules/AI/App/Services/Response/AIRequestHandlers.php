<?php

namespace Modules\AI\App\Services\Response;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Services\Response\AIResponseFormatters;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantHelpers;

/**
 * AI Request Handlers - İstek türlerine göre handler metodları
 * 
 * AIResponseRepository'den ayrılmış handler metodları:
 * - handleAdminChat
 * - handleFeatureTest  
 * - handleProwessTest
 * - handleConversation
 * - handleHelperFunction
 * - handleBulkTest
 * - handleWidgetFeature
 * - handleGenericRequest
 */
class AIRequestHandlers
{
    private AIService $aiService;
    private AIPriorityEngine $priorityEngine;
    private AIResponseFormatters $formatters;

    public function __construct(
        AIService $aiService,
        AIResponseFormatters $formatters
    ) {
        $this->aiService = $aiService;
        $this->priorityEngine = new AIPriorityEngine();
        $this->formatters = $formatters;
    }

    /**
     * Admin panel chat yanıtları
     */
    public function handleAdminChat(array $params): array
    {
        $userMessage = $params['message'] ?? '';
        $customPrompt = $params['custom_prompt'] ?? '';

        if (empty($userMessage)) {
            return [
                'success' => false,
                'error' => 'Mesaj boş olamaz'
            ];
        }

        Log::info('AI Admin Chat Request', [
            'tenant_id' => tenant('id'),
            'message_length' => strlen($userMessage),
            'has_custom_prompt' => !empty($customPrompt)
        ]);

        try {
            $response = $this->aiService->sendRequest($userMessage, [
                'custom_prompt' => $customPrompt,
                'context_type' => 'admin_chat'
            ]);

            return $this->formatters->formatAdminResponse($response['response'] ?? '');

        } catch (\Exception $e) {
            Log::error('Admin Chat Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'AI yanıt alınamadı: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Feature test yanıtları
     */
    public function handleFeatureTest(array $params): array
    {
        $featureId = $params['feature_id'] ?? null;
        $userInput = $params['user_input'] ?? [];
        $testMode = $params['test_mode'] ?? 'standard';

        if (!$featureId) {
            return [
                'success' => false,
                'error' => 'Feature ID gerekli'
            ];
        }

        $feature = AIFeature::find($featureId);
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Feature bulunamadı'
            ];
        }

        Log::info('AI Feature Test Request', [
            'feature_slug' => $feature->slug,
            'test_mode' => $testMode,
            'input_keys' => array_keys($userInput)
        ]);

        try {
            $response = $this->aiService->sendRequest($userInput['message'] ?? '', [
                'feature_id' => $featureId,
                'context_type' => 'feature_test',
                'test_mode' => $testMode
            ]);

            return $this->formatters->formatFeatureResponse(
                $response['response'] ?? '', 
                $feature, 
                'feature_test'
            );

        } catch (\Exception $e) {
            Log::error('Feature Test Error', [
                'feature_id' => $featureId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Feature test hatası: ' . $e->getMessage(),
                'feature_name' => $feature->title ?? 'Bilinmeyen Feature'
            ];
        }
    }

    /**
     * Prowess test yanıtları (showcase için)
     */
    public function handleProwessTest(array $params): array
    {
        $featureId = $params['feature_id'] ?? null;
        $userInput = $params['user_input'] ?? [];
        $showcaseMode = $params['showcase_mode'] ?? true;

        if (!$featureId) {
            return [
                'success' => false,
                'error' => 'Prowess test için feature ID gerekli'
            ];
        }

        $feature = AIFeature::find($featureId);
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Prowess feature bulunamadı'
            ];
        }

        Log::info('AI Prowess Test Request', [
            'feature_slug' => $feature->slug,
            'showcase_mode' => $showcaseMode,
            'tenant_id' => tenant('id')
        ]);

        try {
            // Priority Engine'den en iyi prompt'u al
            $priorityPrompts = $this->priorityEngine->generatePrompt($feature, $userInput);

            $response = $this->aiService->sendRequest($priorityPrompts['final_prompt'], [
                'feature_id' => $featureId,
                'context_type' => 'prowess_showcase',
                'priority_context' => $priorityPrompts
            ]);

            return $this->formatters->formatProwessResponse(
                $response['response'] ?? null, 
                $feature
            );

        } catch (\Exception $e) {
            Log::error('Prowess Test Error', [
                'feature_id' => $featureId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Prowess test hatası: ' . $e->getMessage(),
                'fallback_response' => $this->formatters->formatProwessResponse(
                    'Prowess test sırasında hata oluştu. Lütfen tekrar deneyin.',
                    $feature
                )
            ];
        }
    }

    /**
     * Conversation yanıtları
     */
    public function handleConversation(array $params): array
    {
        $message = $params['message'] ?? '';
        $conversationId = $params['conversation_id'] ?? null;
        $context = $params['context'] ?? [];

        if (empty($message)) {
            return [
                'success' => false,
                'error' => 'Konuşma mesajı boş olamaz'
            ];
        }

        Log::info('AI Conversation Request', [
            'conversation_id' => $conversationId,
            'message_length' => strlen($message),
            'has_context' => !empty($context)
        ]);

        try {
            $response = $this->aiService->sendRequest($message, [
                'conversation_id' => $conversationId,
                'context_type' => 'conversation',
                'context' => $context
            ]);

            return $this->formatters->formatConversationResponse($response['response'] ?? '');

        } catch (\Exception $e) {
            Log::error('Conversation Error', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Konuşma yanıtı alınamadı: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper function yanıtları
     */
    public function handleHelperFunction(array $params): array
    {
        $featureId = $params['feature_id'] ?? null;
        $userInput = $params['user_input'] ?? [];
        $helperName = $params['helper_name'] ?? 'ai_helper';
        $conditions = $params['conditions'] ?? [];

        if (!$featureId) {
            return [
                'success' => false,
                'error' => 'Helper function için feature ID gerekli'
            ];
        }

        $feature = AIFeature::find($featureId);
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Helper feature bulunamadı'
            ];
        }

        Log::info('AI Helper Function Request', [
            'feature_slug' => $feature->slug,
            'helper_name' => $helperName,
            'conditions' => array_keys($conditions)
        ]);

        // Feature-specific handling
        return match($feature->slug) {
            'hizli-seo-analizi', 'seo-analiz' => 
                $this->handleSEOAnalysisFeature($feature, $userInput, $helperName, $conditions),
            'ai-chat', 'genel-ai-chat' => 
                $this->handleAIChatFeature($feature, $userInput, $helperName, $conditions),
            default => 
                $this->handleGenericFeature($feature, $userInput, $helperName, $conditions)
        };
    }

    /**
     * SEO analiz feature'ı için özel işlem
     */
    private function handleSEOAnalysisFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            $message = $this->buildSEOAnalysisMessage($userInput);
            
            $response = $this->aiService->sendRequest($message, [
                'feature_id' => $feature->id,
                'context_type' => 'helper_function',
                'helper_name' => $helperName,
                'seo_conditions' => $conditions
            ]);

            return $this->formatters->formatSEOAnalysisResponse(
                $response['response'] ?? '', 
                $feature, 
                $helperName
            );

        } catch (\Exception $e) {
            Log::error('SEO Analysis Helper Error', [
                'feature_id' => $feature->id,
                'helper_name' => $helperName,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'SEO analiz hatası: ' . $e->getMessage(),
                'type' => 'helper_function'
            ];
        }
    }

    /**
     * AI Chat feature'ı için özel işlem  
     */
    private function handleAIChatFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            $message = $this->buildAIChatMessage($userInput);

            $response = $this->aiService->sendRequest($message, [
                'feature_id' => $feature->id,
                'context_type' => 'helper_function',
                'helper_name' => $helperName,
                'chat_conditions' => $conditions
            ]);

            return [
                'success' => true,
                'response' => $this->formatters->formatAIChatResponse($response['response'] ?? '', $helperName),
                'feature' => $feature->title,
                'helper_name' => $helperName,
                'type' => 'helper_function'
            ];

        } catch (\Exception $e) {
            Log::error('AI Chat Helper Error', [
                'feature_id' => $feature->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'AI Chat hatası: ' . $e->getMessage(),
                'type' => 'helper_function'
            ];
        }
    }

    /**
     * Genel feature işlemi
     */
    private function handleGenericFeature($feature, array $userInput, string $helperName, array $conditions): array
    {
        try {
            $message = $this->buildGenericFeatureMessage($userInput, $feature);

            $response = $this->aiService->sendRequest($message, [
                'feature_id' => $feature->id,
                'context_type' => 'helper_function',
                'helper_name' => $helperName,
                'conditions' => $conditions
            ]);

            return [
                'success' => true,
                'response' => $this->formatters->formatGenericResponse($response['response'] ?? '', $feature, $helperName),
                'feature' => $feature->title,
                'type' => 'helper_function'
            ];

        } catch (\Exception $e) {
            Log::error('Generic Feature Helper Error', [
                'feature_slug' => $feature->slug,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Feature işlem hatası: ' . $e->getMessage(),
                'type' => 'helper_function'
            ];
        }
    }

    /**
     * Bulk test işlemleri
     */
    public function handleBulkTest(array $params): array
    {
        $features = $params['features'] ?? [];
        $userInput = $params['user_input'] ?? [];
        $testCount = count($features);

        if (empty($features)) {
            return [
                'success' => false,
                'error' => 'Bulk test için feature listesi gerekli'
            ];
        }

        Log::info('AI Bulk Test Request', [
            'feature_count' => $testCount,
            'tenant_id' => tenant('id')
        ]);

        $results = [];
        $successCount = 0;

        foreach ($features as $featureId) {
            try {
                $result = $this->handleFeatureTest([
                    'feature_id' => $featureId,
                    'user_input' => $userInput,
                    'test_mode' => 'bulk'
                ]);

                if ($result['success'] ?? false) {
                    $successCount++;
                }

                $results[] = $result;

            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'feature_id' => $featureId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => $successCount > 0,
            'results' => $results,
            'summary' => [
                'total' => $testCount,
                'successful' => $successCount,
                'failed' => $testCount - $successCount
            ]
        ];
    }

    /**
     * Widget feature işlemleri
     */
    public function handleWidgetFeature(array $params): array
    {
        $featureId = $params['feature_id'] ?? null;
        $widgetData = $params['widget_data'] ?? [];
        $widgetType = $params['widget_type'] ?? 'generic';

        if (!$featureId) {
            return [
                'success' => false,
                'error' => 'Widget feature için feature ID gerekli'
            ];
        }

        $feature = AIFeature::find($featureId);
        if (!$feature) {
            return [
                'success' => false,
                'error' => 'Widget feature bulunamadı'
            ];
        }

        Log::info('AI Widget Feature Request', [
            'feature_slug' => $feature->slug,
            'widget_type' => $widgetType
        ]);

        try {
            $message = $this->buildWidgetMessage($widgetData, $widgetType);

            $response = $this->aiService->sendRequest($message, [
                'feature_id' => $featureId,
                'context_type' => 'widget_feature',
                'widget_type' => $widgetType
            ]);

            return [
                'success' => true,
                'response' => $this->formatters->formatWidgetResponse($response['response'] ?? '', $feature),
                'widget_type' => $widgetType,
                'feature' => $feature->title
            ];

        } catch (\Exception $e) {
            Log::error('Widget Feature Error', [
                'feature_id' => $featureId,
                'widget_type' => $widgetType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Widget feature hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Genel istekler
     */
    public function handleGenericRequest(array $params): array
    {
        $message = $params['message'] ?? '';
        $context = $params['context'] ?? [];

        if (empty($message)) {
            return [
                'success' => false,
                'error' => 'Generic request için mesaj gerekli'
            ];
        }

        Log::info('AI Generic Request', [
            'message_length' => strlen($message),
            'has_context' => !empty($context)
        ]);

        try {
            $response = $this->aiService->sendRequest($message, [
                'context_type' => 'generic',
                'context' => $context
            ]);

            return [
                'success' => true,
                'response' => $response['response'] ?? '',
                'type' => 'generic'
            ];

        } catch (\Exception $e) {
            Log::error('Generic Request Error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Generic request hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper metodları
     */
    private function buildSEOAnalysisMessage(array $userInput): string
    {
        $url = $userInput['url'] ?? '';
        $keywords = $userInput['keywords'] ?? '';
        
        return "SEO analizi yap: {$url} (Anahtar kelimeler: {$keywords})";
    }

    private function buildAIChatMessage(array $userInput): string
    {
        return $userInput['message'] ?? 'AI chat mesajı';
    }

    private function buildGenericFeatureMessage(array $userInput, $feature): string
    {
        $message = $userInput['message'] ?? $userInput['content'] ?? '';
        return $message . " (Feature: {$feature->title})";
    }

    private function buildWidgetMessage(array $widgetData, string $widgetType): string
    {
        $content = $widgetData['content'] ?? '';
        return "Widget içeriği oluştur: {$content} (Tip: {$widgetType})";
    }
}