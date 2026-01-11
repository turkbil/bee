<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{DB, Log, Cache};
use Modules\AI\App\Services\V3\{TemplateGenerator, ContextAwareEngine};
use Modules\AI\App\Models\{AIFeature, AIPrompt};
use Modules\AI\App\Exceptions\FormProcessingException;
use Carbon\Carbon;
use Throwable;

/**
 * UNIVERSAL INPUT SYSTEM V3 - TEMPLATE GENERATION JOB
 * 
 * Enterprise-level background job for generating content from templates
 * with dynamic variable substitution, inheritance, and optimization.
 * 
 * Features:
 * - Dynamic template generation with inheritance
 * - Multi-language template variants  
 * - Real-time template optimization
 * - Variable substitution with context awareness
 * - Template validation and syntax checking
 * - Performance metrics and caching
 * 
 * @author Claude Code
 * @version 3.0
 */
class GenerateFromTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200; // 20 minutes timeout
    public int $tries = 3;
    public int $maxExceptions = 3;
    public array $backoff = [60, 180, 300]; // 1min, 3min, 5min

    public function __construct(
        private readonly string $generationId,
        private readonly string $templateId,
        private readonly array $templateData,
        private readonly array $variables = [],
        private readonly array $options = []
    ) {}

    /**
     * Execute the template generation job
     */
    public function handle(
        TemplateGenerator $templateGenerator,
        ContextAwareEngine $contextEngine
    ): void {
        $startTime = microtime(true);

        try {
            // Initialize generation tracking
            $this->initializeGeneration();

            // Build context for template generation
            $generationContext = $contextEngine->buildTemplateGenerationContext([
                'generation_id' => $this->generationId,
                'template_id' => $this->templateId,
                'template_type' => $this->templateData['type'] ?? 'content',
                'variable_count' => count($this->variables),
                'target_language' => $this->options['target_language'] ?? 'tr',
                'optimization_level' => $this->options['optimization_level'] ?? 'standard',
                'inheritance_enabled' => $this->options['inheritance_enabled'] ?? true
            ]);

            // Process generation based on template type
            $generationResult = match ($this->templateData['type'] ?? 'content') {
                'feature_template' => $this->generateFeatureTemplate($templateGenerator, $generationContext),
                'prompt_template' => $this->generatePromptTemplate($templateGenerator, $generationContext),
                'bulk_template' => $this->generateBulkTemplates($templateGenerator, $generationContext),
                'dynamic_template' => $this->generateDynamicTemplate($templateGenerator, $generationContext),
                'multi_language_template' => $this->generateMultiLanguageTemplate($templateGenerator, $generationContext),
                'inherited_template' => $this->generateInheritedTemplate($templateGenerator, $generationContext),
                default => $this->generateContentTemplate($templateGenerator, $generationContext)
            };

            // Validate generated content
            $validationResult = $templateGenerator->validateGeneratedContent(
                $generationResult,
                $this->templateData,
                $generationContext
            );

            // Optimize if requested
            if ($this->options['auto_optimize'] ?? false) {
                $generationResult = $this->optimizeGeneratedContent(
                    $templateGenerator,
                    $generationResult,
                    $generationContext
                );
            }

            // Store final results
            $this->storeGenerationResults($generationResult, $validationResult);

            // Complete generation
            $this->completeGeneration($generationResult, $validationResult, $startTime);

            Log::info('Template generation job completed successfully', [
                'generation_id' => $this->generationId,
                'template_id' => $this->templateId,
                'template_type' => $this->templateData['type'] ?? 'content',
                'variables_count' => count($this->variables),
                'validation_score' => $validationResult['overall_score'] ?? 0,
                'execution_time' => round(microtime(true) - $startTime, 2) . 's'
            ]);

        } catch (Throwable $e) {
            $this->handleGenerationFailure($e, $startTime);
            throw $e;
        }
    }

    /**
     * Generate AI feature template
     */
    private function generateFeatureTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $featureId = $this->templateData['feature_id'] ?? null;
        $templateVariables = $this->variables;

        if (!$featureId) {
            throw new FormProcessingException('Feature ID is required for feature template generation');
        }

        $feature = AIFeature::findOrFail($featureId);

        $this->updateGenerationProgress(20, 'Loading feature template');

        $generationResult = $generator->generateFeatureBasedTemplate(
            $feature,
            $this->templateData,
            $templateVariables,
            $context
        );

        $this->updateGenerationProgress(70, 'Applying feature-specific optimizations');

        // Apply feature-specific optimizations
        if ($this->options['feature_optimization'] ?? true) {
            $generationResult = $generator->applyFeatureOptimizations(
                $generationResult,
                $feature,
                $context
            );
        }

        $this->updateGenerationProgress(90, 'Finalizing feature template');

        return [
            'type' => 'feature_template',
            'feature_id' => $featureId,
            'generated_content' => $generationResult['content'],
            'applied_variables' => $generationResult['variables_applied'],
            'feature_metadata' => $generationResult['feature_metadata'] ?? [],
            'optimization_applied' => $this->options['feature_optimization'] ?? true
        ];
    }

    /**
     * Generate prompt template
     */
    private function generatePromptTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $promptId = $this->templateData['prompt_id'] ?? null;
        $templateContent = $this->templateData['template_content'] ?? '';

        if ($promptId) {
            $prompt = AIPrompt::findOrFail($promptId);
            $templateContent = $prompt->prompt_text;
        }

        $this->updateGenerationProgress(25, 'Processing prompt template');

        $generationResult = $generator->generatePromptBasedTemplate(
            $templateContent,
            $this->variables,
            $context,
            [
                'preserve_intent' => $this->options['preserve_intent'] ?? true,
                'enhance_clarity' => $this->options['enhance_clarity'] ?? true
            ]
        );

        $this->updateGenerationProgress(80, 'Validating prompt structure');

        // Validate prompt structure
        $promptValidation = $generator->validatePromptStructure(
            $generationResult['content'],
            $templateContent,
            $context
        );

        return [
            'type' => 'prompt_template',
            'prompt_id' => $promptId,
            'original_template' => $templateContent,
            'generated_content' => $generationResult['content'],
            'variable_mapping' => $generationResult['variable_mapping'],
            'prompt_validation' => $promptValidation,
            'intent_preserved' => $generationResult['intent_analysis'] ?? true
        ];
    }

    /**
     * Generate bulk templates
     */
    private function generateBulkTemplates(
        TemplateGenerator $generator,
        array $context
    ): array {
        $templateConfigs = $this->templateData['template_configs'] ?? [];
        $sharedVariables = $this->variables;
        $totalTemplates = count($templateConfigs);
        $processedTemplates = 0;
        $results = [];

        $this->updateGenerationProgress(10, 'Starting bulk template generation');

        foreach ($templateConfigs as $templateConfig) {
            try {
                // Merge shared and template-specific variables
                $templateVariables = array_merge(
                    $sharedVariables,
                    $templateConfig['variables'] ?? []
                );

                $templateResult = $generator->generateSingleTemplate(
                    $templateConfig,
                    $templateVariables,
                    $context
                );

                $results[] = [
                    'success' => true,
                    'template_config' => $templateConfig,
                    'generated_content' => $templateResult['content'],
                    'variables_applied' => $templateResult['variables_applied']
                ];

                $processedTemplates++;

                // Update progress
                if ($processedTemplates % 5 === 0) {
                    $progress = 20 + (($processedTemplates / $totalTemplates) * 70);
                    $this->updateGenerationProgress($progress, "Generated {$processedTemplates}/{$totalTemplates} templates");
                }

            } catch (Throwable $e) {
                Log::error('Individual template generation failed', [
                    'generation_id' => $this->generationId,
                    'template_config' => $templateConfig,
                    'error' => $e->getMessage()
                ]);

                $results[] = [
                    'success' => false,
                    'template_config' => $templateConfig,
                    'error' => $e->getMessage()
                ];

                $processedTemplates++;
            }
        }

        return [
            'type' => 'bulk_templates',
            'total_templates' => $totalTemplates,
            'processed_templates' => $processedTemplates,
            'results' => $results,
            'success_rate' => $totalTemplates > 0 ? (count(array_filter($results, fn($r) => $r['success'])) / $totalTemplates) * 100 : 0
        ];
    }

    /**
     * Generate dynamic template
     */
    private function generateDynamicTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $dynamicRules = $this->templateData['dynamic_rules'] ?? [];
        $baseTemplate = $this->templateData['base_template'] ?? '';

        $this->updateGenerationProgress(30, 'Processing dynamic rules');

        $generationResult = $generator->generateDynamicTemplate(
            $baseTemplate,
            $dynamicRules,
            $this->variables,
            $context
        );

        $this->updateGenerationProgress(75, 'Applying dynamic optimizations');

        // Apply dynamic optimizations based on rules
        $optimizedResult = $generator->applyDynamicOptimizations(
            $generationResult,
            $dynamicRules,
            $context
        );

        return [
            'type' => 'dynamic_template',
            'base_template' => $baseTemplate,
            'dynamic_rules' => $dynamicRules,
            'generated_content' => $optimizedResult['content'],
            'applied_rules' => $optimizedResult['rules_applied'],
            'optimization_score' => $optimizedResult['optimization_score'] ?? 0
        ];
    }

    /**
     * Generate multi-language template
     */
    private function generateMultiLanguageTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $targetLanguages = $this->templateData['target_languages'] ?? ['tr', 'en'];
        $baseTemplate = $this->templateData['base_template'] ?? '';
        $languageVariants = [];

        $totalLanguages = count($targetLanguages);
        $processedLanguages = 0;

        $this->updateGenerationProgress(15, 'Starting multi-language generation');

        foreach ($targetLanguages as $language) {
            try {
                $languageContext = array_merge($context, [
                    'target_language' => $language,
                    'language_specific' => true
                ]);

                $languageResult = $generator->generateLanguageVariant(
                    $baseTemplate,
                    $language,
                    $this->variables,
                    $languageContext
                );

                $languageVariants[$language] = [
                    'success' => true,
                    'content' => $languageResult['content'],
                    'localization_score' => $languageResult['localization_score'] ?? 0
                ];

                $processedLanguages++;

                $progress = 20 + (($processedLanguages / $totalLanguages) * 60);
                $this->updateGenerationProgress($progress, "Generated {$processedLanguages}/{$totalLanguages} language variants");

            } catch (Throwable $e) {
                Log::error('Language variant generation failed', [
                    'generation_id' => $this->generationId,
                    'language' => $language,
                    'error' => $e->getMessage()
                ]);

                $languageVariants[$language] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];

                $processedLanguages++;
            }
        }

        return [
            'type' => 'multi_language_template',
            'base_template' => $baseTemplate,
            'target_languages' => $targetLanguages,
            'language_variants' => $languageVariants,
            'success_rate' => $totalLanguages > 0 ? (count(array_filter($languageVariants, fn($v) => $v['success'])) / $totalLanguages) * 100 : 0
        ];
    }

    /**
     * Generate inherited template
     */
    private function generateInheritedTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $parentTemplateId = $this->templateData['parent_template_id'] ?? null;
        $inheritanceRules = $this->templateData['inheritance_rules'] ?? [];

        if (!$parentTemplateId) {
            throw new FormProcessingException('Parent template ID is required for inherited template generation');
        }

        $this->updateGenerationProgress(25, 'Loading parent template');

        $inheritanceResult = $generator->generateInheritedTemplate(
            $parentTemplateId,
            $this->templateData,
            $inheritanceRules,
            $this->variables,
            $context
        );

        $this->updateGenerationProgress(85, 'Applying inheritance optimizations');

        return [
            'type' => 'inherited_template',
            'parent_template_id' => $parentTemplateId,
            'inheritance_rules' => $inheritanceRules,
            'generated_content' => $inheritanceResult['content'],
            'inherited_elements' => $inheritanceResult['inherited_elements'],
            'overridden_elements' => $inheritanceResult['overridden_elements'],
            'inheritance_depth' => $inheritanceResult['inheritance_depth'] ?? 1
        ];
    }

    /**
     * Generate standard content template
     */
    private function generateContentTemplate(
        TemplateGenerator $generator,
        array $context
    ): array {
        $templateContent = $this->templateData['template_content'] ?? '';
        $templateType = $this->templateData['content_type'] ?? 'general';

        $this->updateGenerationProgress(40, 'Processing content template');

        $generationResult = $generator->generateContentTemplate(
            $templateContent,
            $templateType,
            $this->variables,
            $context
        );

        $this->updateGenerationProgress(85, 'Finalizing content generation');

        return [
            'type' => 'content_template',
            'template_content' => $templateContent,
            'content_type' => $templateType,
            'generated_content' => $generationResult['content'],
            'variable_substitutions' => $generationResult['substitutions'],
            'generation_metadata' => $generationResult['metadata'] ?? []
        ];
    }

    /**
     * Optimize generated content
     */
    private function optimizeGeneratedContent(
        TemplateGenerator $generator,
        array $generationResult,
        array $context
    ): array {
        $this->updateGenerationProgress(92, 'Optimizing generated content');

        $optimizationOptions = [
            'readability' => $this->options['optimize_readability'] ?? true,
            'seo' => $this->options['optimize_seo'] ?? false,
            'length' => $this->options['optimize_length'] ?? false,
            'engagement' => $this->options['optimize_engagement'] ?? true
        ];

        $optimizedResult = $generator->optimizeGeneratedContent(
            $generationResult,
            $optimizationOptions,
            $context
        );

        return array_merge($generationResult, [
            'optimization_applied' => true,
            'optimization_score' => $optimizedResult['optimization_score'] ?? 0,
            'optimizations' => $optimizedResult['applied_optimizations'] ?? []
        ]);
    }

    /**
     * Initialize generation tracking
     */
    private function initializeGeneration(): void
    {
        $generationData = [
            'generation_id' => $this->generationId,
            'template_id' => $this->templateId,
            'template_type' => $this->templateData['type'] ?? 'content',
            'variable_count' => count($this->variables),
            'status' => 'processing',
            'progress_percentage' => 0,
            'started_at' => now(),
            'job_id' => $this->job->getJobId()
        ];

        Cache::put("template_generation_{$this->generationId}", $generationData, now()->addHours(2));

        // Store in database for persistence
        DB::table('ai_prompt_templates')->updateOrInsert(
            ['generation_id' => $this->generationId],
            array_merge($generationData, ['created_at' => now(), 'updated_at' => now()])
        );
    }

    /**
     * Update generation progress
     */
    private function updateGenerationProgress(float $progress, string $message = ''): void
    {
        $progressData = [
            'progress_percentage' => round($progress, 2),
            'progress_message' => $message,
            'updated_at' => now()
        ];

        Cache::put("template_generation_{$this->generationId}", array_merge(
            Cache::get("template_generation_{$this->generationId}", []),
            $progressData
        ), now()->addHours(2));

        DB::table('ai_prompt_templates')
            ->where('generation_id', $this->generationId)
            ->update($progressData);
    }

    /**
     * Store generation results
     */
    private function storeGenerationResults(array $generationResult, array $validationResult): void
    {
        $resultData = [
            'generation_result' => json_encode($generationResult),
            'validation_result' => json_encode($validationResult),
            'result_stored_at' => now()
        ];

        Cache::put("generation_result_{$this->generationId}", $resultData, now()->addDays(1));

        DB::table('ai_prompt_templates')
            ->where('generation_id', $this->generationId)
            ->update($resultData);
    }

    /**
     * Complete generation
     */
    private function completeGeneration(
        array $generationResult,
        array $validationResult,
        float $startTime
    ): void {
        $executionTime = microtime(true) - $startTime;

        $completionData = [
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
            'execution_time' => $executionTime,
            'validation_score' => $validationResult['overall_score'] ?? 0,
            'content_length' => strlen($generationResult['generated_content'] ?? '')
        ];

        Cache::put("template_generation_{$this->generationId}", array_merge(
            Cache::get("template_generation_{$this->generationId}", []),
            $completionData
        ), now()->addDays(3)); // Keep completed generations longer

        DB::table('ai_prompt_templates')
            ->where('generation_id', $this->generationId)
            ->update($completionData);
    }

    /**
     * Handle generation failure
     */
    private function handleGenerationFailure(Throwable $e, float $startTime): void
    {
        $failureData = [
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'failed_at' => now(),
            'execution_time' => microtime(true) - $startTime,
            'attempt_number' => $this->attempts()
        ];

        Cache::put("template_generation_{$this->generationId}", array_merge(
            Cache::get("template_generation_{$this->generationId}", []),
            $failureData
        ), now()->addDays(1));

        DB::table('ai_prompt_templates')
            ->where('generation_id', $this->generationId)
            ->update($failureData);

        Log::error('Template generation job failed', [
            'generation_id' => $this->generationId,
            'template_id' => $this->templateId,
            'template_type' => $this->templateData['type'] ?? 'content',
            'error' => $e->getMessage(),
            'attempt' => $this->attempts()
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(Throwable $exception): void
    {
        $this->handleGenerationFailure($exception, 0);

        // Notify administrators or trigger alerts
        Log::critical('Template generation job failed permanently', [
            'generation_id' => $this->generationId,
            'template_id' => $this->templateId,
            'template_type' => $this->templateData['type'] ?? 'content',
            'error' => $exception->getMessage(),
            'attempts' => $this->tries
        ]);
    }
}