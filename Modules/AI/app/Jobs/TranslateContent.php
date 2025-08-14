<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{DB, Log, Cache};
use Modules\AI\App\Services\V3\{TranslationEngine, ContextAwareEngine};
use Modules\AI\App\Models\{AIFeature, AIPrompt};
use Modules\AI\App\Exceptions\FormProcessingException;
use Carbon\Carbon;
use Throwable;

/**
 * UNIVERSAL INPUT SYSTEM V3 - CONTENT TRANSLATION JOB
 * 
 * Enterprise-level background job for translating content with
 * advanced language detection, quality assessment, and batch processing.
 * 
 * Features:
 * - Multi-language translation with format preservation
 * - Quality assessment and confidence scoring
 * - Context-aware translation selection
 * - Batch processing with progress tracking
 * - Smart caching and optimization
 * - Error handling and retry mechanisms
 * 
 * @author Claude Code
 * @version 3.0
 */
class TranslateContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800; // 30 minutes timeout
    public int $tries = 3;
    public int $maxExceptions = 3;
    public array $backoff = [30, 120, 300]; // 30s, 2min, 5min

    public function __construct(
        private readonly string $translationId,
        private readonly string $sourceLanguage,
        private readonly string $targetLanguage,
        private readonly array $contentData,
        private readonly array $options = []
    ) {}

    /**
     * Execute the translation job
     */
    public function handle(
        TranslationEngine $translationEngine,
        ContextAwareEngine $contextEngine
    ): void {
        $startTime = microtime(true);

        try {
            // Initialize translation tracking
            $this->initializeTranslation();

            // Build context for translation
            $translationContext = $contextEngine->buildTranslationContext([
                'translation_id' => $this->translationId,
                'source_language' => $this->sourceLanguage,
                'target_language' => $this->targetLanguage,
                'content_type' => $this->contentData['type'] ?? 'text',
                'content_length' => $this->calculateContentLength(),
                'preserve_formatting' => $this->options['preserve_formatting'] ?? true,
                'quality_level' => $this->options['quality_level'] ?? 'high'
            ]);

            // Process translation based on content type
            $translationResult = match ($this->contentData['type'] ?? 'text') {
                'bulk_content' => $this->processBulkTranslation($translationEngine, $translationContext),
                'structured_data' => $this->processStructuredDataTranslation($translationEngine, $translationContext),
                'template_content' => $this->processTemplateTranslation($translationEngine, $translationContext),
                'feature_content' => $this->processFeatureContentTranslation($translationEngine, $translationContext),
                'prompt_content' => $this->processPromptTranslation($translationEngine, $translationContext),
                default => $this->processTextTranslation($translationEngine, $translationContext)
            };

            // Assess translation quality
            $qualityAssessment = $translationEngine->assessTranslationQuality(
                $this->contentData,
                $translationResult,
                $translationContext
            );

            // Store final results
            $this->storeTranslationResults($translationResult, $qualityAssessment);

            // Complete translation
            $this->completeTranslation($translationResult, $qualityAssessment, $startTime);

            Log::info('Translation job completed successfully', [
                'translation_id' => $this->translationId,
                'source_language' => $this->sourceLanguage,
                'target_language' => $this->targetLanguage,
                'content_type' => $this->contentData['type'] ?? 'text',
                'quality_score' => $qualityAssessment['overall_score'] ?? 0,
                'execution_time' => round(microtime(true) - $startTime, 2) . 's'
            ]);

        } catch (Throwable $e) {
            $this->handleTranslationFailure($e, $startTime);
            throw $e;
        }
    }

    /**
     * Process bulk content translation
     */
    private function processBulkTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $contentItems = $this->contentData['items'] ?? [];
        $totalItems = count($contentItems);
        $processedItems = 0;
        $results = [];
        $batchSize = $this->options['batch_size'] ?? 20;

        $batches = array_chunk($contentItems, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            try {
                // Process batch
                $batchResults = $engine->translateContentBatch($batch, $context);
                
                foreach ($batchResults as $result) {
                    $results[] = $result;
                    $processedItems++;

                    // Update progress
                    if ($processedItems % 5 === 0) {
                        $progress = ($processedItems / $totalItems) * 100;
                        $this->updateTranslationProgress($progress, "Translated {$processedItems}/{$totalItems} items");
                    }
                }

                // Memory cleanup every few batches
                if ($batchIndex % 3 === 0) {
                    gc_collect_cycles();
                }

            } catch (Throwable $e) {
                Log::error('Batch translation failed', [
                    'translation_id' => $this->translationId,
                    'batch_index' => $batchIndex,
                    'error' => $e->getMessage()
                ]);

                // Mark batch items as failed
                foreach ($batch as $item) {
                    $results[] = [
                        'success' => false,
                        'original' => $item,
                        'translated' => null,
                        'error' => $e->getMessage()
                    ];
                    $processedItems++;
                }
            }
        }

        return [
            'type' => 'bulk_translation',
            'total_items' => $totalItems,
            'processed_items' => $processedItems,
            'results' => $results,
            'success_rate' => $totalItems > 0 ? (count(array_filter($results, fn($r) => $r['success'])) / $totalItems) * 100 : 0
        ];
    }

    /**
     * Process structured data translation
     */
    private function processStructuredDataTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $structuredData = $this->contentData['structured_data'] ?? [];
        $fieldMappings = $this->contentData['field_mappings'] ?? [];

        $translationResult = $engine->translateStructuredData(
            $structuredData,
            $fieldMappings,
            $context
        );

        $this->updateTranslationProgress(50, 'Processing structured data fields');

        // Validate structure preservation
        $structureValidation = $engine->validateStructurePreservation(
            $structuredData,
            $translationResult['translated_data'],
            $fieldMappings
        );

        $this->updateTranslationProgress(80, 'Validating structure preservation');

        return [
            'type' => 'structured_translation',
            'translated_data' => $translationResult['translated_data'],
            'field_translations' => $translationResult['field_translations'],
            'structure_validation' => $structureValidation,
            'preserved_fields' => $translationResult['preserved_fields'] ?? []
        ];
    }

    /**
     * Process template content translation
     */
    private function processTemplateTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $templateContent = $this->contentData['template_content'] ?? '';
        $templateVariables = $this->contentData['template_variables'] ?? [];
        $preserveVariables = $this->options['preserve_variables'] ?? true;

        $translationResult = $engine->translateTemplateContent(
            $templateContent,
            $templateVariables,
            $context,
            $preserveVariables
        );

        $this->updateTranslationProgress(60, 'Translating template content');

        // Validate template syntax preservation
        $templateValidation = $engine->validateTemplateSyntax(
            $templateContent,
            $translationResult['translated_template'],
            $templateVariables
        );

        $this->updateTranslationProgress(90, 'Validating template syntax');

        return [
            'type' => 'template_translation',
            'translated_template' => $translationResult['translated_template'],
            'variable_mappings' => $translationResult['variable_mappings'],
            'template_validation' => $templateValidation,
            'preserved_variables' => $translationResult['preserved_variables'] ?? []
        ];
    }

    /**
     * Process AI feature content translation
     */
    private function processFeatureContentTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $featureId = $this->contentData['feature_id'] ?? null;
        $contentFields = $this->contentData['content_fields'] ?? [];

        if (!$featureId) {
            throw new FormProcessingException('Feature ID is required for feature content translation');
        }

        $feature = AIFeature::findOrFail($featureId);

        $translationResult = $engine->translateFeatureContent(
            $feature,
            $contentFields,
            $context
        );

        $this->updateTranslationProgress(70, 'Translating feature content');

        // Update feature with translated content
        if ($translationResult['success'] && ($this->options['auto_update'] ?? false)) {
            $this->updateFeatureWithTranslation($feature, $translationResult);
        }

        return [
            'type' => 'feature_translation',
            'feature_id' => $featureId,
            'translated_fields' => $translationResult['translated_fields'],
            'updated_feature' => $this->options['auto_update'] ?? false,
            'translation_metadata' => $translationResult['metadata'] ?? []
        ];
    }

    /**
     * Process prompt content translation
     */
    private function processPromptTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $promptId = $this->contentData['prompt_id'] ?? null;
        $promptText = $this->contentData['prompt_text'] ?? '';

        if ($promptId) {
            $prompt = AIPrompt::findOrFail($promptId);
            $promptText = $prompt->prompt_text;
        }

        $translationResult = $engine->translatePromptContent(
            $promptText,
            $context,
            [
                'preserve_placeholders' => $this->options['preserve_placeholders'] ?? true,
                'maintain_intent' => $this->options['maintain_intent'] ?? true
            ]
        );

        $this->updateTranslationProgress(75, 'Translating prompt content');

        // Update prompt if specified
        if ($promptId && ($this->options['auto_update'] ?? false)) {
            $prompt = AIPrompt::find($promptId);
            if ($prompt) {
                $this->updatePromptWithTranslation($prompt, $translationResult);
            }
        }

        return [
            'type' => 'prompt_translation',
            'prompt_id' => $promptId,
            'original_prompt' => $promptText,
            'translated_prompt' => $translationResult['translated_text'],
            'intent_preservation' => $translationResult['intent_analysis'] ?? [],
            'updated_prompt' => $promptId && ($this->options['auto_update'] ?? false)
        ];
    }

    /**
     * Process simple text translation
     */
    private function processTextTranslation(
        TranslationEngine $engine,
        array $context
    ): array {
        $textContent = $this->contentData['text'] ?? '';
        $formatOptions = $this->options['format_options'] ?? [];

        $translationResult = $engine->translateText(
            $textContent,
            $context,
            $formatOptions
        );

        $this->updateTranslationProgress(80, 'Translating text content');

        return [
            'type' => 'text_translation',
            'original_text' => $textContent,
            'translated_text' => $translationResult['translated_text'],
            'confidence_score' => $translationResult['confidence_score'] ?? 0,
            'detected_language' => $translationResult['detected_language'] ?? $this->sourceLanguage
        ];
    }

    /**
     * Calculate total content length
     */
    private function calculateContentLength(): int
    {
        $contentType = $this->contentData['type'] ?? 'text';

        return match ($contentType) {
            'bulk_content' => array_sum(array_map('strlen', array_column($this->contentData['items'] ?? [], 'text'))),
            'structured_data' => strlen(json_encode($this->contentData['structured_data'] ?? [])),
            'template_content' => strlen($this->contentData['template_content'] ?? ''),
            'feature_content' => array_sum(array_map('strlen', $this->contentData['content_fields'] ?? [])),
            'prompt_content' => strlen($this->contentData['prompt_text'] ?? ''),
            default => strlen($this->contentData['text'] ?? '')
        };
    }

    /**
     * Initialize translation tracking
     */
    private function initializeTranslation(): void
    {
        $translationData = [
            'translation_id' => $this->translationId,
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage,
            'content_type' => $this->contentData['type'] ?? 'text',
            'content_length' => $this->calculateContentLength(),
            'status' => 'processing',
            'progress_percentage' => 0,
            'started_at' => now(),
            'job_id' => $this->job->getJobId()
        ];

        Cache::put("translation_{$this->translationId}", $translationData, now()->addHours(2));

        // Store in database for persistence
        DB::table('ai_translation_mappings')->updateOrInsert(
            ['translation_id' => $this->translationId],
            $translationData
        );
    }

    /**
     * Update translation progress
     */
    private function updateTranslationProgress(float $progress, string $message = ''): void
    {
        $progressData = [
            'progress_percentage' => round($progress, 2),
            'progress_message' => $message,
            'updated_at' => now()
        ];

        Cache::put("translation_{$this->translationId}", array_merge(
            Cache::get("translation_{$this->translationId}", []),
            $progressData
        ), now()->addHours(2));

        DB::table('ai_translation_mappings')
            ->where('translation_id', $this->translationId)
            ->update($progressData);
    }

    /**
     * Store translation results
     */
    private function storeTranslationResults(array $translationResult, array $qualityAssessment): void
    {
        $resultData = [
            'translation_result' => json_encode($translationResult),
            'quality_assessment' => json_encode($qualityAssessment),
            'result_stored_at' => now()
        ];

        Cache::put("translation_result_{$this->translationId}", $resultData, now()->addDays(1));

        DB::table('ai_translation_mappings')
            ->where('translation_id', $this->translationId)
            ->update($resultData);
    }

    /**
     * Complete translation
     */
    private function completeTranslation(
        array $translationResult,
        array $qualityAssessment,
        float $startTime
    ): void {
        $executionTime = microtime(true) - $startTime;

        $completionData = [
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
            'execution_time' => $executionTime,
            'quality_score' => $qualityAssessment['overall_score'] ?? 0,
            'confidence_score' => $qualityAssessment['confidence_score'] ?? 0
        ];

        Cache::put("translation_{$this->translationId}", array_merge(
            Cache::get("translation_{$this->translationId}", []),
            $completionData
        ), now()->addDays(3)); // Keep completed translations longer

        DB::table('ai_translation_mappings')
            ->where('translation_id', $this->translationId)
            ->update($completionData);
    }

    /**
     * Handle translation failure
     */
    private function handleTranslationFailure(Throwable $e, float $startTime): void
    {
        $failureData = [
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'failed_at' => now(),
            'execution_time' => microtime(true) - $startTime,
            'attempt_number' => $this->attempts()
        ];

        Cache::put("translation_{$this->translationId}", array_merge(
            Cache::get("translation_{$this->translationId}", []),
            $failureData
        ), now()->addDays(1));

        DB::table('ai_translation_mappings')
            ->where('translation_id', $this->translationId)
            ->update($failureData);

        Log::error('Translation job failed', [
            'translation_id' => $this->translationId,
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage,
            'error' => $e->getMessage(),
            'attempt' => $this->attempts()
        ]);
    }

    /**
     * Update feature with translation
     */
    private function updateFeatureWithTranslation(AIFeature $feature, array $translationResult): void
    {
        $translatedFields = $translationResult['translated_fields'] ?? [];
        
        // Update translatable fields
        foreach ($translatedFields as $field => $translatedValue) {
            if (in_array($field, $feature->getFillable())) {
                $currentValue = $feature->getTranslations($field);
                $currentValue[$this->targetLanguage] = $translatedValue;
                $feature->setAttribute($field, $currentValue);
            }
        }

        $feature->save();

        Log::info('Feature updated with translation', [
            'feature_id' => $feature->id,
            'translation_id' => $this->translationId,
            'target_language' => $this->targetLanguage,
            'updated_fields' => array_keys($translatedFields)
        ]);
    }

    /**
     * Update prompt with translation
     */
    private function updatePromptWithTranslation(AIPrompt $prompt, array $translationResult): void
    {
        $translatedText = $translationResult['translated_text'] ?? '';
        
        if ($translatedText) {
            // Store as language-specific version
            $currentTranslations = $prompt->getTranslations('prompt_text');
            $currentTranslations[$this->targetLanguage] = $translatedText;
            $prompt->prompt_text = $currentTranslations;
            $prompt->save();

            Log::info('Prompt updated with translation', [
                'prompt_id' => $prompt->id,
                'translation_id' => $this->translationId,
                'target_language' => $this->targetLanguage
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(Throwable $exception): void
    {
        $this->handleTranslationFailure($exception, 0);

        // Notify administrators or trigger alerts
        Log::critical('Translation job failed permanently', [
            'translation_id' => $this->translationId,
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage,
            'error' => $exception->getMessage(),
            'attempts' => $this->tries
        ]);
    }
}