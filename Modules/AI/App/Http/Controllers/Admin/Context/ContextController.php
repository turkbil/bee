<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Context;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\Universal\ContextAwareEngine;
use Modules\AI\App\Services\Universal\SmartAnalyzer;
use Modules\AI\App\Services\Universal\UniversalInputManagerV3;

/**
 * Enterprise Context & Rules Management Controller
 * 
 * Handles context-aware processing, smart rule management,
 * and intelligent context extraction for Universal Input System V3 Professional
 * 
 * @version 3.0.0 Professional
 * @since 2025-08-10
 */
class ContextController extends Controller
{
    public function __construct(
        private readonly ContextAwareEngine $contextEngine,
        private readonly SmartAnalyzer $smartAnalyzer,
        private readonly UniversalInputManagerV3 $inputManager
    ) {}

    /**
     * Get context data for specific feature and user scenario
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getContext(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_id' => 'required|integer|exists:ai_features,id',
                'context_types' => 'nullable|array',
                'context_types.*' => 'string|in:user,tenant,module,historical,behavioral,temporal',
                'depth_level' => 'nullable|string|in:basic,enhanced,deep,comprehensive',
                'include_history' => 'nullable|boolean',
                'time_range' => 'nullable|string|in:1h,24h,7d,30d'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contextData = $this->contextEngine->extractRelevantContext(
                userId: auth()->id(),
                featureId: $request->get('feature_id'),
                contextTypes: $request->get('context_types', ['user', 'tenant', 'module']),
                depthLevel: $request->get('depth_level', 'enhanced'),
                includeHistory: $request->get('include_history', true),
                timeRange: $request->get('time_range', '7d')
            );

            // Analyze context quality and completeness
            $contextAnalysis = $this->smartAnalyzer->analyzeContextQuality($contextData);

            Log::info('Context data retrieved successfully', [
                'feature_id' => $request->get('feature_id'),
                'user_id' => auth()->id(),
                'context_types' => $request->get('context_types', []),
                'context_score' => $contextAnalysis['quality_score'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Context data retrieved successfully',
                'data' => [
                    'context' => $contextData,
                    'analysis' => $contextAnalysis,
                    'meta' => [
                        'extraction_time' => $contextAnalysis['extraction_time'] ?? 0,
                        'data_sources' => $contextData['data_sources'] ?? [],
                        'confidence_level' => $contextAnalysis['confidence_level'] ?? 'medium'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve context data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve context data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get and manage smart processing rules
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getRules(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'rule_type' => 'nullable|string|in:validation,processing,optimization,security',
                'feature_id' => 'nullable|integer|exists:ai_features,id',
                'module_name' => 'nullable|string|max:100',
                'active_only' => 'nullable|boolean',
                'include_system_rules' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rules = $this->contextEngine->getProcessingRules(
                ruleType: $request->get('rule_type'),
                featureId: $request->get('feature_id'),
                moduleName: $request->get('module_name'),
                activeOnly: $request->get('active_only', true),
                includeSystemRules: $request->get('include_system_rules', false)
            );

            $ruleAnalytics = $this->smartAnalyzer->analyzeRuleEffectiveness($rules);

            Log::info('Processing rules retrieved successfully', [
                'rule_type' => $request->get('rule_type'),
                'feature_id' => $request->get('feature_id'),
                'rule_count' => count($rules),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Processing rules retrieved successfully',
                'data' => [
                    'rules' => $rules,
                    'analytics' => $ruleAnalytics,
                    'summary' => [
                        'total_rules' => count($rules),
                        'active_rules' => count(array_filter($rules, fn($rule) => $rule['is_active'] ?? false)),
                        'rule_types' => array_unique(array_column($rules, 'type')),
                        'effectiveness_score' => $ruleAnalytics['overall_effectiveness'] ?? 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve processing rules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve processing rules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update or create processing rules
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRules(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'rules' => 'required|array|min:1',
                'rules.*.id' => 'nullable|integer',
                'rules.*.name' => 'required|string|max:255',
                'rules.*.type' => 'required|string|in:validation,processing,optimization,security',
                'rules.*.conditions' => 'required|array',
                'rules.*.actions' => 'required|array',
                'rules.*.priority' => 'nullable|integer|min:1|max:100',
                'rules.*.is_active' => 'nullable|boolean',
                'rules.*.feature_id' => 'nullable|integer|exists:ai_features,id',
                'validate_rules' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rules = $request->get('rules');
            $validateRules = $request->get('validate_rules', true);

            // Validate rule logic if requested
            if ($validateRules) {
                $validationResults = $this->contextEngine->validateRuleLogic($rules);
                
                if (!$validationResults['is_valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Rule validation failed',
                        'errors' => $validationResults['errors']
                    ], 422);
                }
            }

            $updatedRules = $this->contextEngine->updateProcessingRules(
                rules: $rules,
                userId: auth()->id()
            );

            Log::info('Processing rules updated successfully', [
                'rule_count' => count($updatedRules),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Processing rules updated successfully',
                'data' => [
                    'updated_rules' => $updatedRules,
                    'summary' => [
                        'total_updated' => count($updatedRules),
                        'new_rules' => count(array_filter($updatedRules, fn($rule) => !isset($rule['original_id']))),
                        'modified_rules' => count(array_filter($updatedRules, fn($rule) => isset($rule['original_id'])))
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update processing rules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update processing rules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze context patterns and generate insights
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function analyzePatterns(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'analysis_period' => 'nullable|string|in:7d,30d,90d',
                'pattern_types' => 'nullable|array',
                'pattern_types.*' => 'string|in:usage,behavioral,temporal,contextual,performance',
                'feature_ids' => 'nullable|array',
                'feature_ids.*' => 'integer|exists:ai_features,id',
                'min_confidence' => 'nullable|numeric|min:0|max:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patternAnalysis = $this->smartAnalyzer->analyzeContextPatterns(
                analysisPeriod: $request->get('analysis_period', '30d'),
                patternTypes: $request->get('pattern_types', ['usage', 'behavioral', 'contextual']),
                featureIds: $request->get('feature_ids', []),
                minConfidence: $request->get('min_confidence', 0.7),
                userId: auth()->id()
            );

            // Generate actionable insights
            $insights = $this->contextEngine->generatePatternInsights($patternAnalysis);

            // Get recommendations based on patterns
            $recommendations = $this->smartAnalyzer->generateContextRecommendations($patternAnalysis);

            Log::info('Context patterns analyzed successfully', [
                'analysis_period' => $request->get('analysis_period', '30d'),
                'pattern_count' => count($patternAnalysis['patterns'] ?? []),
                'insight_count' => count($insights),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Context patterns analyzed successfully',
                'data' => [
                    'pattern_analysis' => $patternAnalysis,
                    'insights' => $insights,
                    'recommendations' => $recommendations,
                    'summary' => [
                        'total_patterns' => count($patternAnalysis['patterns'] ?? []),
                        'high_confidence_patterns' => count(array_filter(
                            $patternAnalysis['patterns'] ?? [], 
                            fn($p) => ($p['confidence'] ?? 0) >= 0.8
                        )),
                        'actionable_insights' => count($insights),
                        'analysis_period' => $request->get('analysis_period', '30d')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to analyze context patterns', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze context patterns',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize context extraction settings
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function optimizeSettings(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'optimization_goals' => 'nullable|array',
                'optimization_goals.*' => 'string|in:performance,accuracy,cost,user_experience',
                'feature_ids' => 'nullable|array',
                'feature_ids.*' => 'integer|exists:ai_features,id',
                'apply_optimizations' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $optimizationResults = $this->contextEngine->optimizeContextSettings(
                optimizationGoals: $request->get('optimization_goals', ['performance', 'accuracy']),
                featureIds: $request->get('feature_ids', []),
                userId: auth()->id()
            );

            $applyOptimizations = $request->get('apply_optimizations', false);
            
            if ($applyOptimizations) {
                $appliedChanges = $this->contextEngine->applyOptimizations(
                    $optimizationResults['recommendations'],
                    auth()->id()
                );
                
                $optimizationResults['applied_changes'] = $appliedChanges;
            }

            Log::info('Context settings optimization completed', [
                'optimization_goals' => $request->get('optimization_goals', []),
                'feature_count' => count($request->get('feature_ids', [])),
                'optimizations_applied' => $applyOptimizations,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Context settings optimization completed',
                'data' => [
                    'optimization_results' => $optimizationResults,
                    'impact_analysis' => $optimizationResults['impact_analysis'] ?? [],
                    'next_steps' => $optimizationResults['next_steps'] ?? []
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to optimize context settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize context settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}