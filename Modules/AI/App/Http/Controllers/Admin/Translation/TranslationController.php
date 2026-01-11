<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Translation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\V3\TranslationEngine;
use Modules\AI\App\Services\V3\ContextAwareEngine;
use Modules\AI\App\Services\V3\SmartAnalyzer;

/**
 * Translation Controller V3
 * 
 * Enterprise-level translation management with:
 * - Multi-language translation with format preservation
 * - Context-aware translation optimization
 * - Bulk translation processing with progress tracking
 * - Quality assessment and improvement suggestions
 * - Translation analytics and performance monitoring
 */
class TranslationController extends Controller
{
    public function __construct(
        private readonly TranslationEngine $translationEngine,
        private readonly ContextAwareEngine $contextEngine,
        private readonly SmartAnalyzer $analyzer
    ) {}

    /**
     * Translate content with context awareness
     */
    public function translateContent(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:50000',
                'source_language' => 'required|string|size:2',
                'target_language' => 'required|string|size:2',
                'content_type' => 'sometimes|string|in:html,markdown,text,json',
                'context' => 'sometimes|array',
                'options' => 'sometimes|array',
                'options.preserve_formatting' => 'sometimes|boolean',
                'options.quality_level' => 'sometimes|string|in:standard,professional,premium',
                'options.tone' => 'sometimes|string|in:formal,casual,friendly,professional'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $content = $request->get('content');
            $sourceLanguage = $request->get('source_language');
            $targetLanguage = $request->get('target_language');
            $contentType = $request->get('content_type', 'text');
            $options = $request->get('options', []);

            // Get context for better translation
            $contextData = $this->contextEngine->detectContext([
                'user_id' => auth()->id(),
                'source_language' => $sourceLanguage,
                'target_language' => $targetLanguage,
                'content_type' => $contentType,
                'content_length' => strlen($content),
                ...$request->get('context', [])
            ]);

            // Perform translation with context
            $result = $this->translationEngine->translateContent(
                $content,
                $sourceLanguage,
                $targetLanguage,
                array_merge($options, [
                    'content_type' => $contentType,
                    'context' => $contextData
                ])
            );

            // Analyze translation quality
            $qualityScore = $this->analyzer->assessTranslationQuality(
                $content,
                $result['translated_content'],
                $sourceLanguage,
                $targetLanguage
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'original_content' => $content,
                    'translated_content' => $result['translated_content'],
                    'source_language' => $sourceLanguage,
                    'target_language' => $targetLanguage,
                    'content_type' => $contentType,
                    'quality_score' => $qualityScore,
                    'translation_time' => $result['processing_time'] ?? null,
                    'word_count' => str_word_count($content),
                    'character_count' => strlen($content),
                    'context_applied' => $contextData
                ],
                'message' => 'Content translated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'source_language' => $request->get('source_language'),
                'target_language' => $request->get('target_language'),
                'content_length' => strlen($request->get('content', '')),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Translation failed',
                'message' => config('app.debug') ? $e->getMessage() : 'Translation error occurred'
            ], 500);
        }
    }

    /**
     * Get available languages and translation capabilities
     */
    public function getAvailableLanguages(Request $request): JsonResponse
    {
        try {
            $languages = $this->translationEngine->getAvailableLanguages();
            $capabilities = $this->translationEngine->getTranslationCapabilities();

            return response()->json([
                'success' => true,
                'data' => [
                    'languages' => $languages,
                    'capabilities' => $capabilities,
                    'supported_formats' => ['html', 'markdown', 'text', 'json'],
                    'quality_levels' => ['standard', 'professional', 'premium'],
                    'available_tones' => ['formal', 'casual', 'friendly', 'professional']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Language data retrieval failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve language data'
            ], 500);
        }
    }

    /**
     * Start bulk translation operation
     */
    public function startBulkTranslation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1|max:1000',
                'items.*.content' => 'required|string|max:10000',
                'items.*.source_language' => 'required|string|size:2',
                'items.*.target_language' => 'required|string|size:2',
                'items.*.content_type' => 'sometimes|string|in:html,markdown,text,json',
                'options' => 'sometimes|array',
                'options.preserve_formatting' => 'sometimes|boolean',
                'options.quality_level' => 'sometimes|string|in:standard,professional,premium'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $items = $request->get('items');
            $options = $request->get('options', []);

            // Start bulk translation process
            $operationId = $this->translationEngine->startBulkTranslation(
                $items,
                array_merge($options, [
                    'user_id' => auth()->id(),
                    'created_at' => now()->toISOString()
                ])
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'operation_id' => $operationId,
                    'total_items' => count($items),
                    'status' => 'started',
                    'estimated_completion' => now()->addMinutes(count($items) * 2)->toISOString()
                ],
                'message' => 'Bulk translation started successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk translation start failed', [
                'item_count' => count($request->get('items', [])),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not start bulk translation'
            ], 500);
        }
    }

    /**
     * Get bulk translation status
     */
    public function getBulkTranslationStatus(string $operationId): JsonResponse
    {
        try {
            $status = $this->translationEngine->getBulkTranslationStatus($operationId);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'error' => 'Operation not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk translation status retrieval failed', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve operation status'
            ], 500);
        }
    }

    /**
     * Get translation alternatives for content
     */
    public function getTranslationAlternatives(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:5000',
                'source_language' => 'required|string|size:2',
                'target_language' => 'required|string|size:2',
                'alternatives_count' => 'sometimes|integer|min:2|max:5',
                'context' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $content = $request->get('content');
            $sourceLanguage = $request->get('source_language');
            $targetLanguage = $request->get('target_language');
            $alternativesCount = $request->get('alternatives_count', 3);

            $alternatives = $this->translationEngine->generateTranslationAlternatives(
                $content,
                $sourceLanguage,
                $targetLanguage,
                [
                    'alternatives_count' => $alternativesCount,
                    'context' => $request->get('context', []),
                    'user_id' => auth()->id()
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'original_content' => $content,
                    'source_language' => $sourceLanguage,
                    'target_language' => $targetLanguage,
                    'alternatives' => $alternatives,
                    'alternatives_count' => count($alternatives)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Translation alternatives generation failed', [
                'source_language' => $request->get('source_language'),
                'target_language' => $request->get('target_language'),
                'content_length' => strlen($request->get('content', '')),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not generate translation alternatives'
            ], 500);
        }
    }

    /**
     * Assess translation quality and get improvement suggestions
     */
    public function assessTranslationQuality(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'original_content' => 'required|string|max:10000',
                'translated_content' => 'required|string|max:10000',
                'source_language' => 'required|string|size:2',
                'target_language' => 'required|string|size:2',
                'content_type' => 'sometimes|string|in:html,markdown,text,json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $originalContent = $request->get('original_content');
            $translatedContent = $request->get('translated_content');
            $sourceLanguage = $request->get('source_language');
            $targetLanguage = $request->get('target_language');

            // Comprehensive quality assessment
            $qualityAnalysis = $this->analyzer->performDetailedTranslationAnalysis([
                'original_content' => $originalContent,
                'translated_content' => $translatedContent,
                'source_language' => $sourceLanguage,
                'target_language' => $targetLanguage,
                'content_type' => $request->get('content_type', 'text')
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'overall_score' => $qualityAnalysis['overall_score'],
                    'quality_metrics' => $qualityAnalysis['metrics'],
                    'improvement_suggestions' => $qualityAnalysis['suggestions'],
                    'detected_issues' => $qualityAnalysis['issues'] ?? [],
                    'language_pair_difficulty' => $qualityAnalysis['difficulty_level'] ?? 'medium',
                    'confidence_score' => $qualityAnalysis['confidence'] ?? 0.8
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Translation quality assessment failed', [
                'source_language' => $request->get('source_language'),
                'target_language' => $request->get('target_language'),
                'original_length' => strlen($request->get('original_content', '')),
                'translated_length' => strlen($request->get('translated_content', '')),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Quality assessment failed'
            ], 500);
        }
    }

    /**
     * Get translation history and analytics
     */
    public function getTranslationHistory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'sometimes|integer|min:1|max:100',
                'offset' => 'sometimes|integer|min:0',
                'source_language' => 'sometimes|string|size:2',
                'target_language' => 'sometimes|string|size:2',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $filters = [
                'user_id' => auth()->id(),
                'limit' => $request->get('limit', 20),
                'offset' => $request->get('offset', 0),
                'source_language' => $request->get('source_language'),
                'target_language' => $request->get('target_language'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to')
            ];

            $history = $this->translationEngine->getTranslationHistory($filters);
            $analytics = $this->analyzer->getTranslationAnalytics(auth()->id());

            return response()->json([
                'success' => true,
                'data' => [
                    'history' => $history['items'] ?? [],
                    'pagination' => $history['pagination'] ?? [],
                    'analytics' => [
                        'total_translations' => $analytics['total_translations'] ?? 0,
                        'languages_used' => $analytics['languages_used'] ?? [],
                        'average_quality_score' => $analytics['average_quality'] ?? 0,
                        'most_translated_languages' => $analytics['top_language_pairs'] ?? [],
                        'translation_volume_trend' => $analytics['volume_trend'] ?? []
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Translation history retrieval failed', [
                'user_id' => auth()->id(),
                'filters' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve translation history'
            ], 500);
        }
    }

    /**
     * Get language detection for content
     */
    public function detectLanguage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:10000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $content = $request->get('content');
            $detection = $this->translationEngine->detectLanguage($content);

            return response()->json([
                'success' => true,
                'data' => [
                    'detected_language' => $detection['language'],
                    'confidence' => $detection['confidence'],
                    'alternative_languages' => $detection['alternatives'] ?? [],
                    'content_sample' => substr($content, 0, 200)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Language detection failed', [
                'content_length' => strlen($request->get('content', '')),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Language detection failed'
            ], 500);
        }
    }

    /**
     * Get supported language pairs and their capabilities
     */
    public function getSupportedLanguagePairs(): JsonResponse
    {
        try {
            $languagePairs = $this->translationEngine->getSupportedLanguagePairs();

            return response()->json([
                'success' => true,
                'data' => [
                    'language_pairs' => $languagePairs,
                    'total_pairs' => count($languagePairs),
                    'most_popular_pairs' => $this->translationEngine->getPopularLanguagePairs(),
                    'quality_ratings' => $this->translationEngine->getLanguagePairQualityRatings()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Language pairs retrieval failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve language pairs'
            ], 500);
        }
    }
}